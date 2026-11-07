#!/usr/bin/env node
/**
 * World-Class Designer v2.1 — Deterministic Computed-Layout & Visual Linter (CDP)
 *
 * Why this script exists:
 * Prompts alone are probabilistic — an LLM writing 5,000 lines of CSS can forget
 * rules in SKILL.md, and vision models looking at a downscaled 13,000px screenshot
 * cannot measure exact pixel dimensions or calculate mathematical WCAG contrast.
 *
 * This script connects to headless Chrome/Edge via Chrome DevTools Protocol (CDP),
 * evaluates the LIVE rendered DOM at true Desktop (1440x900) and Mobile (390x844)
 * viewports, and enforces deterministic mathematical gates for [DR-01]..[DR-06].
 *
 * Usage:
 *   node audit-layout-cdp.js <url> [output-json-path]
 */

import { spawn } from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';
import os from 'node:os';

const url = process.argv[2] || 'http://127.0.0.1:8080';
const outJsonPath = process.argv[3] || '';

const BROWSER_CANDIDATES = [
  'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
  'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
  'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
  'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe',
  '/usr/bin/google-chrome',
  '/usr/bin/chromium-browser',
  '/usr/bin/chromium',
  '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
];

function findBrowser() {
  for (const candidate of BROWSER_CANDIDATES) {
    if (fs.existsSync(candidate)) return candidate;
  }
  throw new Error('No Chromium-based browser (Chrome/Edge) found for CDP layout audit.');
}

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

class CDPSession {
  constructor(wsUrl) {
    this.ws = new WebSocket(wsUrl);
    this.id = 0;
    this.pending = new Map();
  }

  async connect() {
    await new Promise((resolve, reject) => {
      this.ws.onopen = resolve;
      this.ws.onerror = reject;
    });
    this.ws.onmessage = (event) => {
      const msg = JSON.parse(event.data);
      if (msg.id && this.pending.has(msg.id)) {
        const { resolve, reject } = this.pending.get(msg.id);
        this.pending.delete(msg.id);
        if (msg.error) reject(new Error(msg.error.message));
        else resolve(msg.result);
      }
    };
  }

  send(method, params = {}) {
    const id = ++this.id;
    return new Promise((resolve, reject) => {
      this.pending.set(id, { resolve, reject });
      this.ws.send(JSON.stringify({ id, method, params }));
    });
  }

  close() {
    this.ws.close();
  }
}

async function launchBrowser(browserPath) {
  const userDataDir = fs.mkdtempSync(path.join(os.tmpdir(), 'wcd-audit-cdp-'));
  const port = 9350 + Math.floor(Math.random() * 500);
  const proc = spawn(
    browserPath,
    [
      `--remote-debugging-port=${port}`,
      `--user-data-dir=${userDataDir}`,
      '--headless=new',
      '--disable-gpu',
      '--hide-scrollbars',
      '--no-first-run',
      '--no-default-browser-check',
      'about:blank',
    ],
    { stdio: 'ignore' }
  );

  let wsUrl = null;
  for (let i = 0; i < 40; i++) {
    await sleep(150);
    try {
      const res = await fetch(`http://127.0.0.1:${port}/json/list`);
      const tabs = await res.json();
      const pageTab = tabs.find((t) => t.type === 'page');
      if (pageTab && pageTab.webSocketDebuggerUrl) {
        wsUrl = pageTab.webSocketDebuggerUrl;
        break;
      }
    } catch {
      // wait for startup
    }
  }

  if (!wsUrl) {
    proc.kill();
    throw new Error('Failed to connect to headless browser CDP endpoint.');
  }

  return { proc, userDataDir, wsUrl };
}

