import { describe, it } from 'node:test';
import assert from 'node:assert/strict';
import { execSync } from 'node:child_process';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

const ROOT_DIR = path.join(__dirname, '..', '..');

describe('Integralność semantyczna i SEO HTML (Lejek 2026)', () => {
  let indexHtml = '';
  let pakietyHtml = '';

  try {
    indexHtml = execSync('php index.php', { cwd: ROOT_DIR, encoding: 'utf-8' });
  } catch (err) {
    throw new Error('Błąd renderowania index.php przez PHP: ' + err.message);
  }

  try {
    pakietyHtml = execSync('php pakiety.php', { cwd: ROOT_DIR, encoding: 'utf-8' });
  } catch (err) {
    throw new Error('Błąd renderowania pakiety.php przez PHP: ' + err.message);
  }

  it('strona index.php powinna renderować poprawny DOCTYPE oraz deklarację języka <html lang="pl">', () => {
    assert.match(indexHtml, /<!DOCTYPE html>/i);
    assert.match(indexHtml, /<html\s+lang="pl">/i);
  });

  describe('Metadane SEO w sekcji <head>', () => {
    it('powinien definiować precyzyjny tytuł <title> z marką Raricart', () => {
      const match = indexHtml.match(/<title>(.*?)<\/title>/i);
      assert.ok(match, 'Brak znacznika <title>');
      const title = match[1];
      assert.ok(title.includes('Raricart'), 'Tytuł nie zawiera nazwy Raricart');
    });

    it('powinien posiadać unikalny opis meta description o odpowiedniej długości', () => {
      const match = indexHtml.match(/<meta\s+name="description"\s+content="(.*?)"/is);
      assert.ok(match, 'Brak znacznika meta description');
      const desc = match[1];
      assert.ok(desc.length >= 40, `Opis jest zbyt krótki (${desc.length} znaków)`);
    });

    it('powinien posiadać link kanoniczny wskazujący domenę https://raricart.pl/', () => {
      assert.match(indexHtml, /<link\s+rel="canonical"\s+href="https:\/\/raricart\.pl\/?">/i);
    });

    it('powinien zawierać kompletne tagi protokołu Open Graph', () => {
      assert.match(indexHtml, /<meta\s+property="og:type"\s+content="website">/i);
      assert.match(indexHtml, /<meta\s+property="og:url"\s+content="https:\/\/raricart\.pl\/?">/i);
      assert.match(indexHtml, /<meta\s+property="og:title"\s+content="[^"]+">/i);
      assert.match(indexHtml, /<meta\s+property="og:description"\s+content="[^"]+">/i);
      assert.match(indexHtml, /<meta\s+property="og:image"\s+content="[^"]+">/i);
    });

    it('powinien zawierać kompletne tagi Twitter / X Card', () => {
      assert.match(indexHtml, /<meta\s+name="twitter:card"\s+content="summary_large_image">/i);
      assert.match(indexHtml, /<meta\s+name="twitter:title"\s+content="[^"]+">/i);
      assert.match(indexHtml, /<meta\s+name="twitter:description"\s+content="[^"]+">/i);
      assert.match(indexHtml, /<meta\s+name="twitter:image"\s+content="[^"]+">/i);
    });

    it('powinien zawierać odnośniki do favicon, manifestu oraz meta theme-color', () => {
      assert.match(indexHtml, /<meta\s+name="theme-color"\s+content="#[a-fA-F0-9]{6}">/i);
      assert.match(indexHtml, /<link\s+rel="manifest"\s+href="\/site\.webmanifest">/i);
      assert.match(indexHtml, /<link\s+rel="icon"[^>]*href="\/favicon\.ico"/i);
    });
  });

  describe('Architektura sekcji Lejka 2026', () => {
    const requiredSections = [
      { name: 'hero', cls: 'hero-split' },
      { name: 'trust-bar', cls: 'trust-bar' },
      { name: 'stations', cls: 'stations-section' },
      { name: 'why-station', cls: 'why-station-section' },
      { name: 'process', cls: 'process-section' },
      { name: 'audiences', cls: 'audiences-section' },
      { name: 'why-raricart', cls: 'section-why-raricart' },
      { name: 'realizations', cls: 'section-realizations' },
      { name: 'reviews', cls: 'section-reviews' },
      { name: 'about-story', cls: 'section-about-story' },
      { name: 'faq', cls: 'section-faq' },
      { name: 'pre-cta', cls: 'section-pre-cta' },
      { name: 'contact', cls: 'section-contact' },
    ];

    for (const sec of requiredSections) {
      it(`powinien renderować sekcję ${sec.name} z klasą .${sec.cls}`, () => {
        assert.ok(
          indexHtml.includes(sec.cls),
          `Brak wyrenderowanej sekcji ${sec.name} (.${sec.cls}) w HTML`
        );
      });
    }
  });

  describe('Semantyka i hierarchia drzewa DOM (W3C)', () => {
    it('modale powinny znajdować się przed znacznikiem stopki wewnątrz body', () => {
      const modalPos = indexHtml.indexOf('id="modal"');
      const footerPos = indexHtml.indexOf('<footer');
      const bodyEndPos = indexHtml.indexOf('</body>');

      assert.ok(modalPos !== -1, 'Brak #modal');
      assert.ok(footerPos !== -1, 'Brak stopki');
      assert.ok(modalPos < footerPos, '#modal powinien być wyrenderowany przed stopką');
      assert.ok(footerPos < bodyEndPos, 'stopka powinna być wewnątrz body');
    });

    it('główny skrypt JavaScript powinien być ładowany jako moduł przed zamknięciem </body>', () => {
      const scriptPos = indexHtml.indexOf('/assets/js/script.js');
      const bodyEndPos = indexHtml.indexOf('</body>');

      assert.ok(scriptPos !== -1, 'Brak skryptu script.js');
      assert.ok(scriptPos < bodyEndPos, 'script.js powinien być umieszczony przed zamknięciem body');
      assert.match(indexHtml, /<script\s+type="module"\s+src="[^"]*\/assets\/js\/script\.js/);
    });
  });

  describe('Integralność formularza kontaktowego', () => {
    it('formularz kontaktowy powinien posiadać wszystkie wymagane pola wejściowe', () => {
      const formFields = ['name', 'email', 'phone', 'date', 'location', 'guests', 'event_type', 'message'];
      for (const f of formFields) {
        assert.ok(
          indexHtml.includes(`name="${f}"`) || indexHtml.includes(`id="${f}"`),
          `Brak pola formularza: ${f}`
        );
      }
    });

    it('formularz powinien posiadać przycisk wysyłki typu submit', () => {
      assert.match(indexHtml, /<button[^>]*type="submit"[^>]*>/);
    });
  });

  describe('Podstrona pakiety.php', () => {
    it('powinna renderować poprawny nagłówek, styl i nawigację powrotną', () => {
      assert.match(pakietyHtml, /<!DOCTYPE html>/i);
      assert.match(pakietyHtml, /<title>.*Pakiety.*Raricart.*<\/title>/i);
      assert.ok(pakietyHtml.includes('pakiety-style.css'), 'Brak załączenia pakiety-style.css');
    });
  });
});
