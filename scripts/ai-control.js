#!/usr/bin/env node
/**
 * TwojaStronaWWW Enterprise — AI Agent Feature Toggle CLI (scripts/ai-control.js)
 *
 * Pozwala jednym poleceniem włączyć, wyłączyć lub sprawdzić status Asystenta AI
 * w dowolnym projekcie opartym na _site-template (np. gdy klient dokupuje moduł AI RAG
 * lub startuje od pakietu podstawowego bez czatbota).
 *
 * Użycie:
 *   npm run ai:status
 *   npm run ai:enable
 *   npm run ai:disable
 */

import fs from 'node:fs';
import path from 'node:path';
import crypto from 'node:crypto';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const PROJECT_ROOT = path.resolve(__dirname, '..');

function writeFileAtomically(filePath, content) {
  const dir = path.dirname(filePath);
  fs.mkdirSync(dir, { recursive: true });
  const rand = crypto.randomBytes(4).toString('hex');
  const tmpPath = `${filePath}.${process.pid}.${Date.now()}.${rand}.tmp`;
  try {
    fs.writeFileSync(tmpPath, content, 'utf8');
    fs.renameSync(tmpPath, filePath);
  } catch (err) {
    try {
      if (fs.existsSync(tmpPath)) fs.unlinkSync(tmpPath);
    } catch (_) {}
    throw err;
  }
}

export function setAiWidgetState(projectRoot = PROJECT_ROOT, enabled = true) {
  const configPath = path.join(projectRoot, 'assets/data/ai-config.json');
  let config = {
    enabled: Boolean(enabled),
    siteId: 'twojastronawww',
    title: 'Asystent AI'
  };

  if (fs.existsSync(configPath)) {
    try {
      config = JSON.parse(fs.readFileSync(configPath, 'utf8'));
    } catch (_) {}
  }

  config.enabled = Boolean(enabled);
  writeFileAtomically(configPath, JSON.stringify(config, null, 2) + '\n');

  // Zaktualizuj atrybut data-enabled w plikach widoku (index.html lub parts/footer.php)
  const candidateViews = [
    path.join(projectRoot, 'index.html'),
    path.join(projectRoot, 'parts/footer.php'),
    path.join(projectRoot, 'index.php')
  ];

  const updatedViews = [];
  for (const viewPath of candidateViews) {
    if (!fs.existsSync(viewPath)) continue;
    const html = fs.readFileSync(viewPath, 'utf8');
    if (!html.includes('ai-widget.js')) continue;

    let nextHtml = html;
    if (/data-enabled=["'](true|false)["']/.test(nextHtml)) {
      nextHtml = nextHtml.replace(
        /(<script[^>]*ai-widget\.js[^>]*?)data-enabled=["'](true|false)["']/i,
        `$1data-enabled="${Boolean(enabled)}"`
      );
    } else {
      nextHtml = nextHtml.replace(
        /(<script[^>]*src=["'][^"']*ai-widget\.js[^"']*["'])/i,
        `$1 data-enabled="${Boolean(enabled)}"`
      );
    }

    if (nextHtml !== html) {
      writeFileAtomically(viewPath, nextHtml);
      updatedViews.push(path.relative(projectRoot, viewPath));
    }
  }

  return {
    enabled: config.enabled,
    siteId: config.siteId,
    configPath: path.relative(projectRoot, configPath),
    updatedViews
  };
}

export function getAiWidgetStatus(projectRoot = PROJECT_ROOT) {
  const configPath = path.join(projectRoot, 'assets/data/ai-config.json');
  if (!fs.existsSync(configPath)) {
    return { exists: false, enabled: true, siteId: 'default' };
  }
  const config = JSON.parse(fs.readFileSync(configPath, 'utf8'));
  return {
    exists: true,
    enabled: config.enabled !== false,
    siteId: config.siteId || 'twojastronawww',
    title: config.title || 'Asystent AI'
  };
}

if (process.argv[1] && path.resolve(process.argv[1]) === __filename) {
  const cmd = (process.argv[2] || 'status').toLowerCase();
  if (cmd === 'enable' || cmd === 'on') {
    const res = setAiWidgetState(PROJECT_ROOT, true);
    console.log(`✅ Asystent AI WŁĄCZONY (siteId: ${res.siteId})`);
  } else if (cmd === 'disable' || cmd === 'off') {
    const res = setAiWidgetState(PROJECT_ROOT, false);
    console.log(`⏸️ Asystent AI WYŁĄCZONY (siteId: ${res.siteId})`);
  } else {
    const st = getAiWidgetStatus(PROJECT_ROOT);
    console.log(`ℹ️ Status Asystenta AI: ${st.enabled ? 'WŁĄCZONY (ON)' : 'WYŁĄCZONY (OFF)'} | siteId: ${st.siteId}`);
  }
}
