import { describe, it } from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

const ROOT_DIR = path.join(__dirname, '..', '..');
const STYLES_PATH = path.join(ROOT_DIR, 'assets', 'css', 'styles.css');
const PAKIETY_STYLES_PATH = path.join(ROOT_DIR, 'assets', 'css', 'pakiety-style.css');

describe('Integralność stylów CSS (Design System & Responsywność)', () => {
  const css = fs.readFileSync(STYLES_PATH, 'utf-8');
  const pakietyCss = fs.readFileSync(PAKIETY_STYLES_PATH, 'utf-8');

  it('główny arkusz stylów styles.css powinien istnieć i mieć rozmiar produkcyjny', () => {
    assert.ok(fs.existsSync(STYLES_PATH));
    assert.ok(css.length > 5000, `Arkusz CSS jest podejrzanie krótki (${css.length} bajtów)`);
  });

  it('arkusz pakiety-style.css powinien istnieć i posiadać zdefiniowane reguły', () => {
    assert.ok(fs.existsSync(PAKIETY_STYLES_PATH));
    assert.ok(pakietyCss.length > 1000);
  });

  it('arkusz powinien definiować zmienne design tokens w :root', () => {
    assert.match(css, /:root\s*\{/i);
    assert.match(css, /--font-primary/i);
    assert.match(css, /--color-accent/i);
    assert.match(css, /--spacing-section/i);
  });

  it('arkusz powinien zawierać reguły dla klas manipulowanych dynamicznie przez JavaScript', () => {
    const dynamicClasses = ['active', 'lightbox-open', 'modal', 'modal-close', 'cookie-banner'];
    for (const cls of dynamicClasses) {
      const regex = new RegExp(`\\.${cls}[^a-zA-Z0-9_-]`);
      assert.ok(regex.test(css), `Brak definicji stylu dla dynamicznej klasy .${cls} w styles.css`);
    }
  });

  it('arkusz nie powinien zawierać pustych reguł blokowych ({ })', () => {
    // Sprawdza czy nie ma pustych selektorów typu .klasa { }
    const emptyRules = css.match(/[a-zA-Z0-9_#.-]+\s*\{\s*\}/g);
    assert.strictEqual(emptyRules, null, `Wykryto puste reguły CSS: ${emptyRules?.join(', ')}`);
  });

  it('arkusz powinien implementować reguły responsywne dla urządzeń mobilnych (@media)', () => {
    assert.match(css, /@media\s*\(\s*max-width:\s*\d+px\s*\)/i);
  });

  it('arkusze stylów powinny implementować Vanilla CSS Baseline [REF-03] (color-scheme, scrollbar-gutter: stable, @view-transition, text-wrap: balance/pretty) oraz WCAG 2.2 AA [REF-04] (.skip-link, :focus-visible)', () => {
    assert.match(css, /color-scheme:\s*light/i);
    assert.match(css, /scrollbar-gutter:\s*stable/i);
    assert.match(css, /@view-transition\s*\{\s*navigation:\s*auto;\s*\}/i);
    assert.match(css, /text-wrap:\s*balance/i);
    assert.match(css, /text-wrap:\s*pretty/i);
    assert.match(css, /\.skip-link\b/);
    assert.match(css, /:focus-visible\b/);

    assert.match(pakietyCss, /color-scheme:\s*light/i);
    assert.match(pakietyCss, /scrollbar-gutter:\s*stable/i);
    assert.match(pakietyCss, /text-wrap:\s*balance/i);
    assert.match(pakietyCss, /text-wrap:\s*pretty/i);
  });
});
