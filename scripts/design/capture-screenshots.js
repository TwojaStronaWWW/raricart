#!/usr/bin/env node
/**
 * True-Viewport CDP Full-Page & Segmented Screenshot Capture (World-Class Designer v2.0)
 *
 * Fixes the critical bugs of `--window-size=1440,5800`:
 * 1. Keeps TRUE viewport height (`1440x900` Desktop, `390x844` Mobile) so `100vh` = `900px` (never `5800px`!).
 * 2. Scrolls through the entire document before capture to trigger `IntersectionObserver`,
 *    scroll-scrubbed motion, and `loading="lazy"` images.
 * 3. Automatically hides obstructing `.cookie-banner` overlays during visual inspection.
 * 4. Captures the ENTIRE document height (`desktop-full.png`, `mobile-full.png`) PLUS
 *    3 high-resolution slices (`desktop-top.png`, `desktop-mid.png`, `desktop-bottom.png`)
 *    so the Critic Subagent clearly sees the bottom half (Reviews, About, FAQ, Contact, Footer).
 * 5. Optionally captures `reference-moodboard.png` if a 3rd argument (`referenceUrl`) is provided.
 */
import { spawn } from 'node:child_process';
import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';

const url = process.argv[2] || 'http://127.0.0.1:8081';
const outDir = path.resolve(process.argv[3] || path.resolve(process.cwd(), 'screenshots'));
const referenceUrl = process.argv[4] || '';

const CHROME_PATHS = [
  'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
  'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
  'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
  'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe'
];

const browserBin = CHROME_PATHS.find((p) => fs.existsSync(p));
if (!browserBin) {
  console.error('ERROR: Neither Chrome nor Edge found for CDP screenshots.');
  process.exit(1);
}

fs.mkdirSync(outDir, { recursive: true });

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

class CDPConnection {
  constructor(wsUrl) {
    this.ws = new WebSocket(wsUrl);
    this.seq = 0;
    this.pending = new Map();
    this.openPromise = new Promise((resolve, reject) => {
      this.ws.addEventListener('open', resolve);
      this.ws.addEventListener('error', reject);
    });
    this.ws.addEventListener('message', (event) => {
      const msg = JSON.parse(String(event.data));
      if (msg.id && this.pending.has(msg.id)) {
        const { resolve, reject } = this.pending.get(msg.id);
        this.pending.delete(msg.id);
        if (msg.error) reject(new Error(msg.error.message || JSON.stringify(msg.error)));
        else resolve(msg.result);
      }
    });
  }

  async send(method, params = {}) {
    await this.openPromise;
    const id = ++this.seq;
    return new Promise((resolve, reject) => {
      this.pending.set(id, { resolve, reject });
      this.ws.send(JSON.stringify({ id, method, params }));
    });
  }

  close() {
    try {
      this.ws.close();
    } catch {}
  }
}

