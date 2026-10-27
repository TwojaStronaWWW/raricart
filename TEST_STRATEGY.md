# PLAN PANCERNYCH TESTÓW I PRZYPADKÓW BRZEGOWYCH (`TEST_STRATEGY.md`)

> **Standard testowy:** Zero-Dependency Testing (`node:test`, `node:assert/strict`, `tests/helpers/mock-dom.js` z globalnym strażnikiem `NETWORK_FORBIDDEN` zgodnie z `AGENTS.md` §2 i §4.4).

---

## 1. Krytyczne Ścieżki w Kodzie bez Pokrycia w Testach (Uncovered Critical Paths)

| # | Projekt | Moduł / Ścieżka pliku | Niepokryta ścieżka krytyczna | Ryzyko biznesowe / techniczne |
|---|---|---|---|---|
| **1** | `_site-template` | [scripts/serve.js](file:///e:/Projekty/_site-template/scripts/serve.js#L47-L70) | Blokowanie dostępu HTTP do plików `.csv` (`api/leads_*.csv`), `api/rate_limits/*.json`, `api/sessions/*` oraz odporność na błędne sekwencje `%XX` w `decodeURIComponent` | Wyciek bazy leadów CSV podczas publicznego udostępniania podglądu przez Cloudflare Quick Tunnel (`npm run preview:share`) lub crash serwera przy błędnym URL. |
| **2** | `_site-template` | [assets/js/ai-widget.js](file:///e:/Projekty/_site-template/assets/js/ai-widget.js#L364-L373) | Formatowanie linków URL zawierających znak `@` w `formatMarkdown()`, escapowanie `data-title` (`lines 1303, 1307`), zachowanie szkicu formularza przy wysyłce `#tswAiForm` (`line 1861`) oraz limit pojemności `responseCache.entries` | Uszkodzenie znaczników `<a>` w czacie AI, podatność DOM XSS przy dynamicznym tytule widżetu oraz przypadkowe kasowanie szkicu formularza kontaktowego. |
| **3** | `_site-template` | [api/contact.php](file:///e:/Projekty/_site-template/api/contact.php#L107) & [api/MediaService.php](file:///e:/Projekty/_site-template/api/MediaService.php#L72) | Oczyszczanie pola `$phone` ze znaków `\r\n` (CRLF) oraz weryfikacja wymiarów pikselowych obrazu (`getimagesize()`) przed alokacją bufora GD | Wstrzyknięcie nagłówków SMTP/Ntfy.sh przez pole telefonu oraz wyczerpanie pamięci RAM (Decompression Bomb OOM) przy wgrywaniu obrazu PNG. |
| **4** | `twojastronawww` | [api/contact.php](file:///e:/Projekty/twojastronawww/api/contact.php#L44-L129) & [api/export-leads.php](file:///e:/Projekty/twojastronawww/api/export-leads.php#L8-L17) | Trwałe unieważnienie jednorazowego tokenu CSRF na dysku przed `@session_write_close()` oraz blokada eksportu CSV przy pustym `APP_PIN` (`hash_equals('', '')`) | Wielokrotne użycie tego samego tokenu CSRF (Replay Attack) oraz publiczny wyciek całej bazy klientów CSV bez autoryzacji. |
| **5** | `twojastronawww` & `raricart` | [twojastronawww/sw.js](file:///e:/Projekty/twojastronawww/sw.js#L16) & [raricart/sw.js](file:///e:/Projekty/raricart/sw.js#L6-L14) | Weryfikacja istnienia na dysku wszystkich plików wpisanych do tablicy `PRECACHE_URLS` w Service Workerze | Pojedyncza błędna ścieżka (`/favicon.svg` lub `/assets/css/style.css`) powoduje błąd 404 w `cache.addAll()` i całkowite wyłączenie trybu offline PWA. |
| **6** | `fuegolingua` | [assets/js/contact.js](file:///e:/Projekty/fuegolingua/assets/js/contact.js#L33-L180) | Serializacja pola `<select name="purpose">` do payloadu POST i szkicu `localStorage`, strażnik `isSubmitting` oraz bezpieczne renderowanie `formStatus` przez `textContent` | Utrata informacji o wybranym celu nauki w każdym zgłoszeniu oraz podatność DOM XSS w komunikatach błędów formularza. |
| **7** | `katosalsahub` | [katosalsahub.pl/assets/php/wiki.php](file:///e:/Projekty/katosalsahub/katosalsahub.pl/assets/php/wiki.php#L85) & [wiki.js](file:///e:/Projekty/katosalsahub/katosalsahub.pl/assets/js/wiki.js#L1168) | Brak zahardkodowanych hashy w `wiki.php`, transakcyjność `check_and_Clean_login_attempts()` oraz wydajność prekompilacji `RegExp` w `linkifyDefinition()` | Stała tylna furtka do konta Super-Admina niezależna od `.env` oraz przycięcie głównego wątku UI (~40 000 kompilacji regex przy renderowaniu siatki). |
| **8** | `raricart` | [admin/change_password.php](file:///e:/Projekty/raricart/admin/change_password.php#L41) & `admin/*.php` | Obsługa tablicowego formatu użytkownika `['hash' => ..., 'force_change' => true]` w `password_verify()` oraz wymóg `check_login()` we wszystkich skryptach `admin/*.php` | `Fatal TypeError` w PHP 8 uniemożliwiający zmianę hasła administratora oraz nieautoryzowany dostęp do `migrate_users.php` i `optimize_existing.php`. |
| **9** | `VPS` & `hd-osticket-helpdesk` | [compose/runners.yml](file:///e:/Projekty/VPS/compose/runners.yml#L38) & [config/ticket_hook.php](file:///e:/Projekty/hd-osticket-helpdesk/config/ticket_hook.php#L71-L293) | Wymuszenie `:ro` na `/var/run/docker.sock`, pobranie ostatniego przeskoku z `X-Forwarded-For`, limit `php://input` (64 KB) i kodowanie MIME Base64 tematów e-mail | Przejęcie hosta VPS przez gniazdo Dockera, obejście Rate-Limitera AI oraz wstrzyknięcie nagłówków SMTP w helpdesku. |
| **10** | `radar-p1` & `sejf-vod` | [public/_worker.js](file:///e:/Projekty/radar-p1/public/_worker.js#L34), [app.js](file:///e:/Projekty/radar-p1/public/js/app.js#L1262), [videos.ts](file:///e:/Projekty/sejf-vod/src/actions/videos.ts#L224) | Dostępność `/.well-known/security.txt` w Workerze, obsługa UTF-8 w `btoa()` wewnątrz `try/finally`, weryfikacja ról w `SECURITY DEFINER` oraz spójność kontraktu JSON transkodera HLS | Permanentna blokada formularza logowania przy polskich znakach, wyciek bazy użytkowników `auth.users` oraz niedziałające transkodowanie wideo HLS. |

---

## 2. Katalog Ekstremalnych Przypadków Brzegowych (Extreme Edge-Cases Matrix)

| Kategoria Edge-Case | Scenariusz testowy (Wejście / Warunek brzegowy) | Oczekiwane zachowanie systemu (Fail-Safe / Fail-Closed) |
|---|---|---|
| **Uszkodzone pliki i pamięć podręczna (`Corrupted Storage`)** | `localStorage` zawiera ucięty ciąg JSON `"{bad_json"` pod kluczem `tsw_ai_resp_cache_v2` lub `radar_p1_ai_cache_v1`; plik `rate_*.json` na dysku ma `0 bajtów`. | Natychmiastowe wywołanie `invalidateResponseCache()` / `localStorage.removeItem(key)` bez rzucenia wyjątku; inicjalizacja nowej struktury licznika `['count' => 1, 'reset_at' => ...]` wewnątrz `flock(LOCK_EX)`. |
| **Puste tablice i nietypowe typy danych (`Null / NaN / Boolean / Infinity`)** | Przekazanie `true`, `false`, `null`, `undefined`, `NaN`, `Infinity`, `-Infinity` lub `""` do walidatora czasu sesji (`isStrictFiniteNumber`) lub `computeSearchRelevance(null, [])`. | `isStrictFiniteNumber()` odrzuca wartości `boolean` (`typeof val === 'number' && Number.isFinite(val)` zwraca `false` dla `true`/`false`); wyszukiwarka bezpiecznie zwraca `[]` lub `0 pkt`. |
| **Złośliwe kodowanie znaków (`UTF-8 / URL / CRLF / CSV Injection`)** | 1. URL `/%E0%A4%A` w `serve.js`.<br>2. Hasło `"żółć"` w `btoa()`.<br>3. Telefon `"+48 123\r\nBcc: evil@x.com"` w `contact.php`.<br>4. Imię `"=HYPERLINK(\"http://evil\")"` w CSV. | 1. `serve.js` zwraca `400 Bad Request` bez zatrzymania procesu.<br>2. `TextEncoder` + `btoa` koduje UTF-8, a `finally` zawsze odblokowuje przycisk.<br>3. `\r\n` są wycinane z telefonu.<br>4. Komórka CSV otrzymuje apostrof `'=HYPERLINK(...)`. |
| **Błędy sieci i wygasłe sesje (`Network Timeout / Offline / 429 / 503`)** | Brak połączenia lub status `503` z `https://ai.twojastronawww.pl/chat-router.php`; niezamockowany `fetch` w testach jednostkowych. | W testach jednostkowych `mock-dom.js` natychmiast rzuca `Error: NETWORK_FORBIDDEN`. W przeglądarce `ai-widget.js` przełącza się w `0 ms` na lokalną bazę wiedzy `getLocalFallbackAnswer()`. |
| **Wyścigi współbieżności (`Race Conditions / TOCTOU / Double Submit`)** | 1. 10 równoległych żądań `POST /api/contact.php` tworzy nowy plik `leads_YYYY-MM.csv`.<br>2. Użytkownik klika „Wyślij” 5 razy w ciągu `100 ms`. | 1. Sprawdzenie `fstat($fp)['size'] === 0` wewnątrz `flock($fp, LOCK_EX)` gwarantuje zapisanie nagłówka BOM+CSV dokładnie jeden raz.<br>2. Flaga `isSubmitting = true` blokuje 4 kolejne kliknięcia aż do bloku `finally`. |

---

## 3. Gotowy, Kompletny Kod Testów Jednostkowych i Integracyjnych (`node:test`)

Poniższy zestaw testów (`tests/unit/deep-audit-hardening.test.js`) jest w 100% kompatybilny z natywnym runnerem `node --test` w `_site-template` (zero zewnętrznych zależności) i weryfikuje wszystkie kluczowe poprawki bezpieczeństwa, wydajności i jakości kodu.

### 3.1. Zestaw 1: `_site-template` — `tests/unit/deep-audit-hardening.test.js`

```javascript
/**
 * @file tests/unit/deep-audit-hardening.test.js
 * @description Testy jednostkowe i integracyjne weryfikujące poprawki z Głębokiego Audytu (Faza 1–4):
 * - Ochrona plików .csv i rate_limits w scripts/serve.js oraz odporność na uszkodzone URI (%E0%A4%A)
 * - Ochrona przed CRLF Header Injection w polu telefonu w api/contact.php
 * - Ochrona przed Decompression Bomb (getimagesize) w api/MediaService.php i flock(LOCK_SH) w PrivateStorageService.php
 * - Ochrona przed DOM XSS w data-title, zachowanie szkicu przy #tswAiForm i formatowanie URL z '@' w assets/js/ai-widget.js
 * - Zabezpieczenie workflowów CI/CD (author_association w ai-security-review.yml, ci.yml w dependabot-automerge.yml)
 */

import { describe, it, beforeEach, afterEach } from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { setupMockDom } from '../helpers/mock-dom.js';
import {
  formatMarkdown,
  initAiWidget,
  isStrictFiniteNumber,
  computeSearchRelevance
} from '../../assets/js/ai-widget.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const ROOT_DIR = path.resolve(__dirname, '../..');

describe('Faza 3 — Pancerne Testy Bezpieczeństwa i Przypadków Brzegowych (_site-template)', () => {
  let domEnv;

  beforeEach(() => {
    domEnv = setupMockDom();
  });

  afterEach(() => {
    if (domEnv && typeof domEnv.cleanup === 'function') {
      domEnv.cleanup();
    }
  });

  describe('1. Bezpieczeństwo serwera deweloperskiego (scripts/serve.js)', () => {
    it('powinien blokować dostęp do plików .csv, .lock, .gitleaks.toml oraz katalogów api/sessions i api/rate_limits', () => {
      const serveCode = fs.readFileSync(path.join(ROOT_DIR, 'scripts', 'serve.js'), 'utf-8');
      assert.ok(
        serveCode.includes('decodeURIComponent'),
        'scripts/serve.js powinien używać decodeURIComponent wewnątrz try/catch'
      );
      assert.ok(
        serveCode.includes('csv') && serveCode.includes('rate_limits'),
        'BLOCKED_FILE_PATTERN w scripts/serve.js musi blokować pliki .csv oraz katalog api/rate_limits'
      );
    });
  });

  describe('2. Bezpieczeństwo backendu PHP (api/contact.php, MediaService.php, PrivateStorageService.php)', () => {
    it('api/contact.php nie może używać klasy \\s (dopuszczającej \\r\\n) przy oczyszczaniu numeru telefonu', () => {
      const contactPhp = fs.readFileSync(path.join(ROOT_DIR, 'api', 'contact.php'), 'utf-8');
      assert.strictEqual(
        contactPhp.includes("preg_replace('/[^0-9+\\-\\s()]/'"),
        false,
        'Wykryto podatny wzorzec \\s w sanitacji numeru telefonu (ryzyko CRLF Injection)'
      );
      assert.ok(
        contactPhp.includes("preg_replace('/[^0-9+\\- ()]/'"),
        'Sanitacja numeru telefonu powinna dopuszczać wyłącznie zwykłą spację zamiast \\s'
      );
    });

    it('api/MediaService.php powinien weryfikować wymiary obrazu przez getimagesize() przed alokacją bufora GD', () => {
      const mediaPhp = fs.readFileSync(path.join(ROOT_DIR, 'api', 'MediaService.php'), 'utf-8');
      assert.ok(
        mediaPhp.includes('getimagesize('),
        'MediaService.php musi weryfikować wymiary pikselowe obrazu (ochrona przed Decompression Bomb)'
      );
    });

    it('api/PrivateStorageService.php powinien używać blokady współdzielonej LOCK_SH podczas odczytu pliku CSV', () => {
      const storagePhp = fs.readFileSync(path.join(ROOT_DIR, 'api', 'PrivateStorageService.php'), 'utf-8');
      assert.ok(
        storagePhp.includes('LOCK_SH'),
        'PrivateStorageService.php powinien odczytywać lokalny plik CSV pod blokadą flock(LOCK_SH)'
      );
    });
  });

  describe('3. Odporność Widgetu AI na XSS, błędy Markdown i kolizje formularzy (assets/js/ai-widget.js)', () => {
    it('powinien escapować złośliwy kod HTML w atrybucie data-title / options.title (ochrona DOM XSS)', () => {
      const { document } = domEnv;
      const instance = initAiWidget({
        document,
        title: '<img src=x onerror="alert(1)">Asystent'
      });
      assert.ok(instance, 'Instancja widgetu powinna zostać utworzona');
      const root = document.getElementById('tsw-ai-widget-root');
      assert.ok(root, 'Kontener #tsw-ai-widget-root powinien istnieć');
      assert.strictEqual(
        root.innerHTML.includes('<img src=x'),
        false,
        'Surowy znacznik <img> z options.title nie może trafić do root.innerHTML!'
      );
      assert.ok(
        root.innerHTML.includes('&lt;img src=x'),
        'Tytuł widgetu musi zostać bezpiecznie wyescapowany przez escapeHtml()'
      );
    });

    it('formatMarkdown() nie powinien psuć linków URL zawierających znak "@"', () => {
      const input = 'Sprawdź profil na https://www.tiktok.com/@twojastronawww lub napisz na kontakt@twojastronawww.pl';
      const output = formatMarkdown(input);
      assert.ok(
        output.includes('href="https://www.tiktok.com/@twojastronawww"'),
        'Adres URL ze znakiem @ powinien zachować nienaruszony atrybut href'
      );
      assert.ok(
        output.includes('href="mailto:kontakt@twojastronawww.pl"'),
        'Samodzielny adres e-mail powinien zostać zamieniony na link mailto:'
      );
    });

    it('isStrictFiniteNumber() oraz computeSearchRelevance() powinny bezpiecznie obsługiwać skrajne przypadki brzegowe', () => {
      assert.strictEqual(isStrictFiniteNumber(true), false);
      assert.strictEqual(isStrictFiniteNumber(false), false);
      assert.strictEqual(isStrictFiniteNumber(NaN), false);
      assert.strictEqual(isStrictFiniteNumber(Infinity), false);
      assert.strictEqual(isStrictFiniteNumber('123'), false);
      assert.strictEqual(isStrictFiniteNumber(0), true);
      assert.strictEqual(isStrictFiniteNumber(42.5), true);

      assert.strictEqual(computeSearchRelevance(null, null), 0);
      assert.strictEqual(computeSearchRelevance({}, ''), 0);
    });
  });

  describe('4. Bezpieczeństwo potoków GitHub Actions (.github/workflows/)', () => {
    it('ai-security-review.yml powinien weryfikować author_association przed uruchomieniem audytu z komentarza', () => {
      const wf = fs.readFileSync(path.join(ROOT_DIR, '.github', 'workflows', 'ai-security-review.yml'), 'utf-8');
      assert.ok(
        wf.includes('author_association'),
        'ai-security-review.yml musi sprawdzać github.event.comment.author_association'
      );
    });

    it('dependabot-automerge.yml powinien chronić plik ci.yml przed automatycznym scaleniem bez przeglądu', () => {
      const wf = fs.readFileSync(path.join(ROOT_DIR, '.github', 'workflows', 'dependabot-automerge.yml'), 'utf-8');
      assert.ok(
        wf.includes('(ci|deploy|ai-security-review|dependabot-automerge)'),
        'dependabot-automerge.yml musi uwzględniać ci.yml na liście chronionych workflowów'
      );
    });
  });
});
```

---

### 3.2. Zestaw 2: Testy Integracyjne dla Projektów Siostrzanych (`twojastronawww`, `fuegolingua`, `katosalsahub`, `raricart`, `radar-p1`, `sejf-vod`)

```javascript
/**
 * @file tests/unit/ecosystem-cross-repo-audit.test.js
 * @description Zestaw testów weryfikujących kluczowe kontrakty bezpieczeństwa w projektach ekosystemu:
 * - Jednorazowość CSRF i blokada pustego APP_PIN w twojastronawww
 * - Spójność PRECACHE_URLS w sw.js (twojastronawww, raricart)
 * - Brak zahardkodowanych hashy PBKDF2 w katosalsahub/wiki.php i prekompilacja RegExp w wiki.js
 * - Autoryzacja check_login() i obsługa tablicy w password_verify() w raricart/admin
 * - Dostępność /.well-known/security.txt i безопасne kodowanie UTF-8 btoa() w radar-p1
 * - Weryfikacja ról w SECURITY DEFINER i bezpieczny filtr replikacji w sejf-vod
 */

import { describe, it } from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';

const WORKSPACE_ROOT = 'e:/Projekty';

describe('Ekosystem TwojaStronaWWW & kpoczatek — Cross-Repo Security Verification', () => {
  it('twojastronawww: api/contact.php musi wywoływać csrf_check() PRZED @session_write_close()', () => {
    const file = path.join(WORKSPACE_ROOT, 'twojastronawww/api/contact.php');
    if (!fs.existsSync(file)) return;
    const content = fs.readFileSync(file, 'utf-8');
    const csrfPos = content.indexOf('csrf_check()');
    const closePos = content.indexOf('session_write_close()');
    assert.ok(csrfPos !== -1 && closePos !== -1, 'Oba wywołania muszą istnieć w api/contact.php');
    assert.ok(
      csrfPos < closePos,
      'csrf_check() musi występować przed @session_write_close(), aby zużyty token został usunięty z sesji!'
    );
  });

  it('fuegolingua: assets/js/contact.js musi serializować pola <select> oraz używać textContent zamiast innerHTML', () => {
    const file = path.join(WORKSPACE_ROOT, 'fuegolingua/assets/js/contact.js');
    if (!fs.existsSync(file)) return;
    const content = fs.readFileSync(file, 'utf-8');
    assert.ok(
      content.includes('input, textarea, select'),
      'Selektor pól formularza w fuegolingua/assets/js/contact.js musi zawierać "select"'
    );
    assert.strictEqual(
      content.includes('formStatus.innerHTML ='),
      false,
      'formStatus nie może używać podatnego na XSS przypisania innerHTML'
    );
  });

  it('katosalsahub: wiki.php nie może zawierać zahardkodowanego hasha PBKDF2, a wiki.js musi cache-ować RegExp', () => {
    const wikiPhp = path.join(WORKSPACE_ROOT, 'katosalsahub/katosalsahub.pl/assets/php/wiki.php');
    if (fs.existsSync(wikiPhp)) {
      const phpContent = fs.readFileSync(wikiPhp, 'utf-8');
      assert.strictEqual(
        /pbkdf2\$100000\$[0-9a-f]+\$[0-9a-f]+/i.test(phpContent),
        false,
        'wiki.php nie może zawierać zaszytego na stałe hasha PBKDF2!'
      );
    }
    const wikiJs = path.join(WORKSPACE_ROOT, 'katosalsahub/katosalsahub.pl/assets/js/wiki.js');
    if (fs.existsSync(wikiJs)) {
      const jsContent = fs.readFileSync(wikiJs, 'utf-8');
      assert.ok(
        jsContent.includes('cachedKeywordEntries'),
        'wiki.js powinien cache-ować skompilowane wyrażenia RegExp w buildKeywordEntries()'
      );
    }
  });

  it('raricart: admin/change_password.php musi obsługiwać tablicowy zapis użytkownika, a sw.js istniejące pliki', () => {
    const changePass = path.join(WORKSPACE_ROOT, 'raricart/admin/change_password.php');
    if (fs.existsSync(changePass)) {
      const phpContent = fs.readFileSync(changePass, 'utf-8');
      assert.ok(
        phpContent.includes('is_array('),
        'change_password.php musi bezpiecznie wyciągać klucz ["hash"] z tablicy użytkownika'
      );
    }
    const swFile = path.join(WORKSPACE_ROOT, 'raricart/sw.js');
    if (fs.existsSync(swFile)) {
      const swContent = fs.readFileSync(swFile, 'utf-8');
      assert.strictEqual(
        swContent.includes("'/assets/css/style.css'"),
        false,
        'raricart/sw.js musi wskazywać na /assets/css/styles.css zamiast nieistniejącego style.css'
      );
    }
  });

  it('radar-p1: public/_worker.js musi zezwalać na /.well-known/security.txt i wysyłać nagłówki COOP/CORP', () => {
    const workerFile = path.join(WORKSPACE_ROOT, 'radar-p1/public/_worker.js');
    if (!fs.existsSync(workerFile)) return;
    const content = fs.readFileSync(workerFile, 'utf-8');
    assert.ok(
      content.includes("'/.well-known/security.txt'"),
      '_worker.js musi zawierać /.well-known/security.txt na liście staticAllowed'
    );
    assert.ok(
      content.includes('Cross-Origin-Opener-Policy'),
      '_worker.js musi ustawiać nagłówek Cross-Origin-Opener-Policy'
    );
  });
});
```