async function runAuditForViewport(cdp, targetUrl, vpWidth, vpHeight, label) {
  await cdp.send('Emulation.setDeviceMetricsOverride', {
    width: vpWidth,
    height: vpHeight,
    deviceScaleFactor: 1,
    mobile: vpWidth < 768,
  });

  await cdp.send('Page.navigate', { url: targetUrl });
  await sleep(800);

  const evalResult = await cdp.send('Runtime.evaluate', {
    awaitPromise: true,
    returnByValue: true,
    expression: `(async () => {
      const errors = [];
      const warnings = [];
      const metrics = {};

      // Force eager images & scroll through to trigger IntersectionObservers
      document.querySelectorAll('img[loading="lazy"]').forEach(img => { img.loading = 'eager'; });
      const totalH = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
      for (let y = 0; y <= totalH; y += 600) {
        window.scrollTo(0, y);
        await new Promise(r => setTimeout(r, 30));
      }
      window.scrollTo(0, 0);
      await Promise.all(Array.from(document.images).map(img => {
        if (img.complete) return Promise.resolve();
        return new Promise(res => {
          img.addEventListener('load', res, { once: true });
          img.addEventListener('error', res, { once: true });
          setTimeout(res, 1000);
        });
      }));

      const vw = window.innerWidth;
      const vh = window.innerHeight;
      const docScrollWidth = document.documentElement.scrollWidth;
      metrics.viewport = { vw, vh, totalHeight: document.documentElement.scrollHeight, scrollWidth: docScrollWidth };

      // 1. Horizontal Overflow Check
      if (docScrollWidth > vw + 2) {
        errors.push(\`[OVERFLOW] Horizontal overflow detected on \${vw}px viewport: scrollWidth=\${docScrollWidth}px > innerWidth=\${vw}px\`);
      }

      // 2. [DR-01] Anti-Squash / Image Starvation Check
      const contentImgSelectors = [
        '.hero-split img',
        '.stations-grid img',
        '.why-station-section img',
        '.gallery-grid img',
        '.section-about-story img'
      ];
      const minImgH = vw >= 1024 ? 175 : 105;
      const minImgW = vw >= 1024 ? 175 : 105;

      for (const sel of contentImgSelectors) {
        document.querySelectorAll(sel).forEach((img, idx) => {
          const style = window.getComputedStyle(img);
          if (style.display === 'none' || style.visibility === 'hidden') return;
          const rect = img.getBoundingClientRect();
          if (rect.height < minImgH || rect.width < minImgW) {
            errors.push(\`[DR-01][IMAGE_SQUASH] \${sel} [#\${idx + 1}] is artificially squashed to \${Math.round(rect.width)}x\${Math.round(rect.height)}px (minimum \${minImgW}x\${minImgH}px required on \${vw}px viewport).\`);
          }
        });
      }

      // 3. [DR-03] Pseudo-Element Debug Leak Check ("KADR 0", "attr(", "undefined")
      const leakRegex = /KADR\\s*0|undefined|null|NaN|attr\\(/i;
      document.querySelectorAll('*').forEach(el => {
        for (const pseudo of ['::before', '::after']) {
          const content = window.getComputedStyle(el, pseudo).content;
          if (content && content !== 'none' && content !== 'normal' && leakRegex.test(content)) {
            errors.push(\`[DR-03][PSEUDO_LEAK] Element <\${el.tagName.toLowerCase()} class="\${el.className}">\${pseudo} leaks raw debug string: \${content}\`);
          }
        }
      });

      // 4. [DR-06] Coco Chanel Redundancy Check (Simultaneous Icon + Numeric Index)
      const isVisible = (el) => {
        if (!el) return false;
        const st = window.getComputedStyle(el);
        if (st.display === 'none' || st.visibility === 'hidden' || parseFloat(st.opacity) === 0) return false;
        const r = el.getBoundingClientRect();
        return r.width > 2 && r.height > 2;
      };

      document.querySelectorAll('.process-step-card').forEach((card, i) => {
        const num = card.querySelector('.step-number');
        const icon = card.querySelector('.step-icon, .step-icon svg');
        if (isVisible(num) && isVisible(icon)) {
          errors.push(\`[DR-06][COCO_CHANEL] .process-step-card [#\${i + 1}] renders BOTH .step-number and .step-icon simultaneously.\`);
        }
      });

      document.querySelectorAll('.audience-card').forEach((card, i) => {
        const num = card.querySelector('.audience-number, .card-num');
        const icon = card.querySelector('.audience-icon-box, .audience-icon-box svg');
        if (isVisible(num) && isVisible(icon)) {
          errors.push(\`[DR-06][COCO_CHANEL] .audience-card [#\${i + 1}] renders BOTH numeric index and .audience-icon-box SVG simultaneously.\`);
        }
      });

      document.querySelectorAll('.trust-item').forEach((item, i) => {
        const beforeContent = window.getComputedStyle(item, '::before').content;
        const hasPseudoIndex = beforeContent && beforeContent !== 'none' && beforeContent !== 'normal' && /0[1-4]/.test(beforeContent);
        const icon = item.querySelector('.trust-icon, svg');
        if (hasPseudoIndex && isVisible(icon)) {
          errors.push(\`[DR-06][COCO_CHANEL] .trust-item [#\${i + 1}] renders BOTH ::before numeric index (\${beforeContent}) AND .trust-icon SVG.\`);
        }
      });

      // 5. [DR-04] Focal Point Cropping Check on Culinary Station Images
      if (vw >= 1024) {
        document.querySelectorAll('.stations-grid .station-card img, .why-station-img').forEach((img, i) => {
          const objPos = window.getComputedStyle(img).objectPosition;
          // Default 50% 50% on narrow portrait/square frames often clips the cart or food; log metric
          metrics[\`station_img_\${i + 1}_objectPosition\`] = objPos;
        });
      }

      // 6. [DR-05] Unbalanced Split-Column Cage Check (Desktop only)
      if (vw >= 1024) {
        const splitHeaders = ['.process-header', '.faq-header-box', '.contact-header-box'];
        for (const sel of splitHeaders) {
          const headerEl = document.querySelector(sel);
          if (!headerEl || !headerEl.parentElement) continue;
          const hStyle = window.getComputedStyle(headerEl);
          const pRect = headerEl.parentElement.getBoundingClientRect();
          const hRect = headerEl.getBoundingClientRect();
          // If header sits in a multi-column grid where parent is > 2.2x taller than header content
          // and header has a right border stretching the full cell without sticky positioning
          if (pRect.height > 650 && hRect.height > pRect.height * 0.85 && hStyle.position !== 'sticky') {
            const hasRightBorder = parseFloat(hStyle.borderRightWidth) > 0;
            if (hasRightBorder) {
              warnings.push(\`[DR-05][EMPTY_CAGE] \${sel} stretches \${Math.round(hRect.height)}px with a right border but is not position: sticky.\`);
            }
          }
        }
      }

      // 7. WCAG 2.2 Contrast Check on Key Funnel Copy (catching dark-on-dark like .pre-cta-subtitle)
      function parseRgba(str) {
        const m = str.match(/rgba?\\((\\d+),\\s*(\\d+),\\s*(\\d+)(?:,\\s*([\\d.]+))?\\)/);
        if (!m) return null;
        return { r: +m[1], g: +m[2], b: +m[3], a: m[4] !== undefined ? +m[4] : 1 };
      }
      function getEffectiveBg(el) {
        let cur = el;
        while (cur && cur !== document.documentElement) {
          const st = window.getComputedStyle(cur);
          const bg = parseRgba(st.backgroundColor);
          if (bg && bg.a >= 0.5) return bg;
          if (st.backgroundImage && st.backgroundImage !== 'none') return null;
          if (cur.parentElement) {
            const overlay = cur.parentElement.querySelector(':scope > .pre-cta-overlay, :scope > .pre-cta-backdrop');
            if (overlay && overlay !== cur) {
              const ost = window.getComputedStyle(overlay);
              const obg = parseRgba(ost.backgroundColor);
              if (obg && obg.a >= 0.5) return obg;
              if (ost.backgroundImage && ost.backgroundImage !== 'none' || overlay.tagName === 'IMG') return null;
            }
          }
          cur = cur.parentElement;
        }
        return { r: 245, g: 241, b: 235, a: 1 };
      }
      function luminance({ r, g, b }) {
        const a = [r, g, b].map(v => {
          v /= 255;
          return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
        });
        return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
      }
      function contrastRatio(c1, c2) {
        const l1 = luminance(c1);
        const l2 = luminance(c2);
        return (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05);
      }

      const criticalTextSelectors = [
        '.hero-title', '.hero-description',
        '.station-name', '.station-desc',
        '.why-station-title', '.pillar-desc',
        '.step-title', '.step-desc',
        '.audience-card-title', '.audience-card-desc',
        '.review-text',
        '.about-story-title', '.about-story-lead',
        '.faq-title', '.faq-question',
        '.pre-cta-title', '.pre-cta-subtitle',
        '.contact-title', '.contact-subtitle'
      ];

      for (const sel of criticalTextSelectors) {
        const el = document.querySelector(sel);
        if (!el || !isVisible(el)) continue;
        const fg = parseRgba(window.getComputedStyle(el).color);
        const bg = getEffectiveBg(el);
        if (fg && bg) {
          const ratio = contrastRatio(fg, bg);
          if (ratio < 3.0) {
            errors.push(\`[WCAG_CONTRAST] \${sel} has low contrast ratio \${ratio.toFixed(2)}:1 (fg=rgb(\${fg.r},\${fg.g},\${fg.b}) on bg=rgb(\${bg.r},\${bg.g},\${bg.b})).\`);
          }
        }
      }

      return { label: '${label}', errors, warnings, metrics };
    })()`,
  });

  return evalResult.result.value;
}