async function launchBrowserWithCDP() {
  const profileDir = fs.mkdtempSync(path.join(os.tmpdir(), 'wcd-cdp-'));
  const proc = spawn(
    browserBin,
    [
      '--headless=new',
      '--disable-gpu',
      '--hide-scrollbars',
      '--no-first-run',
      '--no-default-browser-check',
      '--remote-debugging-port=0',
      `--user-data-dir=${profileDir}`,
      'about:blank'
    ],
    { stdio: ['ignore', 'ignore', 'pipe'] }
  );

  const wsEndpoint = await new Promise((resolve, reject) => {
    const timeout = setTimeout(() => reject(new Error('Timed out waiting for CDP ws endpoint')), 12000);
    let stderrBuf = '';
    proc.stderr.on('data', (chunk) => {
      stderrBuf += chunk.toString();
      const match = stderrBuf.match(/DevTools listening on (ws:\/\/[^\r\n]+)/);
      if (match) {
        clearTimeout(timeout);
        resolve(match[1]);
      }
    });
    proc.on('exit', (code) => {
      clearTimeout(timeout);
      reject(new Error(`Browser exited early with code ${code}`));
    });
  });

  const httpBase = wsEndpoint.replace(/^ws:\/\//, 'http://').replace(/\/devtools\/browser\/.*$/, '');
  const listRes = await fetch(`${httpBase}/json/list`);
  const targets = await listRes.json();
  const pageTarget = targets.find((t) => t.type === 'page') || targets[0];

  return {
    proc,
    profileDir,
    pageWsUrl: pageTarget.webSocketDebuggerUrl
  };
}

async function preparePageAndScroll(cdp) {
  await cdp.send('Runtime.evaluate', {
    expression: `(async () => {
      // 1. Force eager loading on all lazy images
      document.querySelectorAll('img[loading="lazy"]').forEach(img => {
        img.loading = 'eager';
      });
      // 2. Hide cookie banners so they don't block bottom-left layout critique
      document.querySelectorAll('.cookie-banner, #cookieBanner, [id*="cookie"], [class*="cookie-banner"]').forEach(el => {
        el.style.setProperty('display', 'none', 'important');
      });
      // 3. Step-scroll through the entire page to trigger IntersectionObservers & scroll-scrubbed motion
      const totalH = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
      for (let y = 0; y <= totalH; y += 500) {
        window.scrollTo(0, y);
        window.dispatchEvent(new Event('scroll'));
        await new Promise(r => setTimeout(r, 35));
      }
      window.scrollTo(0, 0);
      window.dispatchEvent(new Event('scroll'));
      // 4. Wait for fonts & visible images
      if (document.fonts && document.fonts.ready) {
        await Promise.race([document.fonts.ready, new Promise(r => setTimeout(r, 1500))]);
      }
      await new Promise(r => setTimeout(r, 250));
      return Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
    })()`,
    awaitPromise: true,
    returnByValue: true
  });

  const metrics = await cdp.send('Runtime.evaluate', {
    expression: `Math.max(document.body.scrollHeight, document.documentElement.scrollHeight)`,
    returnByValue: true
  });
  return Math.min(15000, Math.max(900, Number(metrics?.result?.value) || 4000));
}

async function captureClipPng(cdp, outFile, clip) {
  const res = await cdp.send('Page.captureScreenshot', {
    format: 'png',
    captureBeyondViewport: true,
    fromSurface: true,
    clip: {
      x: clip.x,
      y: clip.y,
      width: clip.width,
      height: clip.height,
      scale: 1
    }
  });
  fs.writeFileSync(outFile, Buffer.from(res.data, 'base64'));
  console.log(`Captured ${path.basename(outFile)} (${clip.width}x${clip.height} @ y=${clip.y}) -> ${outFile}`);
}

async function run() {
  const { proc, profileDir, pageWsUrl } = await launchBrowserWithCDP();
  const cdp = new CDPConnection(pageWsUrl);

  try {
    await cdp.send('Page.enable');
    await cdp.send('Runtime.enable');

    // 1. DESKTOP TRUE VIEWPORT (1440x900 -> 100vh = 900px!)
    await cdp.send('Emulation.setDeviceMetricsOverride', {
      width: 1440,
      height: 900,
      deviceScaleFactor: 1,
      mobile: false
    });
    await cdp.send('Page.navigate', { url });
    await sleep(1200);
    const desktopHeight = await preparePageAndScroll(cdp);

    // Full desktop screenshot
    await captureClipPng(cdp, path.join(outDir, 'desktop-full.png'), {
      x: 0,
      y: 0,
      width: 1440,
      height: desktopHeight
    });

    // 3 high-resolution slices (Top / Mid / Bottom) so Critic inspects 100% of sections down to Footer
    const sliceH = Math.ceil(desktopHeight / 3);
    await captureClipPng(cdp, path.join(outDir, 'desktop-top.png'), {
      x: 0,
      y: 0,
      width: 1440,
      height: sliceH
    });
    await captureClipPng(cdp, path.join(outDir, 'desktop-mid.png'), {
      x: 0,
      y: sliceH,
      width: 1440,
      height: sliceH
    });
    await captureClipPng(cdp, path.join(outDir, 'desktop-bottom.png'), {
      x: 0,
      y: sliceH * 2,
      width: 1440,
      height: Math.max(600, desktopHeight - sliceH * 2)
    });

    // 2. MOBILE TRUE VIEWPORT (390x844 -> 100vh = 844px!)
    await cdp.send('Emulation.setDeviceMetricsOverride', {
      width: 390,
      height: 844,
      deviceScaleFactor: 1,
      mobile: true
    });
    await cdp.send('Page.navigate', { url });
    await sleep(1000);
    const mobileHeight = await preparePageAndScroll(cdp);

    await captureClipPng(cdp, path.join(outDir, 'mobile-full.png'), {
      x: 0,
      y: 0,
      width: 390,
      height: mobileHeight
    });

    // 3. OPTIONAL REFERENCE MOODBOARD CAPTURE (Anshu Chimala Technique 3 Baseline)
    if (referenceUrl && /^https?:\/\//i.test(referenceUrl)) {
      try {
        await cdp.send('Emulation.setDeviceMetricsOverride', {
          width: 1440,
          height: 900,
          deviceScaleFactor: 1,
          mobile: false
        });
        await cdp.send('Page.navigate', { url: referenceUrl });
        await sleep(2200);
        await captureClipPng(cdp, path.join(outDir, 'reference-moodboard.png'), {
          x: 0,
          y: 0,
          width: 1440,
          height: 2400
        });
      } catch (refErr) {
        console.warn(`Warning: Could not capture referenceUrl (${referenceUrl}): ${refErr.message}`);
      }
    }
  } finally {
    cdp.close();
    try {
      proc.kill('SIGKILL');
    } catch {}
    setTimeout(() => {
      try {
        fs.rmSync(profileDir, { recursive: true, force: true });
      } catch {}
    }, 500);
  }
}

run().catch((err) => {
  console.error('Screenshot capture failed:', err);
  process.exit(1);
});
