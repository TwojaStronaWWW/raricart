/**
 * Automatyczna synchronizacja współbieżnych przestrzeni roboczych Git Worktree
 * (E:\Projekty\raricart oraz E:\Projekty\_workspaces\raricart-ws1..3).
 *
 * Zapobiega sytuacji, w której zatwierdzenie (commit) w jednym workspace'ie
 * pozostawia pozostałe worktree w tyle lub nadpisuje cudze zmiany.
 */
import fs from 'node:fs';
import path from 'node:path';
import { execFileSync } from 'node:child_process';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const ROOT_DIR = path.resolve(__dirname, '..');

/**
 * Parsuje wyjście `git worktree list --porcelain` do tablicy obiektów.
 * @param {string} rawOutput
 * @returns {Array<{path: string, head: string, branch: string | null}>}
 */
export function parseWorktreeListPorcelain(rawOutput) {
  if (!rawOutput || typeof rawOutput !== 'string') return [];
  const blocks = rawOutput.trim().split(/\r?\n\r?\n/);
  const result = [];

  for (const block of blocks) {
    const lines = block.trim().split(/\r?\n/);
    let wtPath = '';
    let head = '';
    let branch = null;

    for (const line of lines) {
      if (line.startsWith('worktree ')) {
        wtPath = line.slice('worktree '.length).trim();
      } else if (line.startsWith('HEAD ')) {
        head = line.slice('HEAD '.length).trim();
      } else if (line.startsWith('branch ')) {
        const ref = line.slice('branch '.length).trim();
        branch = ref.replace(/^refs\/heads\//, '');
      }
    }

    if (wtPath) {
      result.push({ path: path.resolve(wtPath), head, branch });
    }
  }

  return result;
}

const GIT_HOOK_ENV_VARS = [
  'GIT_DIR',
  'GIT_WORK_TREE',
  'GIT_INDEX_FILE',
  'GIT_PREFIX',
  'GIT_COMMON_DIR',
  'GIT_OBJECT_DIRECTORY',
  'GIT_ALTERNATE_OBJECT_DIRECTORIES'
];

/**
 * Tworzy czyste środowisko dla wywołań git w sąsiednich worktree,
 * usuwając zmienne GIT_DIR / GIT_INDEX_FILE wstrzykiwane przez hooki gita (np. post-commit).
 * @param {NodeJS.ProcessEnv} [baseEnv]
 * @returns {NodeJS.ProcessEnv}
 */
export function buildCleanGitEnv(baseEnv = process.env) {
  const env = { ...baseEnv, RARICART_SYNC_IN_PROGRESS: '1' };
  for (const key of GIT_HOOK_ENV_VARS) {
    delete env[key];
  }
  return env;
}

function runGit(args, cwd = ROOT_DIR) {
  return execFileSync('git', ['-C', cwd, ...args], {
    cwd,
    env: buildCleanGitEnv(process.env),
    encoding: 'utf-8',
    stdio: ['ignore', 'pipe', 'pipe']
  }).trim();
}

function isAncestor(ancestorRef, descendantRef, cwd = ROOT_DIR) {
  try {
    execFileSync('git', ['-C', cwd, 'merge-base', '--is-ancestor', ancestorRef, descendantRef], {
      cwd,
      env: buildCleanGitEnv(process.env),
      stdio: 'ignore'
    });
    return true;
  } catch {
    return false;
  }
}

function getWorktreeDirtyTracked(wtPath) {
  try {
    return runGit(['status', '--porcelain', '--untracked-files=no'], wtPath);
  } catch {
    return '';
  }
}

function getWorktreeStatusPorcelain(wtPath) {
  try {
    return runGit(['status', '--porcelain'], wtPath);
  } catch {
    return 'ERROR';
  }
}

export function syncAllWorktrees(options = {}) {
  if (process.env.RARICART_SYNC_IN_PROGRESS === '1') {
    return { synced: [], mergedFrom: [] };
  }
  process.env.RARICART_SYNC_IN_PROGRESS = '1';

  const {
    preDeploy = false,
    postDeploy = false,
    postCommit = false
  } = options;

  let rawWt = '';
  try {
    rawWt = runGit(['worktree', 'list', '--porcelain'], ROOT_DIR);
  } catch {
    return { synced: [], mergedFrom: [] };
  }

  const worktrees = parseWorktreeListPorcelain(rawWt);
  if (worktrees.length <= 1) {
    return { synced: [], mergedFrom: [] };
  }

  const currentPath = path.resolve(ROOT_DIR);
  const siblings = worktrees.filter((w) => w.path.toLowerCase() !== currentPath.toLowerCase());
  const mergedFrom = [];
  const synced = [];

  for (const wt of siblings) {
    if (!fs.existsSync(wt.path)) continue;

    const dirtyTracked = getWorktreeDirtyTracked(wt.path);
    if (dirtyTracked && (preDeploy || postCommit || !options.skipAutoSave)) {
      console.log(`⚠️  [sync-worktrees] Wykryto niezapisane zmiany w ${wt.path} (${wt.branch || 'detached'}). Automatyczny zapis przed integracją...`);
      try {
        runGit(['add', '-u'], wt.path);
        execFileSync(
          'git',
          ['-C', wt.path, 'commit', '--no-verify', '-m', `chore(sync): auto-save pending changes from ${wt.branch || path.basename(wt.path)}`],
          { cwd: wt.path, stdio: 'ignore', env: buildCleanGitEnv(process.env) }
        );
        wt.head = runGit(['rev-parse', 'HEAD'], wt.path);
      } catch (err) {
        if (preDeploy) {
          console.error(`❌ [sync-worktrees] Nie udało się automatycznie zapisać zmian w ${wt.path}: ${err.message}`);
          process.exit(1);
        }
      }
    }

    const refToCheck = wt.branch || wt.head;
    if (!refToCheck) continue;

    const currentHead = runGit(['rev-parse', 'HEAD'], ROOT_DIR);
    if (wt.head && wt.head !== currentHead && !isAncestor(wt.head, currentHead, ROOT_DIR)) {
      if (isAncestor(currentHead, wt.head, ROOT_DIR)) {
        console.log(`🔄 [sync-worktrees] Przesuwanie (fast-forward) bieżącego workspace'u do nowszego stanu z ${wt.branch || wt.path}...`);
        runGit(['merge', '--ff-only', wt.head], ROOT_DIR);
        mergedFrom.push(wt.branch || wt.path);
      } else {
        console.log(`🔀 [sync-worktrees] Scalanie równoległych zmian z ${wt.branch || wt.path} do bieżącego workspace'u...`);
        try {
          runGit(
            ['merge', '--no-verify', '-m', `chore(sync): merge ${wt.branch || path.basename(wt.path)} into current worktree`, wt.head],
            ROOT_DIR
          );
          mergedFrom.push(wt.branch || wt.path);
        } catch {
          try {
            runGit(['merge', '--abort'], ROOT_DIR);
          } catch {
            // ignore
          }
          if (preDeploy) {
            console.error(`❌ [sync-worktrees] Konflikt scalania z ${wt.branch || wt.path}! Rozwiąż konflikt przed wdrożeniem.`);
            process.exit(1);
          }
        }
      }
    }
  }

  const unifiedHead = runGit(['rev-parse', 'HEAD'], ROOT_DIR);
  for (const wt of siblings) {
    if (!fs.existsSync(wt.path)) continue;
    const status = getWorktreeStatusPorcelain(wt.path);
    if (status !== '') {
      continue;
    }
    const wtHead = runGit(['rev-parse', 'HEAD'], wt.path);
    if (wtHead !== unifiedHead && isAncestor(wtHead, unifiedHead, ROOT_DIR)) {
      try {
        runGit(['merge', '--ff-only', unifiedHead], wt.path);
        synced.push(wt.branch || path.basename(wt.path));
        console.log(`✅ [sync-worktrees] Zsynchronizowano ${wt.branch || path.basename(wt.path)} (${wt.path}) -> ${unifiedHead.slice(0, 7)}`);
      } catch {
        // ignore if locked
      }
    }
  }

  if (postDeploy) {
    try {
      console.log('🚀 [sync-worktrees] Aktualizacja zdalnego repozytorium origin/main...');
      runGit(['push', 'origin', `${unifiedHead}:refs/heads/main`], ROOT_DIR);
      console.log(`✅ [sync-worktrees] Zdalna gałąź origin/main zaktualizowana do ${unifiedHead.slice(0, 7)}.`);
    } catch (pushErr) {
      console.warn(`⚠️  [sync-worktrees] Nie udało się wypchnąć do origin/main: ${pushErr.message}`);
    }
  }

  return { synced, mergedFrom, unifiedHead };
}

const isMain = process.argv[1] && path.resolve(process.argv[1]) === __filename;
if (isMain) {
  const args = new Set(process.argv.slice(2));
  syncAllWorktrees({
    preDeploy: args.has('--pre-deploy'),
    postDeploy: args.has('--post-deploy'),
    postCommit: args.has('--post-commit')
  });
}