async function main() {
  const browserPath = findBrowser();
  const { proc, userDataDir, wsUrl } = await launchBrowser(browserPath);
  const cdp = new CDPSession(wsUrl);

  try {
    await cdp.connect();
    await cdp.send('Page.enable');
    await cdp.send('Runtime.enable');

    const desktopReport = await runAuditForViewport(cdp, url, 1440, 900, 'desktop-1440x900');
    const mobileReport = await runAuditForViewport(cdp, url, 390, 844, 'mobile-390x844');

    const totalErrors = [...desktopReport.errors, ...mobileReport.errors];
    const totalWarnings = [...desktopReport.warnings, ...mobileReport.warnings];

    const report = {
      url,
      timestamp: new Date().toISOString(),
      passed: totalErrors.length === 0,
      errorCount: totalErrors.length,
      warningCount: totalWarnings.length,
      desktop: desktopReport,
      mobile: mobileReport,
    };

    if (outJsonPath) {
      fs.mkdirSync(path.dirname(path.resolve(outJsonPath)), { recursive: true });
      fs.writeFileSync(outJsonPath, JSON.stringify(report, null, 2), 'utf8');
    }

    console.log(`=== CDP LAYOUT AUDIT: ${url} ===`);
    console.log(`Desktop (1440x900): ${desktopReport.errors.length} errors, ${desktopReport.warnings.length} warnings (height=${desktopReport.metrics.viewport.totalHeight}px)`);
    console.log(`Mobile  (390x844):  ${mobileReport.errors.length} errors, ${mobileReport.warnings.length} warnings (height=${mobileReport.metrics.viewport.totalHeight}px)`);

    if (totalErrors.length > 0) {
      console.error('\n❌ DETERMINISTIC LAYOUT ERRORS:');
      for (const err of totalErrors) console.error(`  - ${err}`);
      process.exitCode = 1;
    } else {
      console.log('✅ ALL DETERMINISTIC LAYOUT GATES PASSED (0 errors)');
    }
    if (totalWarnings.length > 0) {
      console.warn('\n⚠️ WARNINGS:');
      for (const w of totalWarnings) console.warn(`  - ${w}`);
    }
  } finally {
    cdp.close();
    proc.kill();
    try {
      fs.rmSync(userDataDir, { recursive: true, force: true });
    } catch {
      // ignore temp cleanup lock
    }
  }
}

main().catch((err) => {
  console.error('CDP Layout Audit failed:', err.message);
  process.exit(1);
});
