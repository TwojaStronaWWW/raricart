# GŁĘBOKI AUDYT BEZPIECZEŃSTWA ZERO-TRUST, WYDAJNOŚCI I JAKOŚCI KODU (`DEEP_AUDIT_REPORT.md`)

> **Zakres audytu:** Cały ekosystem projektów **TwojaStronaWWW** oraz **kpoczatek (`kpoczatek-dev`)** w przestrzeni roboczej `e:\Projekty` (10 repozytoriów, **769 plików źródłowych, konfiguracyjnych i testowych** przeanalizowanych plik po pliku).  
> **Standardy odniesienia:** ISO/IEC 27001, OWASP ASVS 4.0.3, Evergreen Web Standards Radar (`[REF-01]`–`[REF-08]`), Platform-First & Zero-Bloat Architecture (`AGENTS.md` / `GEMINI.md`).

---

## Spis Treści i Inwentarz Przeskanowanych Repozytoriów

| # | Repozytorium (Ścieżka lokalna) | Remote GitHub | Liczba plików | Stos technologiczny |
|---|---|---|---|---|
| **1** | `e:\Projekty\_site-template` | `TwojaStronaWWW/_site-template` | 61 | HTML5, Vanilla CSS, ES6 JS (`main.js`, `ai-widget.js`), PHP 8.3 (`contact.php`, `R2Client.php`), `node:test`, Playwright |
| **2** | `e:\Projekty\twojastronawww` | `TwojaStronaWWW/twojastronawww` | 75 (+34 runtime) | HTML5, Vanilla CSS, ES6 JS, PHP 8.3 (`bootstrap.php`, `contact.php`, `admin.php`, `export-leads.php`), PWA (`sw.js`) |
| **3** | `e:\Projekty\VPS` | `TwojaStronaWWW/devops-stack` | 77 | Docker Compose (9 stosów), PHP 8.3 (`chat-router.php`, `router-endpoints.php`, SQLite FTS5), Caddy, Prometheus, Grafana |
| **4** | `e:\Projekty\hd-osticket-helpdesk` | `TwojaStronaWWW/hd-osticket-helpdesk` | 14 | PHP 8.x (`config/ticket_hook.php`), osTicket API Gateway, Bash (`backup.sh`), Apache `.htaccess` |
| **5** | `e:\Projekty\fuegolingua` | `TwojaStronaWWW/fuegolingua` | 87 | HTML5 (`index.html`, `cennik.html`), ES6 JS (`main.js`, `contact.js`, `ai-widget.js`), PHP 8.3 + Cloudflare R2 |
| **6** | `e:\Projekty\katosalsahub` | `TwojaStronaWWW/katosalsahub` | 99 (+4 robocze) | HTML5, ES6 JS (`wiki.js`, `schedule.js`, `radio.js`), PHP 8.3 (`wiki.php`, `radio.php`, `contact.php`), Node.js ETL |
| **7** | `e:\Projekty\Moje-CV` | `TwojaStronaWWW/Moje-CV` | 45 | Statyczny HTML5 (PL/EN), Vanilla CSS + Print CSS (A4), ES6 JS, Playwright PDF Generator (`generate-pdf.js`) |
| **8** | `e:\Projekty\raricart` | `TwojaStronaWWW/raricart` | 128 | PHP 8.3 SSR (`index.php`, `pakiety.php`, `parts/*.php`), Panel CMS (`admin/*.php`), Cloudflare R2, ES6 Modules |
| **9** | `e:\Projekty\radar-p1` | `TwojaStronaWWW/radar-p1` | 56 | Cloudflare Pages + `_worker.js`, Vanilla JS SPA (`app.js`, `charts.js`, `config.js`), Custom SVG Engine |
| **10** | `e:\Projekty\sejf-vod` | `kpoczatek-dev/vod-dance-mvp` | 167 | Next.js 16 (App Router, React 19, TypeScript), Supabase (PostgreSQL + RLS), Cloudflare R2, VPS FFmpeg HLS AES-128 |

---

## Tabela Analizy Epistemicznej (Analysis Table)

| Założenie / Teza | Źródło błędu / Ryzyko | Stopień pewności (Niski/Średni/Wysoki) | Mitygacja / Lepszy model |
|---|---|---|---|
| Jednorazowość tokenów CSRF w `twojastronawww/api/contact.php` chroni przed atakami Replay | W `twojastronawww/api/contact.php:44` wywołano `@session_write_close()` przed `csrf_check()` (linia `129`), przez co `unset($_SESSION['csrf_token'])` nie zapisuje się na dysku i ten sam token działa wielokrotnie | **Wysoki** | Przeniesienie `@session_write_close()` bezpośrednio po `csrf_check()` i walidacji czasu sesji formularza |
| Eksport bazy leadów w `twojastronawww/api/export-leads.php` wymaga znajomości kodu PIN | Gdy brak `.env`, `APP_PIN` w `api/bootstrap.php:26` przyjmuje `''`, a `hash_equals('', $_GET['pin'] ?? '')` zwraca `true` dla pustego zapytania, pozwalając pobrać pełną bazę CSV bez uwierzytelnienia | **Wysoki** | Blokada Fail-Closed (`APP_PIN === ''`), wymuszenie sesji zalogowanego administratora lub metody `POST` z `rate_limit()` i `flock(LOCK_SH)` |
| Kontenery GitHub Actions Runner na VPS są odizolowane od hosta | W `VPS/compose/runners.yml:38, 174` kontenery `runner-unified` i `runner-web` montują `/var/run/docker.sock` w trybie zapisu (`RW`) bez `:ro`, co daje pełny dostęp `root` na hoście VPS | **Wysoki** | Dodanie sufiksu `:ro` do `/var/run/docker.sock` we wszystkich runnerach oraz dedykowane tokeny `RUNNER_*_TOKEN` |
| Funkcje RPC `SECURITY DEFINER` w `sejf-vod` są dostępne wyłącznie dla Super-Admina | W `sejf-vod/supabase/migrations/20260819164500_*.sql:52-106` funkcje `get_admin_platform_users()` i `get_admin_platform_videos()` mają `GRANT EXECUTE TO authenticated` i brak sprawdzenia roli wewnątrz SQL | **Wysoki** | Wewnętrzny strażnik `IF NOT EXISTS (SELECT 1 FROM profiles WHERE id = auth.uid() AND role = 'admin') THEN RAISE EXCEPTION` oraz `REVOKE EXECUTE FROM PUBLIC, anon` |
| Kontrakt JSON między Next.js a transkoderem FFmpeg na VPS w `sejf-vod` jest spójny | `src/actions/videos.ts:233` wysyła `{r2ObjectKey, callbackUrl}`, a `vps-transcoder/server.mjs:56` wymaga `{sourceUrl, r2Prefix, webhookUrl}` i nigdy nie wysyła segmentów `.ts`/`.m3u8` do R2 przed usunięciem `workDir` | **Wysoki** | Ujednolicenie kontraktu JSON oraz dodanie pętli uploadu wygenerowanych plików HLS (`.m3u8` i `.ts`) do Cloudflare R2 przed czyszczeniem katalogu roboczego |
| Panel administracyjny i skrypty migracyjne w `raricart` i `katosalsahub` nie posiadają ukrytych furtek ani błędów typów PHP 8 | W `katosalsahub/assets/php/wiki.php:85, 422` zaszyto na stałe hash PBKDF2 sprawdzany przed `.env`; w `raricart/admin/migrate_users.php` brak `check_login()`, a `change_password.php:41` przekazuje tablicę do `password_verify()` rzucając `Fatal TypeError` | **Wysoki** | Usunięcie zahardkodowanego hasha z `wiki.php`, wymuszenie `check_login()` w `admin/*.php` oraz obsługa struktury tablicowej użytkownika w `change_password.php` |
| Serwery deweloperskie `scripts/serve.js` chronią pliki wrażliwe podczas tunelowania Cloudflare Quick Tunnel | W `_site-template/scripts/serve.js:47` regex nie blokuje `.csv` ani `api/rate_limits/*.json`; w `Moje-CV/scripts/serve.js:40` brak `try/catch` wokół `decodeURI()` zabija proces Node.js przy błędnym URL (`/%E0%A4%A`) | **Wysoki** | Rozszerzenie `BLOCKED_FILE_PATTERN` o `.csv`, `api/sessions`, `api/rate_limits` oraz owinięcie `decodeURIComponent`/`decodeURI` w `try/catch` |

---
---

# 1. REPOZYTORIUM: `e:\Projekty\_site-template` (`TwojaStronaWWW/_site-template`)

## 1.1. Bezpieczeństwo (Zero-Trust / ISO 27001 / OWASP)

### `[HIGH]` ST-SEC-01: Wyciek plików leadów `.csv` oraz liczników `api/rate_limits/*.json` przez `scripts/serve.js` i Cloudflare Quick Tunnel
- **Ścieżka pliku:** [scripts/serve.js](file:///e:/Projekty/_site-template/scripts/serve.js#L47-L70)
- **Numery linii:** `47`, `49-70`
- **Opis podatności:** Wyrażenie `BLOCKED_FILE_PATTERN = /(^|[\/\\])(\.env.*|\.htaccess|\.git.*|.*\.(php|bak|tmp|log|sql|sh|swp))$/i;` nie blokuje plików `.csv` (`api/leads_YYYY-MM.csv`), `.lock`, `.gitleaks.toml`, ani katalogów `api/sessions/*` i `api/rate_limits/*.json`. Ponieważ serwer Node.js nie interpretuje plików `.htaccess`, uruchomienie publicznego tunelu `npm run preview:share` (`cloudflared tunnel --url http://localhost:3000`) wystawia plik `GET /api/leads_2026-10.csv` na publiczny odczyt bez autoryzacji. Dodatkowo linia `53` używa `decodeURI()` zamiast `decodeURIComponent()`, przez co znaki `%2f` i `%5c` nie są dekodowane przed weryfikacją.
- **Gotowy blok kodu z poprawką:**
```javascript
// e:\Projekty\_site-template\scripts\serve.js (linie 46-66)
const BLOCKED_FILE_PATTERN =
  /(^|[\/\\])(\.env.*|\.htaccess|\.git.*|\.gitleaks\.toml|api[\/\\](sessions|rate_limits)([\/\\]|$)|.*\.(php|bak|tmp|log|sql|sh|swp|csv|lock))$/i;

const server = http.createServer((req, res) => {
  const rawUrl = (req.url || '/').split('?')[0].split('#')[0];
  let decodedPath;
  try {
    decodedPath = decodeURIComponent(rawUrl);
  } catch {
    res.writeHead(400, { 'Content-Type': 'text/plain; charset=utf-8' });
    res.end('400 Bad Request');
    return;
  }

  const normalizedUrlPath = decodedPath.replace(/\\/g, '/');
  if (
    normalizedUrlPath.includes('..') ||
    BLOCKED_FILE_PATTERN.test(normalizedUrlPath)
  ) {
    res.writeHead(403, { 'Content-Type': 'text/plain; charset=utf-8' });
    res.end('403 Forbidden: Access to sensitive file denied');
    return;
  }
```

### `[HIGH]` ST-SEC-02: Wstrzyknięcie znaków nowej linii (CRLF Header Injection) przez pole `$phone` w `api/contact.php`
- **Ścieżka pliku:** [api/contact.php](file:///e:/Projekty/_site-template/api/contact.php#L107-L280)
- **Numery linii:** `107`, `149`, `242`, `275-278`
- **Opis podatności:** W linii `107` numer telefonu jest filtrowany przez `preg_replace('/[^0-9+\-\s()]/', '', $phoneRaw)`. Klasa znaków `\s` w PCRE przepuszcza znaki `\r` (CR) oraz `\n` (LF)! Następnie zmienna `$phone` trafia bezpośrednio do `$baseSubject` (linia `149`, przekazywanego do `mail()` w linii `242`) oraz do nagłówków HTTP powiadomienia Ntfy.sh (`Title:` i `Actions:` w liniach `275`, `278`), co umożliwia wstrzyknięcie dodatkowych nagłówków HTTP/SMTP.
- **Gotowy blok kodu z poprawką:**
```php
// e:\Projekty\_site-template\api\contact.php (linia 107)
$phone   = preg_replace('/[^0-9+\- ()]/', '', $phoneRaw) ?: '-';
```

### `[HIGH]` ST-SEC-03: Bezwarunkowe zaufanie do nagłówka `HTTP_CF_CONNECTING_IP` (obejście Rate-Limitera przy bezpośrednim połączeniu do serwera Origin)
- **Ścieżka pliku:** [api/contact.php](file:///e:/Projekty/_site-template/api/contact.php#L48-L55) oraz [api/admin.php](file:///e:/Projekty/_site-template/api/admin.php#L40-L53)
- **Numery linii:** `api/contact.php:48`, `api/admin.php:40`
- **Opis podatności:** Kod pobiera adres IP klienta przez `$_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR']` bez weryfikacji, czy adres `REMOTE_ADDR` należy do zaufanych podsieci Cloudflare lub czy żądanie przeszło przez zaufany proxy. Jeśli serwer współdzielony (LH.pl) jest osiągalny bezpośrednio po adresie IP hostingu, atakujący może losować nagłówek `CF-Connecting-IP` w każdym żądaniu POST, całkowicie omijając `rate_limit('admin_login_' . $clientIp, 5, 300)` oraz `rate_limit('contact_' . $maskedIp, 5, 60)`.
- **Gotowy blok kodu z poprawką:**
```php
// Funkcja pomocnicza w api/rate-limit.php:
function resolve_trusted_client_ip(): string {
    $remoteAddr = trim((string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'));
    $cfIp = trim((string)($_SERVER['HTTP_CF_CONNECTING_IP'] ?? ''));
    // Ufaj HTTP_CF_CONNECTING_IP tylko gdy obecny jest nagłówek CF-Ray i poprawny format IP
    if ($cfIp !== '' && !empty($_SERVER['HTTP_CF_RAY']) && filter_var($cfIp, FILTER_VALIDATE_IP)) {
        return $cfIp;
    }
    return filter_var($remoteAddr, FILTER_VALIDATE_IP) ? $remoteAddr : '0.0.0.0';
}
```

### `[MEDIUM]` ST-SEC-04: Potencjalny DOM XSS przez nieescapowany atrybut `data-title` / `options.title` w `assets/js/ai-widget.js`
- **Ścieżka pliku:** [assets/js/ai-widget.js](file:///e:/Projekty/_site-template/assets/js/ai-widget.js#L1265-L1310)
- **Numery linii:** `1265`, `1303`, `1307`
- **Opis podatności:** Zmienna `title` pobrana z `scriptEl?.getAttribute('data-title') || options.title` (linia `1265`) jest wstrzykiwana bezpośrednio do szablonu `root.innerHTML` w liniach `1303` (`aria-label="Otwórz czat: ${title}"`) oraz `1307` (`<p class="tsw-ai-header__title">${title}</p>`) bez przepuszczenia przez `escapeHtml(title)`.
- **Gotowy blok kodu z poprawką:**
```javascript
// e:\Projekty\_site-template\assets\js\ai-widget.js (linie 1265-1266)
const rawTitle = scriptEl?.getAttribute('data-title') || options.title || profile.title || DEFAULT_CONFIG.title;
const title = escapeHtml(rawTitle);
```

### `[MEDIUM]` ST-SEC-05: Brak weryfikacji `author_association` w `.github/workflows/ai-security-review.yml` oraz pominięcie `ci.yml` w `dependabot-automerge.yml`
- **Ścieżka pliku:** [.github/workflows/ai-security-review.yml](file:///e:/Projekty/_site-template/.github/workflows/ai-security-review.yml#L36-L40) oraz [.github/workflows/dependabot-automerge.yml](file:///e:/Projekty/_site-template/.github/workflows/dependabot-automerge.yml#L50-L90)
- **Numery linii:** `ai-security-review.yml:36-40`, `dependabot-automerge.yml:50, 90`
- **Opis podatności:**
  1. W `ai-security-review.yml:36-40` wyzwalacz `issue_comment` sprawdza jedynie obecność frazy `@claude review` lub `@ai review`, nie weryfikując `github.event.comment.author_association`. W publicznym repozytorium dowolny użytkownik GitHuba może wielokrotnie komentować PR, wyczerpując budżet tokenów API Anthropic/VPS.
  2. W `dependabot-automerge.yml:50` i `90` filtr krytycznych workflowów `\.github/workflows/(deploy|ai-security-review|dependabot-automerge)` pomija plik `ci.yml`, pozwalając na automatyczne scalenie zmian w głównym potoku testowym bez przeglądu człowieka.
- **Gotowy blok kodu z poprawką:**
```yaml
# 1. .github/workflows/ai-security-review.yml (linie 36-42)
    if: |
      (github.event_name == 'pull_request') ||
      (
        github.event_name == 'issue_comment' &&
        github.event.issue.pull_request &&
        contains(['OWNER', 'MEMBER', 'COLLABORATOR'], github.event.comment.author_association) &&
        (contains(github.event.comment.body, '@claude review') || contains(github.event.comment.body, '@ai review'))
      )
```
```yaml
# 2. .github/workflows/dependabot-automerge.yml (linie 50 oraz 90)
if echo "$CHANGED_FILES" | grep -qE '^(\.github/workflows/(ci|deploy|ai-security-review|dependabot-automerge)|api/|\.htaccess|sw\.js)'; then
```

---

## 1.2. Wydajność i Pamięć (Performance, Concurrency & Memory)

### `[MEDIUM]` ST-PERF-01: Synchroniczny podwójny upload całego pliku CSV do Cloudflare R2 wewnątrz `save_lead()` blokujący odpowiedź formularza
- **Ścieżka pliku:** [api/leads-store.php](file:///e:/Projekty/_site-template/api/leads-store.php#L153-L167) oraz [api/PrivateStorageService.php](file:///e:/Projekty/_site-template/api/PrivateStorageService.php#L47-L76)
- **Numery linii:** `api/leads-store.php:153-167`, `api/PrivateStorageService.php:67-69`
- **Opis problemu:** Przy każdym wysłaniu formularza kontaktowego funkcja `save_lead()` po zwolnieniu blokady `flock` odczytuje cały plik `leads_YYYY-MM.csv` do pamięci i wykonuje **dwa synchroniczne żądania HTTP PUT** do Cloudflare R2 (`leads/leady.csv` oraz `leads/archive/leady_YYYY-MM-DD.csv`, każde z timeoutem do 30s), zanim `api/contact.php` przejdzie do osTicket, Ntfy.sh i zwrócenia odpowiedzi JSON użytkownikowi. Ponadto w `PrivateStorageService::backupLeadsFile()` (linia `52`) plik CSV jest odczytywany przez `file_get_contents()` bez blokady współdzielonej `flock(LOCK_SH)`.
- **Gotowy blok kodu z poprawką:**
```php
// e:\Projekty\_site-template\api\PrivateStorageService.php (linie 50-58)
$fp = @fopen($localCsvPath, 'rb');
if ($fp === false) {
    return false;
}
$content = false;
if (@flock($fp, LOCK_SH)) {
    $content = stream_get_contents($fp);
    @flock($fp, LOCK_UN);
}
@fclose($fp);
if ($content === false || $content === '') {
    return false;
}
```

### `[MEDIUM]` ST-PERF-02: Ryzyko Decompression Bomb (OOM) w `MediaService.php` oraz niewymuszony `MAX_RESPONSE_BYTES` w `R2Client.php`
- **Ścieżka pliku:** [api/MediaService.php](file:///e:/Projekty/_site-template/api/MediaService.php#L72-L114) oraz [api/R2Client.php](file:///e:/Projekty/_site-template/api/R2Client.php#L12-L226)
- **Numery linii:** `api/MediaService.php:72-114`, `api/R2Client.php:12, 202-226`
- **Opis problemu:**
  1. `MediaService::processAndUpload()` sprawdza rozmiar pliku w bajtach (`<= 15 MB`), ale przed wywołaniem `imagecreatefrompng()` / `imagecreatefromjpeg()` nie sprawdza wymiarów pikselowych obrazu (`getimagesize()`). Skompresowany plik PNG o wymiarach `20000x20000 px` ma poniżej 1 MB na dysku, ale po rozpakowaniu do bufora GD zajmuje `~1.6 GB RAM`, natychmiast zabijając proces PHP (`Fatal error: Allowed memory size exhausted`).
  2. W `R2Client.php:12` zadeklarowano stałą `private const MAX_RESPONSE_BYTES = 10485760;`, która nigdy nie jest używana w `sendRequest()` (brak `CURLOPT_WRITEFUNCTION`).
- **Gotowy blok kodu z poprawką:**
```php
// e:\Projekty\_site-template\api\MediaService.php (przed createImageResource w linii 72)
$imgInfo = @getimagesize($tmpPath);
if ($imgInfo === false || ($imgInfo[0] ?? 0) > 8192 || ($imgInfo[1] ?? 0) > 8192 || (($imgInfo[0] ?? 0) * ($imgInfo[1] ?? 0)) > 25000000) {
    throw new \RuntimeException('Wymiary obrazu przekraczają bezpieczny limit (maks. 8192x8192 px / 25 MP).');
}
```

### `[MEDIUM]` ST-PERF-03: Brak limitu rozmiaru słownika `responseCache.entries` w `localStorage` w `assets/js/ai-widget.js`
- **Ścieżka pliku:** [assets/js/ai-widget.js](file:///e:/Projekty/_site-template/assets/js/ai-widget.js#L904-L923)
- **Numery linii:** `904-923`
- **Opis problemu:** Funkcja `setCachedReply()` dopisuje kolejne odpowiedzi modelu do obiektu `responseCache.entries` bez górnego limitu liczby kluczy (FIFO/LRU eviction). Przy długotrwałym użytkowaniu obiekt rozrasta się aż do przekroczenia `QuotaExceededError` w `localStorage`.
- **Gotowy blok kodu z poprawką:**
```javascript
// e:\Projekty\_site-template\assets\js\ai-widget.js (wewnątrz setCachedReply, przed zapisem nowego klucza w linii 914)
const MAX_CACHE_ENTRIES = 50;
const existingKeys = Object.keys(responseCache.entries);
if (existingKeys.length >= MAX_CACHE_ENTRIES) {
  delete responseCache.entries[existingKeys[0]];
}
```

---

## 1.3. Jakość i Dług Technologiczny (Code Quality, SOLID & Error Handling)

### `[MEDIUM]` ST-QUAL-01: Literówka w identyfikatorze formularza (`tsw-ai-form` zamiast `tswAiForm`) oraz błąd zagnieżdżania linków w `formatMarkdown()` w `assets/js/ai-widget.js`
- **Ścieżka pliku:** [assets/js/ai-widget.js](file:///e:/Projekty/_site-template/assets/js/ai-widget.js#L364-L373) oraz [assets/js/ai-widget.js](file:///e:/Projekty/_site-template/assets/js/ai-widget.js#L1861)
- **Numery linii:** `364-373`, `1318`, `1861`
- **Opis problemu:**
  1. W linii `1318` formularz czatu AI ma identyfikator `id="tswAiForm"`, natomiast globalny nasłuchiwacz `submit` w linii `1861` sprawdza `if (form && form.id !== 'tsw-ai-form' && !form.hasAttribute('data-tsw-ignore'))`. Przez tę literówkę każde wysłanie wiadomości w czacie AI jest błędnie traktowane jako wysłanie zewnętrznego formularza kontaktowego i wywołuje `clearDraft()`!
  2. W `formatMarkdown()` (linie `364-373`) zamiana adresów URL (`https?://...`) wykonuje się przed zamianą adresów e-mail. Jeśli adres URL zawiera znak `@` (np. `https://example.com/@profil` lub `https://user@example.com`), drugi regex (`mailto:`) dopasowuje tekst wewnątrz wygenerowanego atrybutu `href="..."`, tworząc uszkodzony, zagnieżdżony znacznik `<a href="...<a href="mailto:...">`.
- **Gotowy blok kodu z poprawką:**
```javascript
// 1. e:\Projekty\_site-template\assets\js\ai-widget.js (linia 1861)
if (form && form.id !== 'tswAiForm' && form.id !== 'tsw-ai-form' && !form.hasAttribute('data-tsw-ignore')) {
```
```javascript
// 2. e:\Projekty\_site-template\assets\js\ai-widget.js (linia 368 — wykluczenie dopasowania e-maila wewnątrz istniejących atrybutów/tagów)
html = html.replace(
  /(?<![">/@\w.-])([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/g,
  '<a href="mailto:$1" class="tsw-ai-link">$1</a>'
);
```

### `[MEDIUM]` ST-QUAL-02: Zduplikowana, niepełna definicja `get_env_secret()` w `api/contact.php`, błąd zapisu CSV w `docker-compose.yml` i uprawnienia `0777` w `scripts/migrate-to-r2.php`
- **Ścieżka pliku:** [api/contact.php](file:///e:/Projekty/_site-template/api/contact.php#L13-L20), [docker-compose.yml](file:///e:/Projekty/_site-template/docker-compose.yml#L14-L16), [scripts/migrate-to-r2.php](file:///e:/Projekty/_site-template/scripts/migrate-to-r2.php#L22)
- **Numery linii:** `api/contact.php:15-19`, `docker-compose.yml:14-16`, `scripts/migrate-to-r2.php:22`
- **Opis problemu:**
  1. `api/contact.php` w linii `13` ładuje `require_once __DIR__ . '/leads-store.php';`, który już definiuje pełną funkcję `get_env_secret()` (czytającą zarówno zmienne środowiskowe, jak i plik `.env`). Mimo to w liniach `15-19` `api/contact.php` zawiera zduplikowaną, okrojoną definicję `if (!function_exists('get_env_secret'))` (martwy kod).
  2. `docker-compose.yml:14-16` montuje główny katalog jako `.:/var/www/html:ro`, udostępniając do zapisu (`:rw`) tylko `api/sessions` i `api/rate_limits`. Przez to zapis leadów `api/leads_YYYY-MM.csv` w lokalnym kontenerze Docker kończy się błędem `Read-only file system`.
  3. `scripts/migrate-to-r2.php:22` tworzy katalog z nadmiarowymi uprawnieniami `mkdir($outputDir, 0777, true)` zamiast `0755`.
- **Gotowy blok kodu z poprawką:**
```php
// e:\Projekty\_site-template\scripts\migrate-to-r2.php (linia 22)
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}
```

---
---

# 2. REPOZYTORIUM: `e:\Projekty\twojastronawww` (`TwojaStronaWWW/twojastronawww`)

## 2.1. Bezpieczeństwo (Zero-Trust / ISO 27001 / OWASP)

### `[CRITICAL]` TSW-SEC-01: Całkowite unieszkodliwienie jednorazowości tokenów CSRF (Token Replay) przez przedwczesne `@session_write_close()` w `api/contact.php`
- **Ścieżka pliku:** [api/contact.php](file:///e:/Projekty/twojastronawww/api/contact.php#L44-L129) oraz [api/bootstrap.php](file:///e:/Projekty/twojastronawww/api/bootstrap.php#L43-L55)
- **Numery linii:** `api/contact.php:44`, `api/contact.php:129`, `api/bootstrap.php:51`
- **Opis podatności:** W `api/contact.php` w linii `44` wywołano `@session_write_close()`, natomiast wywołanie `csrf_check()` znajduje się dopiero w linii `129`! Wewnątrz `csrf_check()` (`api/bootstrap.php:51`) wykonywane jest `unset($_SESSION['csrf_token'])`, które ma unieważnić zużyty token CSRF. Ponieważ sesja została już zamknięta na linii `44`, zmiana w tablicy `$_SESSION` odbywa się wyłącznie w pamięci RAM i **nigdy nie zostaje zapisana na dysku** (`api/sessions/sess_*`). Ten sam token CSRF może być używany wielokrotnie bez ograniczeń (co potwierdza nieprzechodzący Krok 7 w `tests/test_live_csrf.php:147`).
- **Gotowy blok kodu z poprawką:**
```php
// e:\Projekty\twojastronawww\api\contact.php
// 1. Usunąć @session_write_close(); z linii 44.
// 2. Umieścić @session_write_close(); bezpośrednio po linii 129 (po csrf_check()):
csrf_check();
@session_write_close();
```

### `[CRITICAL]` TSW-SEC-02: Nieautoryzowane pobranie pełnej bazy leadów CSV przy pustym `ADMIN_PIN` (`hash_equals('', '')`) oraz wyciek PIN-u w URL w `api/export-leads.php`
- **Ścieżka pliku:** [api/export-leads.php](file:///e:/Projekty/twojastronawww/api/export-leads.php#L7-L48) oraz [api/bootstrap.php](file:///e:/Projekty/twojastronawww/api/bootstrap.php#L26)
- **Numery linii:** `api/bootstrap.php:26`, `api/export-leads.php:7-17, 35-48`
- **Opis podatności:**
  1. W `api/bootstrap.php:26` stała `APP_PIN` jest definiowana jako `define('APP_PIN', $_ENV['ADMIN_PIN'] ?? '');`. Jeśli zmienna `ADMIN_PIN` nie jest ustawiona w `.env`, `APP_PIN` ma wartość `''`.
  2. W `api/export-leads.php:8-11` kod sprawdza: `$pin = $_GET['pin'] ?? ''; if (!hash_equals(APP_PIN, $pin))`. Dla pustego parametru `GET /api/export-leads.php` wyrażenie `hash_equals('', '')` zwraca **`true`**, pozwalając każdemu niezalogowanemu użytkownikowi z internetu pobrać pełną bazę klientów CSV!
  3. Ponadto przekazywanie tajnego PIN-u w parametrze `GET ?pin=...` zapisuje go w historii przeglądarki i logach dostępowych serwera HTTP, endpoint nie posiada żadnego `rate_limit()`, a odczyt pliku CSV (linia `44`) odbywa się bez blokady `flock($fp, LOCK_SH)`.
- **Gotowy blok kodu z poprawką:**
```php
// e:\Projekty\twojastronawww\api\export-leads.php (linie 1-50)
<?php
require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method Not Allowed');
}

$clientIp = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
if (!rate_limit('export_leads_' . mask_ip($clientIp), 5, 300)) {
    http_response_code(429);
    die('Too Many Requests');
}

$isSessionAuthed = !empty($_SESSION['admin_logged_in']);
$pin = (string)($_POST['pin'] ?? '');
$isPinAuthed = (APP_PIN !== '' && $pin !== '' && hash_equals(APP_PIN, $pin));

if (!$isSessionAuthed && !$isPinAuthed) {
    http_response_code(403);
    die('Brak dostępu.');
}
@session_write_close();
```

### `[HIGH]` TSW-SEC-03: Obejście walidacji domeny `source_url` (`strpos`) w `api/contact.php` oraz brak ochrony brute-force i flag ciasteczek sesji w `api/admin.php` / `api/bootstrap.php`
- **Ścieżka pliku:** [api/contact.php](file:///e:/Projekty/twojastronawww/api/contact.php#L117), [api/admin.php](file:///e:/Projekty/twojastronawww/api/admin.php#L10-L39), [api/bootstrap.php](file:///e:/Projekty/twojastronawww/api/bootstrap.php#L9-L14)
- **Numery linii:** `api/contact.php:117`, `api/admin.php:10-39`, `api/bootstrap.php:9-14`
- **Opis podatności:**
  1. W `api/contact.php:117` walidacja `strpos($sourceUrl, 'twojastronawww.pl') !== false` daje się obejść przez `https://twojastronawww.pl.evil.com` lub `https://evil.com/?q=twojastronawww.pl`.
  2. W `api/admin.php:12` użyto `hash_equals(APP_PIN, $pin)` bez sprawdzenia `APP_PIN !== ''`, a zamiast plikowego `rate_limit()` zastosowano `sleep(2)` (linia `17`), który blokuje workery PHP-FPM przy ataku DoS.
  3. W `api/bootstrap.php:12` `session_start()` jest wywoływane bez `session_set_cookie_params(['httponly' => true, 'secure' => ..., 'samesite' => 'Strict'])`.
- **Gotowy blok kodu z poprawką:**
```php
// 1. e:\Projekty\twojastronawww\api\contact.php (linia 117)
$parsedHost = strtolower((string)parse_url($sourceUrl, PHP_URL_HOST));
$isAllowedHost = ($parsedHost === 'twojastronawww.pl' || $parsedHost === 'www.twojastronawww.pl' || $parsedHost === 'localhost');
if (!filter_var($sourceUrl, FILTER_VALIDATE_URL) || !$isAllowedHost) {
    $sourceUrl = 'https://twojastronawww.pl';
}
```
```php
// 2. e:\Projekty\twojastronawww\api\bootstrap.php (linie 9-15)
if (session_status() === PHP_SESSION_NONE) {
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? 80) == 443);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}
```

### `[HIGH]` TSW-SEC-04: Wyciek danych produkcyjnych i `node_modules` w `.github/workflows/deploy.yml` oraz żywe pliki leadów/sesji na dysku roboczym
- **Ścieżka pliku:** [.github/workflows/deploy.yml](file:///e:/Projekty/twojastronawww/.github/workflows/deploy.yml#L120-L148)
- **Numery linii:** `120-148`
- **Opis podatności:** Krok `Prepare clean deployment directory` w `deploy.yml:136` usuwa jedynie `.git`, `.github`, `.gemini`, `tests`, `README.md`, `ROADMAP.md` i `.env*`, po czym wgrywa całe `./*` po SFTP na produkcję! Nie usuwa `node_modules/`, `smoke-diagnostics/`, `scripts/`, `package.json`, `api/leads_*.csv`, `api/sessions/sess_*` ani `api/rate_limits/*.json`! Co więcej, w lokalnym katalogu `e:\Projekty\twojastronawww\api\` znajdują się obecnie żywe pliki runtime: `api/leads_2026-02.csv`, 30 plików `api/sessions/sess_*` oraz 3 pliki `api/rate_limits/rate_*.json`.
- **Gotowy blok kodu z poprawką:**
```yaml
# e:\Projekty\twojastronawww\.github\workflows\deploy.yml (linie 136-140)
      - name: Prepare clean deployment directory
        run: |
          rm -rf .git .github .gemini .githooks tests scripts node_modules smoke-diagnostics
          rm -f README.md ROADMAP.md BLUEPRINT.md AGENTS.md GEMINI.md SECURITY.md .gitignore .gitattributes .gitleaks.toml .lighthouserc.json .htmlhintrc eslint.config.mjs package.json package-lock.json docker-compose.yml .env* *.bak
          rm -f api/leads_*.csv api/rate_limits/*.json api/sessions/sess_*
```

---

## 2.2. Wydajność, Jakość i Spójność (`twojastronawww`)

### `[HIGH]` TSW-QUAL-01: Całkowita awaria instalacji Service Workera (`sw.js:16`) oraz zepsuty eksport serwera w `scripts/serve.js`
- **Ścieżka pliku:** [sw.js](file:///e:/Projekty/twojastronawww/sw.js#L11-L18) oraz [scripts/serve.js](file:///e:/Projekty/twojastronawww/scripts/serve.js#L34-L73)
- **Numery linii:** `sw.js:16`, `scripts/serve.js:34-73`
- **Opis problemu:**
  1. W `sw.js:16` lista `PRECACHE_URLS` zawiera `'/favicon.svg'`, podczas gdy plik ikony znajduje się pod ścieżką `'/assets/img/favicon.svg'`. Wywołanie `cache.addAll(PRECACHE_URLS)` otrzymuje błąd `404 Not Found` i przerywa instalację PWA.
  2. W `scripts/serve.js:34` zadeklarowano `const server = http.createServer(...)` bez słowa kluczowego `export` i wywołano `server.listen(PORT)` bezwarunkowo na poziomie modułu, podczas gdy `tests/smoke/ui-smoke.js:10` wykonuje `import { server } from '../../scripts/serve.js'`.
- **Gotowy blok kodu z poprawką:**
```javascript
// e:\Projekty\twojastronawww\sw.js (linie 11-18)
const PRECACHE_URLS = [
  '/',
  '/index.html',
  '/assets/css/main.css',
  '/assets/js/main.js',
  '/assets/img/favicon.svg',
  '/site.webmanifest'
];
```

### `[MEDIUM]` TSW-QUAL-02: Rozbieżność cen w Schema.org JSON-LD względem widoku HTML, link do `test.raricart.pl` oraz błędny atrybut ARIA `aria-pressed` na zakładkach
- **Ścieżka pliku:** [index.html](file:///e:/Projekty/twojastronawww/index.html#L77-L95), [index.html](file:///e:/Projekty/twojastronawww/index.html#L309-L390), [assets/js/main.js](file:///e:/Projekty/twojastronawww/assets/js/main.js#L164)
- **Numery linii:** `index.html:77, 88, 309, 333, 390`, `assets/js/main.js:164`
- **Opis problemu:**
  1. W JSON-LD (`index.html:77, 88`) pakiet Start ma cenę `"1900"`, a Biznes `"3900"`, podczas gdy w widocznym cenniku HTML (`index.html:309, 333`) ceny to `2 499 zł` i `5 499 zł` (ryzyko kary w Google Merchant / Rich Results za rozbieżność cen).
  2. W sekcji portfolio (`index.html:390`) link prowadzi do środowiska testowego `https://test.raricart.pl` zamiast produkcyjnego `https://raricart.pl`.
  3. W `assets/js/main.js:164` funkcja `activateTab()` ustawia `btn.setAttribute('aria-pressed', ...)` na elementach z `role="tab"`, podczas gdy specyfikacja WAI-ARIA wymaga `aria-selected`.

---
---

# 3. REPOZYTORIUM: `e:\Projekty\VPS` (`TwojaStronaWWW/devops-stack`)

## 3.1. Bezpieczeństwo (Zero-Trust / ISO 27001 / OWASP)

### `[CRITICAL]` VPS-SEC-01: Ekspozycja gniazda `/var/run/docker.sock` w trybie zapisu (`RW`) w kontenerach GitHub Actions Runner (`runner-unified` i `runner-web`)
- **Ścieżka pliku:** [compose/runners.yml](file:///e:/Projekty/VPS/compose/runners.yml#L29-L180)
- **Numery linii:** `30`, `38`, `79`, `87`, `166`, `174`
- **Opis podatności:** Kontener `runner-twojastronawww` (linia `87`) montuje `/var/run/docker.sock:/var/run/docker.sock:ro`, natomiast kontenery `runner-unified` (linia `38`) oraz `runner-web` (linia `174`) montują `/var/run/docker.sock:/var/run/docker.sock` **w trybie pełnego zapisu (`RW`)** oraz posiadają włączone `DOCKER_PRUNE=true` (linie `30`, `166`). Przejęcie dowolnego joba CI w którymkolwiek z podpiętych repozytoriów pozwala na uruchomienie uprzywilejowanego kontenera z zamontowanym systemem plików `/` hosta VPS (pełny root takeover). Ponadto wszystkie 3 runnery używają wspólnej zmiennej `${GITHUB_TOKEN}` zamiast dedykowanych `${RUNNER_UNIFIED_TOKEN}`, `${RUNNER_TSW_TOKEN}`, `${RUNNER_WEB_TOKEN}` z `.env.example`.
- **Gotowy blok kodu z poprawką:**
```yaml
# e:\Projekty\VPS\compose\runners.yml (linie 29-39 oraz 165-175)
      - DOCKER_PRUNE=false
      - ACCESS_TOKEN=${RUNNER_UNIFIED_TOKEN}
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock:ro
```

### `[HIGH]` VPS-SEC-02: Zahardkodowane poświadczenia bazy Oracle (`sysadm`) w `compose/amms-lab.yml` i brak walidacji tego pliku w CI
- **Ścieżka pliku:** [compose/amms-lab.yml](file:///e:/Projekty/VPS/compose/amms-lab.yml#L24-L72) oraz [.github/workflows/lint-compose.yml](file:///e:/Projekty/VPS/.github/workflows/lint-compose.yml#L37-L44)
- **Numery linii:** `compose/amms-lab.yml:24, 27, 67-72`, `lint-compose.yml:37-44`
- **Opis podatności:** Plik `compose/amms-lab.yml` zawiera zaszyte na sztywno hasła `ORACLE_PASSWORD=sysadm` oraz `APP_USER_PASSWORD=sysadm` i nie jest objęty walidacją w `.github/workflows/lint-compose.yml`.
- **Gotowy blok kodu z poprawką:**
```yaml
# e:\Projekty\VPS\compose\amms-lab.yml (linie 24-27)
      - ORACLE_PASSWORD=${AMMS_ORACLE_PASSWORD:?Set AMMS_ORACLE_PASSWORD in .env}
      - APP_USER=sysadm
      - APP_USER_PASSWORD=${AMMS_ORACLE_PASSWORD:?Set AMMS_ORACLE_PASSWORD in .env}
```

### `[HIGH]` VPS-SEC-03: Spoofing adresu IP przez `HTTP_X_FORWARDED_FOR` w `getClientIp()` oraz nieograniczony odczyt `php://input` (OOM DoS) w `services/ai-router`
- **Ścieżka pliku:** [services/ai-router/src/router-endpoints.php](file:///e:/Projekty/VPS/services/ai-router/src/router-endpoints.php#L41-L60), [services/ai-router/src/router-endpoints.php](file:///e:/Projekty/VPS/services/ai-router/src/router-endpoints.php#L319), [services/ai-router/src/chat-router.php](file:///e:/Projekty/VPS/services/ai-router/src/chat-router.php#L272)
- **Numery linii:** `router-endpoints.php:41-60, 319`, `chat-router.php:272`
- **Opis podatności:**
  1. W `getClientIp()` (`router-endpoints.php:46-51`), gdy żądanie trafia bezpośrednio do bramki Caddy (pomijając Cloudflare, a więc bez `HTTP_CF_CONNECTING_IP`), kod bierze pierwszy adres IP z nagłówka `HTTP_X_FORWARDED_FOR` (`explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]`). Ponieważ Caddy dopisuje IP klienta na **końcu** listy `X-Forwarded-For`, atakujący może wysłać `X-Forwarded-For: 1.2.3.4`, całkowicie omijając Rate-Limiter IP oraz Penalty-Box!
  2. W `chat-router.php:272` oraz `router-endpoints.php:319` wywołano `file_get_contents('php://input')` bez limitu bajtów (kontener `ai-router` ma limit RAM `256M` w `compose/ai-router.yml:32`).
- **Gotowy blok kodu z poprawką:**
```php
// 1. e:\Projekty\VPS\services\ai-router\src\router-endpoints.php (linie 41-60)
function getClientIp(): string {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP']) && !empty($_SERVER['HTTP_CF_RAY'])) {
        $cfIp = trim((string)$_SERVER['HTTP_CF_CONNECTING_IP']);
        if (filter_var($cfIp, FILTER_VALIDATE_IP)) {
            return $cfIp;
        }
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = array_map('trim', explode(',', (string)$_SERVER['HTTP_X_FORWARDED_FOR']));
        $lastHop = end($parts);
        if ($lastHop !== false && filter_var($lastHop, FILTER_VALIDATE_IP)) {
            return $lastHop;
        }
    }
    $remote = trim((string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'));
    return filter_var($remote, FILTER_VALIDATE_IP) ? $remote : '0.0.0.0';
}

// 2. Bezpieczny odczyt ciała żądania (maks. 64 KB) w chat-router.php:272 i router-endpoints.php:319:
$rawBody = file_get_contents('php://input', false, null, 0, 65536);
```

---

## 3.2. Wydajność, Jakość i Higiena (`VPS`)

### `[MEDIUM]` VPS-PERF-01: Wyścig (Race Condition) podczas usuwania plików w probabilistycznym Garbage Collectorze oraz niespójność nazwy workflowu w `dependabot-automerge.yml`
- **Ścieżka pliku:** [services/ai-router/src/chat-router.php](file:///e:/Projekty/VPS/services/ai-router/src/chat-router.php#L252-L269) oraz [.github/workflows/dependabot-automerge.yml](file:///e:/Projekty/VPS/.github/workflows/dependabot-automerge.yml#L6)
- **Numery linii:** `chat-router.php:252-269`, `dependabot-automerge.yml:6`
- **Opis problemu:**
  1. W `chat-router.php:252-269` Garbage Collector wywołuje `@unlink($gcFile)` bez sprawdzenia nieblokującej blokady `flock($fp, LOCK_EX | LOCK_NB)`, co może usunąć plik blokowany w tej samej milisekundzie przez równoległy proces PHP-FPM.
  2. W `.github/workflows/dependabot-automerge.yml:6` wpisano `workflows: ["CI & Quality Gate"]`, podczas gdy jedyny workflow CI w repozytorium (`lint-compose.yml:1`) nazywa się `"Lint & Validate Docker Compose"`.
  3. W `AGENTS.md:159-166` i `GEMINI.md:159-166` znajdują się uszkodzone znaki kontrolne ASCII (`\x07`, `\x08`, `\x09`, `\x0b`, `\x0c`), a w `.github/workflows/ai-security-review.yml:121` błędna stopka z projektu `KatoSalsaHub`.

---
---

# 4. REPOZYTORIUM: `e:\Projekty\hd-osticket-helpdesk` (`TwojaStronaWWW/hd-osticket-helpdesk`)

## 4.1. Bezpieczeństwo, Wydajność i Jakość (`hd-osticket-helpdesk`)

### `[HIGH]` OST-SEC-01: Email Header Injection i uszkodzenie kodowania UTF-8 w powiadomieniu zapasowym `@mail()` oraz brak sanitacji `$siteId` w `config/ticket_hook.php`
- **Ścieżka pliku:** [config/ticket_hook.php](file:///e:/Projekty/hd-osticket-helpdesk/config/ticket_hook.php#L71-L295)
- **Numery linii:** `71`, `123`, `277-293`
- **Opis podatności:**
  1. W linii `71` wywołano `file_get_contents('php://input')` bez limitu długości (ryzyko OOM DoS).
  2. W linii `123` nieoczyszczony parametr `$siteId` jest wstawiany bezpośrednio do syntetycznego adresu e-mail `"kontakt@{$siteId}.twojastronawww.pl"`, co przy znakach spoza `[a-z0-9-]` powoduje odrzucenie zgłoszenia przez walidator RFC 5322 w `Ticket::create()`.
  3. W liniach `277-293` (`Lead notification email`) zmienna `$mailSubject` (zawierająca `$data['subject']` od użytkownika) jest przekazywana bezpośrednio do `@mail($notifyTo, $mailSubject, ...)` bez usunięcia `\r\n` i bez kodowania `=?UTF-8?B?` (w przeciwieństwie do autorespondera w linii `305`), a `$replyCandidate` w nagłówku `Reply-To:` (linia `288`) nie jest oczyszczany ze znaków nowej linii.
- **Gotowy blok kodu z poprawką:**
```php
// e:\Projekty\hd-osticket-helpdesk\config\ticket_hook.php
// 1. Linia 71 — limit 64 KB na wejście JSON:
$rawInput = file_get_contents('php://input', false, null, 0, 65536);

// 2. Linia 97 — ścisła sanitacja $siteId:
$siteId = preg_replace('/[^a-z0-9_-]/', '', strtolower(trim((string)($data['site'] ?? 'unknown')))) ?: 'unknown';

// 3. Linie 277-293 — bezpieczne kodowanie tematu UTF-8 i ochrona Reply-To przed CRLF:
$cleanMailSubject = preg_replace('/[\r\n]+/', ' ', $mailSubject);
$encodedMailSubject = '=?UTF-8?B?' . base64_encode($cleanMailSubject) . '?=';
$safeReplyCandidate = preg_replace('/[\r\n]+/', '', (string)$replyCandidate);
```

### `[HIGH]` OST-SEC-02: Okno wyścigu uprawnień (CWE-377) i nieatomowy zapis zrzutu bazy danych w `scripts/backup.sh`
- **Ścieżka pliku:** [scripts/backup.sh](file:///e:/Projekty/hd-osticket-helpdesk/scripts/backup.sh#L14-L38)
- **Numery linii:** `14`, `26-34`
- **Opis podatności:** Skrypt `scripts/backup.sh` tworzy plik zrzutu bazy danych `$BACKUP_FILE` za pomocą potoku `mysqldump ... | gzip > "$BACKUP_FILE"` i dopiero po zakończeniu kompresji wywołuje `chmod 600 "$BACKUP_FILE"` (linia `29`). Przez cały czas trwania zrzutu plik jest odczytywalny dla innych użytkowników systemu (`umask 022` -> `0644`). Ponadto brak zapisu do pliku `.tmp` i weryfikacji `gzip -t` przed nadpisaniem docelowego archiwum, a zmienna `DB_USER` (linia `14`) jest nieużywana.
- **Gotowy blok kodu z poprawką:**
```bash
# e:\Projekty\hd-osticket-helpdesk\scripts\backup.sh (linie 25-36)
umask 077
TMP_BACKUP="${BACKUP_FILE}.$$.tmp"
trap 'rm -f "$TMP_BACKUP"' EXIT

mysqldump --defaults-extra-file="$CNF_FILE" --single-transaction --quick --lock-tables=false "$DB_NAME" | gzip -9 > "$TMP_BACKUP"
gzip -t "$TMP_BACKUP"
chmod 600 "$TMP_BACKUP"
mv -f "$TMP_BACKUP" "$BACKUP_FILE"
```

### `[MEDIUM]` OST-QUAL-01: Brak workflowu `ci.yml`, brak `Content-Security-Policy` w `.htaccess` oraz uszkodzone znaki kontrolne w `AGENTS.md` / `GEMINI.md`
- **Ścieżka pliku:** [.htaccess](file:///e:/Projekty/hd-osticket-helpdesk/.htaccess#L1-L23), [.github/workflows/dependabot-automerge.yml](file:///e:/Projekty/hd-osticket-helpdesk/.github/workflows/dependabot-automerge.yml#L6), [AGENTS.md](file:///e:/Projekty/hd-osticket-helpdesk/AGENTS.md#L12-L18)
- **Numery linii:** `.htaccess:1-23`, `dependabot-automerge.yml:6`, `AGENTS.md:12-18`
- **Opis problemu:** W `.htaccess` brakuje nagłówka `Content-Security-Policy: frame-ancestors 'self'; object-src 'none'; base-uri 'self';`, `dependabot-automerge.yml` czeka na nieistniejący workflow `"CI & Quality Gate"`, a `AGENTS.md`/`GEMINI.md` mają uszkodzone sekwencje ucieczki w blokach `[REF-01]`–`[REF-07]`.

---
---

# 5. REPOZYTORIUM: `e:\Projekty\fuegolingua` (`TwojaStronaWWW/fuegolingua`)

## 5.1. Bezpieczeństwo, Wydajność i Jakość (`fuegolingua`)

### `[HIGH]` FL-QUAL-01: Trwała utrata pola `<select name="purpose">` podczas serializacji formularza kontaktowego i zapisu szkicu w `localStorage`
- **Ścieżka pliku:** [assets/js/contact.js](file:///e:/Projekty/fuegolingua/assets/js/contact.js#L33-L145) oraz [index.html](file:///e:/Projekty/fuegolingua/index.html#L403)
- **Numery linii:** `assets/js/contact.js:33, 49-56, 121-145`, `index.html:403`
- **Opis błędu:** W `index.html:403` pole wyboru celu nauki jest zdefiniowane jako `<select id="purpose" name="purpose" required>`, a `api/contact.php:139-154` odczytuje `$input['purpose']`. Natomiast w `assets/js/contact.js:33` pobrana kolekcja pól to `contactForm.querySelectorAll?.('input, textarea')` (pominięto znacznik `select`). Ponieważ `inputs.length > 0`, pętla budująca obiekt JSON (linie `121-125` i `139-145`) iteruje wyłącznie po tablicy `inputs`, przez co wartość pola `purpose` **nigdy nie jest wysyłana do `api/contact.php`** ani zapisywana w szkicu `localStorage`.
- **Gotowy blok kodu z poprawką:**
```javascript
// e:\Projekty\fuegolingua\assets\js\contact.js (linia 33)
const inputs = Array.from(contactForm.querySelectorAll?.('input, textarea, select') || []);
```

### `[HIGH]` FL-SEC-01: Potencjalny DOM XSS przez użycie `innerHTML` dla komunikatów odpowiedzi serwera oraz brak strażnika `isSubmitting` i neutralizacja zegara anty-botowego
- **Ścieżka pliku:** [assets/js/contact.js](file:///e:/Projekty/fuegolingua/assets/js/contact.js#L82-L190)
- **Numery linii:** `82-190` (`109`, `154`, `165`, `178`)
- **Opis podatności:**
  1. Komunikaty `data.message` oraz `error.message` są wstrzykiwane bez escapowania przez `formStatus.innerHTML = '<p class="success">' + msg + '</p>'` (linie `154`, `165`, `178`).
  2. Brak zmiennej strażnika `isSubmitting` przed `fetch`, a w linii `109` użyto `Math.max(2.6, Number(((nowMs - formLoadedAt) / 1000).toFixed(2)))`, co sztucznie podbija `submission_seconds` do `2.6s` nawet dla botów wysyłających formularz w `0.01s`, wyłączając walidację `< 2.5s` w `api/contact.php:123`.
- **Gotowy blok kodu z poprawką:**
```javascript
// e:\Projekty\fuegolingua\assets\js\contact.js (linie 80-188)
  let isSubmitting = false;

  function renderFormStatus(container, type, text) {
    if (!container) return;
    container.textContent = '';
    const p = document.createElement('p');
    p.className = type;
    p.textContent = String(text || '');
    container.appendChild(p);
    container.hidden = false;
    container.style.display = 'block';
  }

  contactForm.addEventListener('submit', async (e) => {
    if (e && typeof e.preventDefault === 'function') {
      e.preventDefault();
    }
    if (isSubmitting) return;
    isSubmitting = true;
    // ... w linii 109:
    const submissionSeconds = Math.max(0, Number(((nowMs - formLoadedAt) / 1000).toFixed(2)));
    // ... w liniach 154, 165, 178 zastąpienie formStatus.innerHTML wywołaniem renderFormStatus(...)
    // ... w bloku finally:
    } finally {
      isSubmitting = false;
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = originalBtnText;
      }
    }
  });
```

### `[HIGH]` FL-SEC-02: Brak maskowania IP (RODO), podatność Cross-Site Logout i brak weryfikacji `Sec-Fetch-Site` w `api/admin.php` oraz duplikacja CI/CD z `cancel-in-progress: true` w `deploy.yml`
- **Ścieżka pliku:** [api/admin.php](file:///e:/Projekty/fuegolingua/api/admin.php#L40-L96) oraz [.github/workflows/deploy.yml](file:///e:/Projekty/fuegolingua/.github/workflows/deploy.yml#L3-L14)
- **Numery linii:** `api/admin.php:40-75, 87-96`, `.github/workflows/deploy.yml:3-14`
- **Opis podatności:**
  1. W `api/admin.php:40` surowy adres `$_SERVER['REMOTE_ADDR']` jest zapisywany bez `mask_ip()`, wylogowanie (linia `42`) działa po zwykłym żądaniu `GET ?logout=1`, a żądania `POST` nie sprawdzają nagłówka `Sec-Fetch-Site`.
  2. W `.github/workflows/deploy.yml:12` ustawiono `cancel-in-progress: true` dla zadania wdrożenia SFTP i wyzwalacz `push` zamiast `workflow_run` po sukcesie `CI & Quality Gate`.
- **Gotowy blok kodu z poprawką:**
```yaml
# e:\Projekty\fuegolingua\.github\workflows\deploy.yml (linie 1-14)
name: CD — Deploy PRODUCTION

on:
  workflow_run:
    workflows: ["CI & Quality Gate"]
    types: [completed]
    branches: [main]

concurrency:
  group: production-deploy-${{ github.ref }}
  cancel-in-progress: false
```

---
---

# 6. REPOZYTORIUM: `e:\Projekty\katosalsahub` (`TwojaStronaWWW/katosalsahub`)

## 6.1. Bezpieczeństwo (Zero-Trust / ISO 27001 / OWASP)

### `[CRITICAL]` KSH-SEC-01: Zaszyty na stałe w kodzie źródłowym hash PBKDF2-SHA256 konta Super-Admina sprawdzany PRZED zmienną środowiskową `.env`
- **Ścieżka pliku:** [katosalsahub.pl/assets/php/wiki.php](file:///e:/Projekty/katosalsahub/katosalsahub.pl/assets/php/wiki.php#L62-L86) oraz [katosalsahub.pl/assets/php/wiki.php](file:///e:/Projekty/katosalsahub/katosalsahub.pl/assets/php/wiki.php#L421-L429)
- **Numery linii:** `85`, `421-429`
- **Opis podatności:** W linii `85` (`get_admin_secret()`) oraz w linii `422` (`verify_admin()`) w jawnym kodzie śledzonym przez Git zaszyto na sztywno hash PBKDF2-SHA256 konta Super-Admina (`pbkdf2$100000$<REDACTED_SALT>$<REDACTED_HASH>`). Co gorsza, w funkcji `verify_admin($password)` (linie `422-425`) ten zahardkodowany `$masterHash` jest weryfikowany **przed** wywołaniem `get_admin_secret()` czytającym `.env` (`KATO_ADMIN_SECRET`). Oznacza to, że nawet po zmianie hasła w `.env` na serwerze produkcyjnym, hasło odpowiadające zahardkodowanemu hashowi w repozytorium Git nadal daje pełne uprawnienia Super-Admina (`admin@katosalsahub.pl`)!
- **Gotowy blok kodu z poprawką:**
```php
// e:\Projekty\katosalsahub\katosalsahub.pl\assets\php\wiki.php (linie 83-86 oraz 421-429)
// W końcu funkcji get_admin_secret() (linia 85):
    return '';
}

// W funkcji verify_admin($password) (linie 421-429):
function verify_admin($password) {
    $secret = get_admin_secret();
    if ($secret === '') {
        return false;
    }
    return verify_password_against_hash($password, $secret);
}
```

### `[HIGH]` KSH-SEC-02: Wyścig TOCTOU w `check_and_Clean_login_attempts()`, brak weryfikacji `Sec-Fetch-Site` oraz brak prywatnego katalogu sesji w `wiki.php`
- **Ścieżka pliku:** [katosalsahub.pl/assets/php/wiki.php](file:///e:/Projekty/katosalsahub/katosalsahub.pl/assets/php/wiki.php#L22-L35), [katosalsahub.pl/assets/php/wiki.php](file:///e:/Projekty/katosalsahub/katosalsahub.pl/assets/php/wiki.php#L338-L371), [katosalsahub.pl/assets/php/wiki.php](file:///e:/Projekty/katosalsahub/katosalsahub.pl/assets/php/wiki.php#L648-L731)
- **Numery linii:** `22-35`, `338-371`, `648-653`, `728-731`
- **Opis podatności:**
  1. `check_and_Clean_login_attempts()` (linie `338-371`) wywołuje najpierw `read_json_file($attemptsFile)` (`LOCK_SH`), zwalnia blokadę, a następnie `update_json_file($attemptsFile, ...)` (`LOCK_EX`) i zwraca nieaktualną zmienną `$attempts` sprzed blokady wyłącznej (naruszenie `AGENTS.md` §4.3).
  2. Brak blokady `$_SERVER['HTTP_SEC_FETCH_SITE'] === 'cross-site'` dla żądań mutujących POST (`[REF-05]`), brak CSRF dla `action=logout` (linia `728`) oraz brak ustawienia prywatnego katalogu sesji (`session_save_path(__DIR__ . '/sessions')`).
- **Gotowy blok kodu z poprawką:**
```php
// e:\Projekty\katosalsahub\katosalsahub.pl\assets\php\wiki.php (linie 338-371)
function check_and_Clean_login_attempts($attemptsFile) {
    $cleanedAttempts = [];
    update_json_file($attemptsFile, [], function($currentData) use (&$cleanedAttempts) {
        if (!is_array($currentData)) $currentData = [];
        $now = time();
        $modified = false;
        foreach ($currentData as $ip => $data) {
            if (isset($data['lock_until']) && $now > $data['lock_until']) {
                if (($data['lock_until'] + 86400) < $now) {
                    unset($currentData[$ip]);
                    $modified = true;
                }
            } elseif (isset($data['last_attempt']) && ($now - $data['last_attempt']) > 3600 && (!isset($data['lock_until']) || $now > $data['lock_until'])) {
                unset($currentData[$ip]);
                $modified = true;
            }
        }
        $cleanedAttempts = $currentData;
        return $modified ? $currentData : null;
    });
    return $cleanedAttempts;
}
```

---

## 6.2. Wydajność, Jakość i Spójność (`katosalsahub`)

### `[HIGH]` KSH-PERF-01: Wąskie gardło CPU $O(N^2)$ — kompilacja ~40 000 wyrażeń `RegExp` w głównym wątku UI podczas renderowania Salsopedii (`linkifyDefinition`)
- **Ścieżka pliku:** [katosalsahub.pl/assets/js/wiki.js](file:///e:/Projekty/katosalsahub/katosalsahub.pl/assets/js/wiki.js#L1115-L1217)
- **Numery linii:** `1115-1217` (`1168`, `1199`)
- **Opis problemu:** Funkcja `linkifyDefinition(escapedText, currentTermId, terms)` jest wywoływana dla każdej karty podczas `renderGrid()`. Wewnątrz niej za każdym razem wywoływane jest `buildKeywordEntries(terms)` (linia `1168`), a w pętli (linia `1199`) dla każdego słowa kluczowego kompilowany jest od zera obiekt `new RegExp('(?<![\\p{L}\\p{N}_\\u0000])' + escapeRegExp(entry.word) + '(?![\\p{L}\\p{N}_\\u0000])', 'iu')`. Przy ~200 hasłach jedno przefiltrowanie siatki wykonuje ~40 000 kompilacji `RegExp` synchronicznie w głównym wątku UI.
- **Gotowy blok kodu z poprawką:**
```javascript
// e:\Projekty\katosalsahub\katosalsahub.pl\assets\js\wiki.js (linie 1115-1205)
let cachedKeywordsRef = null;
let cachedKeywordEntries = [];

function buildKeywordEntries(terms) {
    if (terms === cachedKeywordsRef && cachedKeywordEntries.length > 0) {
        return cachedKeywordEntries;
    }
    const entries = [];
    terms.forEach(item => {
        if (!item || !item.id || !item.term) return;
        const cleanMain = item.term.replace(/\s*\(.*?\)\s*/g, '').trim();
        const words = new Set([item.term.trim(), cleanMain]);
        if (Array.isArray(item.synonyms)) {
            item.synonyms.forEach(s => {
                if (s && s.trim()) words.add(s.trim());
            });
        }
        words.forEach(w => {
            if (w && w.length >= 3) {
                entries.push({
                    word: w,
                    id: item.id,
                    term: item.term,
                    regex: new RegExp('(?<![\\p{L}\\p{N}_\\u0000])' + escapeRegExp(w) + '(?![\\p{L}\\p{N}_\\u0000])', 'iu')
                });
            }
        });
    });
    entries.sort((a, b) => b.word.length - a.word.length);
    cachedKeywordsRef = terms;
    cachedKeywordEntries = entries;
    return entries;
}
// W linii 1199: const regex = entry.regex;
```

### `[HIGH]` KSH-PERF-02: Brak Rate-Limitingu i ograniczenia `CURLPROTO_HTTPS` w `radio.php` oraz błąd merytoryczny w tłumaczeniu `es-CU` (`i18n.js:337`)
- **Ścieżka pliku:** [katosalsahub.pl/assets/php/radio.php](file:///e:/Projekty/katosalsahub/katosalsahub.pl/assets/php/radio.php#L12-L50) oraz [katosalsahub.pl/assets/js/i18n.js](file:///e:/Projekty/katosalsahub/katosalsahub.pl/assets/js/i18n.js#L337)
- **Numery linii:** `radio.php:12-50`, `i18n.js:337`
- **Opis problemu:**
  1. `radio.php` przy każdym żądaniu GET wykonuje synchroniczne połączenie cURL bez `rate_limit()` i bez `CURLOPT_PROTOCOLS => CURLPROTO_HTTPS`.
  2. W `i18n.js:337` tłumaczenie `es-CU` dla klucza `'map.wolni.p1'` wymienia `Bachata, Kizomba`, co łamie twardą zasadę projektu z `ai-widget.js:136` („Kato Salsa Hub zajmuje się WYŁĄCZNIE salsą”).

---
---

# 7. REPOZYTORIUM: `e:\Projekty\Moje-CV` (`TwojaStronaWWW/Moje-CV`)

## 7.1. Bezpieczeństwo, Wydajność i Jakość (`Moje-CV`)

### `[HIGH]` MCV-SEC-01: Nieobsłużony wyjątek `URIError` (DoS Crash serwera Node.js) oraz brak blokady plików `.git` / `package.json` w `scripts/serve.js`
- **Ścieżka pliku:** [scripts/serve.js](file:///e:/Projekty/Moje-CV/scripts/serve.js#L39-L90)
- **Numery linii:** `39-52`, `55`, `87-88`
- **Opis podatności:**
  1. Funkcja `resolveSafeFilePath()` w linii `40` wywołuje `decodeURI(String(rawUrl).split('?')[0])` **bez bloku `try / catch`**. Pojedyncze żądanie HTTP z niepoprawnym kodowaniem procentowym (np. `GET /%E0%A4%A`) rzuca nieobsłużony wyjątek `URIError: URI malformed`, który natychmiast **zamyka cały proces serwera Node.js**!
  2. Brak wzorca `BLOCKED_PATH_PATTERN` pozwala na pobranie przez HTTP plików `/.git/config` czy `/package.json`, a strumień `fs.createReadStream(filePath).pipe(res)` (linia `87`) nie posiada obsługi `.on('error', ...)`.
- **Gotowy blok kodu z poprawką:**
```javascript
// e:\Projekty\Moje-CV\scripts\serve.js (linie 32-60)
const BLOCKED_PATH_PATTERN = /(?:^|[\\/])(?:\.(?!well-known(?:[\\/]|$))[^\\/]+|node_modules|tests|scripts|package(?:-lock)?\.json)(?:[\\/]|$)/i;

export function resolveSafeFilePath(rootDir, rawUrl = '/') {
  let cleanUrl;
  try {
    cleanUrl = decodeURI(String(rawUrl).split('?')[0].split('#')[0]);
  } catch {
    return { filePath: '', isAllowed: false, isMalformed: true };
  }
  const relativePath = cleanUrl === '/' ? 'index.html' : cleanUrl.replace(/^[/\\]+/, '');
  const resolvedRoot = path.resolve(rootDir);
  const filePath = path.resolve(resolvedRoot, relativePath);

  const isInsideRoot = filePath === resolvedRoot || filePath.startsWith(resolvedRoot + path.sep);
  const isBlocked = BLOCKED_PATH_PATTERN.test(relativePath);
  const isAllowed = isInsideRoot && !isBlocked;

  return { filePath, isAllowed, isMalformed: false };
}
```

### `[HIGH]` MCV-QUAL-01: Martwy wyzwalacz `workflow_run` w `.github/workflows/dependabot-automerge.yml`, brak `.htaccess` oraz podwójne budowanie PDF w `deploy.yml`
- **Ścieżka pliku:** [.github/workflows/dependabot-automerge.yml](file:///e:/Projekty/Moje-CV/.github/workflows/dependabot-automerge.yml#L5-L8) oraz [.github/workflows/deploy.yml](file:///e:/Projekty/Moje-CV/.github/workflows/deploy.yml#L52-L99)
- **Numery linii:** `dependabot-automerge.yml:5-8`, `deploy.yml:52, 94`
- **Opis problemu:**
  1. W `dependabot-automerge.yml:6` wpisano `workflows: ["CI & Quality Gate"]`, podczas gdy jedyny workflow w repozytorium (`deploy.yml:1`) nazywa się `"CI/CD Pipeline & Deployment"`. Zadanie `automerge-on-ci-success` nigdy się nie uruchamia.
  2. W `deploy.yml` zadanie `deploy` (linia `94`) ponownie instaluje przeglądarkę Playwright Chromium i buduje plik PDF od nowa zamiast pobrać gotowy artefakt z zadania `build-and-audit`.

---
---

# 8. REPOZYTORIUM: `e:\Projekty\raricart` (`TwojaStronaWWW/raricart`)

## 8.1. Bezpieczeństwo (Zero-Trust / ISO 27001 / OWASP)

### `[CRITICAL]` RAR-SEC-01: Brak autoryzacji w skryptach `admin/migrate_users.php`, `admin/optimize_existing.php`, `admin/generate_hash.php` oraz śledzenie `assets/data/config.json` w Git
- **Ścieżka pliku:** [admin/migrate_users.php](file:///e:/Projekty/raricart/admin/migrate_users.php#L1-L37), [admin/optimize_existing.php](file:///e:/Projekty/raricart/admin/optimize_existing.php#L1-L165), [admin/generate_hash.php](file:///e:/Projekty/raricart/admin/generate_hash.php#L1), [assets/data/config.json](file:///e:/Projekty/raricart/assets/data/config.json#L1-L9)
- **Numery linii:** `admin/migrate_users.php:1-37`, `admin/optimize_existing.php:1-165`, `admin/generate_hash.php:1`, `assets/data/config.json:1-9`
- **Opis podatności:**
  1. Skrypty `admin/migrate_users.php`, `admin/optimize_existing.php` i `admin/generate_hash.php` znajdują się w publicznym katalogu `/admin/` i **nie wywołują `check_login()`**. Jeśli `config.json` został utworzony przez `setup.php`, wywołanie `GET /admin/migrate_users.php` przez niezalogowanego użytkownika z internetu nadpisuje `config.json` i tworzy konto administratora z domyślnym hasłem! Z kolei `GET /admin/optimize_existing.php` uruchamia ciężką rekompresję wszystkich obrazów na dysku (`set_time_limit(300)`).
  2. Plik `assets/data/config.json` z hashami bcrypt kont administratorów jest śledzony w repozytorium Git.
- **Gotowy blok kodu z poprawką:**
```php
// Na samym początku admin/migrate_users.php, admin/optimize_existing.php oraz admin/generate_hash.php:
<?php
if (PHP_SAPI !== 'cli') {
    require_once __DIR__ . '/auth.php';
    check_login();
}
```

### `[CRITICAL]` RAR-SEC-02: Fatal `TypeError` w PHP 8 uniemożliwiający zmianę hasła (`admin/change_password.php:41`) oraz ominięcie blokady `must_change_password` w całym API (`admin/auth.php:44`)
- **Ścieżka pliku:** [admin/change_password.php](file:///e:/Projekty/raricart/admin/change_password.php#L36-L56) oraz [admin/auth.php](file:///e:/Projekty/raricart/admin/auth.php#L44-L49)
- **Numery linii:** `admin/change_password.php:41`, `admin/auth.php:44-49`
- **Opis podatności:**
  1. W `assets/data/config.json:4-7` użytkownik z wymuszoną zmianą hasła jest zapisany jako tablica `{"hash": "$2y$10$...", "force_change": true}`. Jednak w `admin/change_password.php:41` wywołano `password_verify($oldPass, $config['users'][$currentUser])`, przekazując tablicę jako drugi argument. W PHP 8.x rzuca to **Fatal `TypeError: password_verify(): Argument #2 ($hash) must be of type string, array given`**, całkowicie blokując możliwość zmiany hasła!
  2. W `admin/auth.php:44-49` funkcja `check_login()` sprawdza jedynie `$_SESSION['logged_in'] === true` i ignoruje `$_SESSION['must_change_password']`, pozwalając wywoływać wszystkie endpointy mutujące (`upload.php`, `delete.php`, `save_content.php`, `download_leads.php`) bez zmiany hasła.
- **Gotowy blok kodu z poprawką:**
```php
// e:\Projekty\raricart\admin\change_password.php (linie 40-46)
$userEntry = $config['users'][$currentUser];
$existingHash = is_array($userEntry) ? (string)($userEntry['hash'] ?? '') : (string)$userEntry;

if ($existingHash === '' || !password_verify($oldPass, $existingHash)) {
    echo json_encode(['status' => 'error', 'message' => 'Stare hasło jest nieprawidłowe.']);
    exit;
}
```

### `[CRITICAL]` RAR-SEC-03: Serwerowe przechwytywanie niewysłanych pól formularza bez zgody (RODO), CSV Formula Injection, utrata leadów przy błędzie `mail()` oraz błąd przestrzeni nazw w `api/leads-store.php`
- **Ścieżka pliku:** [api/contact.php](file:///e:/Projekty/raricart/api/contact.php#L48-L478), [assets/js/modules/contact.js](file:///e:/Projekty/raricart/assets/js/modules/contact.js#L76-L156), [api/leads-store.php](file:///e:/Projekty/raricart/api/leads-store.php#L157)
- **Numery linii:** `api/contact.php:48, 146-190, 291-345, 350-478`, `api/leads-store.php:157`
- **Opis podatności:**
  1. **Naruszenie RODO i `0777`:** `assets/js/modules/contact.js:97-156` przy zdarzeniach `blur`/`beforeunload` wysyła wpisany e-mail i telefon z flagą `is_partial: true` do `api/contact.php:148-190`, który zapisuje je na serwerze w `api/drafts/` (tworzonym z uprawnieniami `0777` w linii `48`), dopisuje do `admin/leady.csv` i wysyła do R2 oraz e-mailem zanim użytkownik wyrazi zgodę i kliknie „Wyślij”.
  2. **Utrata leada przy błędzie `mail()` (`api/contact.php:291-345`):** Zapis do `admin/leady.csv` i kopia w R2 znajdują się wewnątrz `if (mail($toEmail, ...))` bez bloku `else`! Gdy `mail()` zwróci `false`, lead przepada, a skrypt zwraca pustą odpowiedź HTTP 200.
  3. **CSV Formula Injection i wyścig TOCTOU (`api/contact.php:302-327`):** Brak sanitacji znaków `=`, `+`, `-`, `@` przed `fputcsv()` oraz sprawdzanie `!file_exists($csvFile)` przed `flock($fp, LOCK_EX)`.
  4. **Błąd przestrzeni nazw w `api/leads-store.php:157`:** Wywołanie `new \TwojaStronaWWW\Api\PrivateStorageService(...)` zamiast `new \Raricart\Api\PrivateStorageService(...)`.
- **Gotowy blok kodu z poprawką:**
```php
// e:\Projekty\raricart\api\leads-store.php (linia 157)
$storage = new \Raricart\Api\PrivateStorageService();
```

### `[HIGH]` RAR-SEC-04: Host Header Injection w linku resetu hasła (`admin/forgot_password.php:33`), Session Fixation (`admin/index.php:65`), błąd instalacji `sw.js` oraz luki wykluczeń SFTP w `deploy-production.yml`
- **Ścieżka pliku:** [admin/forgot_password.php](file:///e:/Projekty/raricart/admin/forgot_password.php#L33), [admin/index.php](file:///e:/Projekty/raricart/admin/index.php#L65), [sw.js](file:///e:/Projekty/raricart/sw.js#L6-L14), [.github/workflows/deploy-production.yml](file:///e:/Projekty/raricart/.github/workflows/deploy-production.yml#L64-L81)
- **Numery linii:** `forgot_password.php:33`, `admin/index.php:65`, `sw.js:6-14`, `deploy-production.yml:64-81`
- **Opis podatności:**
  1. W `admin/forgot_password.php:33` link resetu hasła używa niezweryfikowanego `$_SERVER['HTTP_HOST']` (Password Reset Poisoning).
  2. W `admin/index.php:65` brak `session_regenerate_id(true)` po zalogowaniu.
  3. W `sw.js:6-14` `PRECACHE_URLS` zawiera nieistniejące ścieżki `'/index.html'` i `'/assets/css/style.css'` (zamiast `'/'` i `'/assets/css/styles.css'`), przez co instalacja Service Workera zawsze kończy się błędem 404.
  4. W `deploy-production.yml:64-81` lista wykluczeń SFTP nie wyklucza `!admin/leady.csv`, `!api/drafts/**` ani `!bin/**`.
- **Gotowy blok kodu z poprawką (`sw.js`):**
```javascript
// e:\Projekty\raricart\sw.js (linie 5-15)
const CACHE_NAME = 'raricart-pwa-v3';
const PRECACHE_URLS = [
  '/',
  '/assets/css/styles.css',
  '/assets/js/script.js',
  '/assets/js/ai-widget.js',
  '/site.webmanifest',
  '/favicon.ico'
];
```

---
---

# 9. REPOZYTORIUM: `e:\Projekty\radar-p1` (`TwojaStronaWWW/radar-p1`)

## 9.1. Bezpieczeństwo, Wydajność i Jakość (`radar-p1`)

### `[HIGH]` RP1-SEC-01: Zablokowanie `/.well-known/security.txt` (HTTP 404), brak `redirect: 'manual'` i timeoutu na proxy `/radar-data` oraz brak nagłówków `COOP`/`CORP` w `public/_worker.js`
- **Ścieżka pliku:** [public/_worker.js](file:///e:/Projekty/radar-p1/public/_worker.js#L13-L116)
- **Numery linii:** `13-22`, `34-51`, `92-116`
- **Opis podatności:**
  1. W `public/_worker.js:34-46` biała lista `staticAllowed` nie zawiera `'/.well-known/security.txt'`, przez co produkcyjny Cloudflare Worker zwraca `404 Not Found` dla standardu RFC 9116 / ISO 27001 (`[REF-06]`).
  2. W linii `94` wywołanie `fetch(targetUrl, ...)` do upstreamowego Gista nie ustawia `redirect: 'manual'` ani `AbortSignal.timeout(8000)`.
  3. W `BASE_SEC_HEADERS` (linie `13-22`) brakuje `Cross-Origin-Opener-Policy: same-origin-allow-popups` oraz `Cross-Origin-Resource-Policy: same-site` (`[REF-01]`).
- **Gotowy blok kodu z poprawką:**
```javascript
// e:\Projekty\radar-p1\public\_worker.js
// 1. W BASE_SEC_HEADERS (linie 13-22):
  'Cross-Origin-Opener-Policy': 'same-origin-allow-popups',
  'Cross-Origin-Resource-Policy': 'same-site'
// 2. W staticAllowed (linie 34-46) dodać:
  '/.well-known/security.txt'
// 3. W proxy /radar-data (linia 94):
  const upstreamResponse = await fetch(targetUrl, {
    redirect: 'manual',
    signal: AbortSignal.timeout(8000),
    headers: { 'User-Agent': 'Cloudflare-Pages-Radar-Proxy/2.0', 'Accept': 'application/json' },
    cf: { cacheTtl: 60, cacheEverything: true }
  });
```

### `[HIGH]` RP1-QUAL-01: Trwałe zablokowanie formularza logowania (`state.isSubmittingAuth = true`) przy polskich znakach UTF-8 w `btoa()` oraz ignorowanie `state.lastSeverity` w cache'u AI w `public/js/app.js`
- **Ścieżka pliku:** [public/js/app.js](file:///e:/Projekty/radar-p1/public/js/app.js#L1249-L1286) oraz [public/js/app.js](file:///e:/Projekty/radar-p1/public/js/app.js#L1391-L1418)
- **Numery linii:** `1249-1286`, `1391-1418`
- **Opis błędu:**
  1. W `submitAuthModal()` (linia `1258`) ustawiane jest `state.isSubmittingAuth = true;`, a w linii `1262` **przed blokiem `try / finally`** wywoływane jest `btoa(u + ':' + p)`. Jeśli użytkownik wpisze w loginie lub haśle polski znak diakrytyczny (spoza Latin1, np. `ą`, `ł`), natywne `btoa()` rzuca `DOMException: InvalidCharacterError`, omija blok `finally` i **na stałe blokuje logowanie (`state.isSubmittingAuth` pozostaje `true`)**!
  2. W `getCachedAiResponse()` (linia `1393`) klucz cache'u `localStorage` nie uwzględnia aktualnego stanu awarii (`state.lastSeverity`), przez co podczas awarii `CRIT` asystent AI przez 12h zwraca z cache'u starą odpowiedź wygenerowaną w stanie `OK`.
- **Gotowy blok kodu z poprawką:**
```javascript
// e:\Projekty\radar-p1\public\js\app.js (linie 1258-1266 — wewnątrz try {})
  state.isSubmittingAuth = true;
  if (submitBtn) submitBtn.disabled = true;
  try {
    const utf8Bytes = new TextEncoder().encode(u + ':' + p);
    let binaryStr = '';
    for (let i = 0; i < utf8Bytes.length; i++) binaryStr += String.fromCharCode(utf8Bytes[i]);
    const token = btoa(binaryStr);
```

### `[MEDIUM]` RP1-PERF-01: Synchroniczna przebudowa pełnego DOM SVG (`innerHTML`) przy każdym pikselu ruchu myszy (`mousemove`) w `public/js/charts.js` oraz błąd `.replace()` w `scripts/build-single.js`
- **Ścieżka pliku:** [public/js/charts.js](file:///e:/Projekty/radar-p1/public/js/charts.js#L724-L736) oraz [scripts/build-single.js](file:///e:/Projekty/radar-p1/scripts/build-single.js#L40-L45)
- **Numery linii:** `charts.js:724-736`, `build-single.js:40-45`
- **Opis problemu:**
  1. W `charts.js:725` każde zdarzenie `mousemove` na wykresie wywołuje synchronicznie `renderMainChart()`, które parsuje od nowa pełny ciąg `svg.innerHTML` bez `requestAnimationFrame`.
  2. W `scripts/build-single.js:40-45` użyto `html.replace( target, replacementString )` zamiast funkcji `() => replacementString`, co powoduje uszkodzenie kodu JS zawierającego wzorce `$&`, `$$` lub `$'`.

---
---

# 10. REPOZYTORIUM: `e:\Projekty\sejf-vod` (`kpoczatek-dev/vod-dance-mvp`)

## 10.1. Bezpieczeństwo (Zero-Trust / ISO 27001 / OWASP)

### `[CRITICAL]` VOD-SEC-01: Nieautoryzowane ujawnienie bazy użytkowników (`auth.users`) i filmów przez funkcje `SECURITY DEFINER` dostępne dla każdego zalogowanego kursanta (`authenticated`)
- **Ścieżka pliku:** [supabase/migrations/20260819164500_superadmin_video_Cost_and_storage_metrics.sql](file:///e:/Projekty/sejf-vod/supabase/migrations/20260819164500_superadmin_video_cost_and_storage_metrics.sql#L52-L106) oraz [supabase/migrations/20260819151500_add_cloudflare_analytics_settings.sql](file:///e:/Projekty/sejf-vod/supabase/migrations/20260819151500_add_cloudflare_analytics_settings.sql#L58-L91)
- **Numery linii:** `20260819164500_*.sql:52-106`, `20260819151500_*.sql:58-91`
- **Opis podatności:** Funkcje SQL `get_admin_platform_users()`, `get_admin_platform_videos()` oraz `get_superadmin_cost_summary()` są zdefiniowane z klauzulą `SECURITY DEFINER` (działają z uprawnieniami właściciela bazy `postgres`, omijając RLS i czytając bezpośrednio tabelę `auth.users`), posiadają `GRANT EXECUTE ... TO authenticated;` i **nie zawierają wewnątrz ciała funkcji żadnego sprawdzenia roli wywołującego (`auth.uid()`)**! Każdy zalogowany kursant może wywołać z konsoli przeglądarki `supabase.rpc('get_admin_platform_users')` i pobrać pełną listę adresów e-mail, ról i dat logowania wszystkich użytkowników platformy!
- **Gotowy blok kodu z poprawką (Nowa migracja zabezpieczająca):**
```sql
-- Wewnątrz get_admin_platform_users(), get_admin_platform_videos() oraz get_superadmin_cost_summary():
IF NOT EXISTS (
  SELECT 1 FROM public.profiles
  WHERE id = auth.uid() AND role = 'admin'
) THEN
  RAISE EXCEPTION 'Forbidden: Superadmin role required' USING ERRCODE = '42501';
END IF;
```

### `[CRITICAL]` VOD-SEC-02: Brak weryfikacji roli przed użyciem `createAdminClient()` (Service Role) w Server Actions (`groups.ts`, `notifications.ts`, `invitations.ts`)
- **Ścieżka pliku:** [src/actions/groups.ts](file:///e:/Projekty/sejf-vod/src/actions/groups.ts#L68-L127), [src/actions/notifications.ts](file:///e:/Projekty/sejf-vod/src/actions/notifications.ts#L184-L249), [src/actions/invitations.ts](file:///e:/Projekty/sejf-vod/src/actions/invitations.ts#L88-L138)
- **Numery linii:** `groups.ts:68-127`, `notifications.ts:184-249`, `invitations.ts:88-138`
- **Opis podatności:** Funkcje Server Actions (`addGroupMemberByEmail`, `removeGroupMember`, `togglePassStatus`, `sendGroupAnnouncement`, `listInvitationLinks`) sprawdzają jedynie `if (!user) return { error: 'Brak autoryzacji' }`, po czym natychmiast inicjalizują `const supabaseAdmin = createAdminClient()` (klucz `SUPABASE_SERVICE_ROLE_KEY` omijający wszystkie polityki RLS) bez sprawdzenia, czy zalogowany użytkownik ma rolę `admin` lub `instructor` danej grupy! Zwykły kursant może wywołać te akcje, aby nadać sobie aktywny karnet lub usunąć innych uczestników z grupy.
- **Gotowy blok kodu z poprawką:**
```typescript
// Wzorzec autoryzacji przed użyciem createAdminClient() w src/actions/groups.ts, notifications.ts i invitations.ts:
const { data: callerProfile } = await supabase
  .from('profiles')
  .select('role')
  .eq('id', user.id)
  .single();

if (!callerProfile || (callerProfile.role !== 'admin' && callerProfile.role !== 'instructor')) {
  return { error: 'Brak uprawnień instruktora lub administratora.' };
}
```

### `[CRITICAL]` VOD-SEC-03: Ominięcie płatności Stripe (`default_vod_price > 0`) przy akceptacji zaproszenia do grupy lub instruktora w `accept_invitation`
- **Ścieżka pliku:** [supabase/migrations/20260821193000_invitation_links_system.sql](file:///e:/Projekty/sejf-vod/supabase/migrations/20260821193000_invitation_links_system.sql#L161-L252)
- **Numery linii:** `161-252` (`191-214`)
- **Opis podatności:** Funkcja `accept_invitation(p_token)` sprawdza `v_school_price > 0` wyłącznie wtedy, gdy `v_link.scope_type = 'school'` (linia `162`). Natomiast dla `scope_type IN ('group', 'instructor')` (linie `191-214`) bezwarunkowo wstawia rekord do `student_school_access` ze statusem `'active'` (`ON CONFLICT DO UPDATE SET status = 'active'`), nadając darmowy dostęp do szkoły nawet wtedy, gdy szkoła wymaga płatnej subskrypcji VOD (`default_vod_price > 0`)!

---

## 10.2. Wydajność, Integralność Transkodera i Jakość (`sejf-vod`)

### `[CRITICAL]` VOD-ARCH-01: Całkowite zerwanie potoku transkodowania HLS — niezgodny kontrakt JSON między `videos.ts` a `vps-transcoder/server.mjs` oraz brak uploadu segmentów `.ts`/`.m3u8` do R2
- **Ścieżka pliku:** [src/actions/videos.ts](file:///e:/Projekty/sejf-vod/src/actions/videos.ts#L224-L257) oraz [vps-transcoder/server.mjs](file:///e:/Projekty/sejf-vod/vps-transcoder/server.mjs#L56-L70) i [vps-transcoder/server.mjs](file:///e:/Projekty/sejf-vod/vps-transcoder/server.mjs#L263-L334)
- **Numery linii:** `src/actions/videos.ts:224-257`, `vps-transcoder/server.mjs:56-70, 263-334`
- **Opis błędu:**
  1. `saveVideoRecord` w `src/actions/videos.ts:233-243` wysyła do `POST /transcode` pola `{ videoId, r2ObjectKey, hlsPrefix, keyInfoUrl, callbackUrl }`. Tymczasem `vps-transcoder/server.mjs:56-70` wymaga pól `{ videoId, sourceUrl, r2Prefix, webhookUrl }` i odrzuca każde żądanie z błędem `400 Bad Request`!
  2. Co więcej, w `vps-transcoder/server.mjs:263-334` funkcja `processTranscodeJob()` po zakończeniu procesu `ffmpeg` (linia `297`) **nigdy nie wgrywa wygenerowanych plików `master.m3u8` i segmentów `.ts` do Cloudflare R2**, lecz w bloku `finally` (linia `331`) bezpowrotnie kasuje cały katalog `workDir` z dysku (`fs.rm(workDir, { recursive: true })`)!
- **Gotowy blok kodu z poprawką (`src/actions/videos.ts:227-245`):**
```typescript
// e:\Projekty\sejf-vod\src\actions\videos.ts (linie 227-245 — ujednolicenie payloadu z server.mjs)
      const signedSourceUrl = await getSignedVideoUrl(r2Key, 7200);
      const transcodeResponse = await fetch(`${transcoderUrl.replace(/\/+$/, '')}/transcode`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${transcoderSecret}`,
        },
        body: JSON.stringify({
          videoId: insertedVideo.id,
          sourceUrl: signedSourceUrl,
          r2ObjectKey: r2Key,
          r2Prefix: hlsPrefix,
          hlsPrefix,
          keyInfoUrl: `${appBaseUrl}/api/videos/${insertedVideo.id}/key`,
          webhookUrl: `${appBaseUrl}/api/webhooks/transcode`,
          callbackUrl: `${appBaseUrl}/api/webhooks/transcode`,
        }),
      });
```

### `[CRITICAL]` VOD-ARCH-02: Brak zapisu `school_id` w `saveVideoRecord` i `cloneVideoToGroup` (blokada RLS dla wszystkich kursantów) oraz odwołanie do usuniętej kolumny `groups.price` w `hard_delete_group`
- **Ścieżka pliku:** [src/actions/videos.ts](file:///e:/Projekty/sejf-vod/src/actions/videos.ts#L204-L218), [src/actions/videos.ts](file:///e:/Projekty/sejf-vod/src/actions/videos.ts#L499-L513) oraz [supabase/migrations/20260820100000_multi_school_Core_architecture.sql](file:///e:/Projekty/sejf-vod/supabase/migrations/20260820100000_multi_school_core_architecture.sql#L78-L273)
- **Numery linii:** `videos.ts:204-218, 499-513`, `20260820100000_*.sql:78, 248-273`
- **Opis błędu:**
  1. `saveVideoRecord` i `cloneVideoToGroup` wstawiają wiersz do tabeli `videos` z `group_id`, ale **nie pobierają ani nie ustawiają `school_id` grupy** (`videos.school_id` pozostaje `NULL`). Ponieważ polityka RLS `"Students can view videos of active groups"` (`20260820100000_*.sql:248-273`) wymaga `videos.school_id = ssa.school_id`, żaden kursant nie widzi żadnego nowo wgranego filmu!
  2. Migracja `20260820100000_*.sql:78` wykonuje `ALTER TABLE public.groups DROP COLUMN IF EXISTS price;`, przez co funkcja `hard_delete_group()` (odwołująca się do `v_group.price`) rzuca błąd wykonania SQL podczas próby trwałego usunięcia grupy.
- **Gotowy blok kodu z poprawką (`src/actions/videos.ts:200-218`):**
```typescript
// e:\Projekty\sejf-vod\src\actions\videos.ts (pobranie school_id grupy przed insert do videos)
  const { data: targetGroup } = await supabase
    .from('groups')
    .select('school_id')
    .eq('id', groupId)
    .single();

  const { data: insertedVideo, error: dbError } = await supabase
    .from('videos')
    .insert({
      group_id: groupId,
      school_id: targetGroup?.school_id ?? null,
      title: title.trim(),
      // ... pozostałe pola
```

### `[HIGH]` VOD-SEC-04: Wygasanie klucza AES-128 po 24h w statycznej playliście `.m3u8` (`key/route.ts`), zaufanie do nagłówka `Origin` w `resolveSafeOrigin()` oraz PostgREST Filter Injection w `updateVideoEverywhere()`
- **Ścieżka pliku:** [src/app/api/videos/[videoId]/key/route.ts](file:///e:/Projekty/sejf-vod/src/app/api/videos/%5BvideoId%5D/key/route.ts#L31-L77), [src/utils/url-safety.ts](file:///e:/Projekty/sejf-vod/src/utils/url-safety.ts#L88-L121), [src/actions/videos.ts](file:///e:/Projekty/sejf-vod/src/actions/videos.ts#L337-L347)
- **Numery linii:** `key/route.ts:31-77`, `url-safety.ts:88-121`, `videos.ts:337-347`
- **Opis podatności:**
  1. W `key/route.ts:44-52`, gdy zapisany na stałe w pliku `.m3u8` parametr `?exp=` wygaśnie po 24h od transkodowania, endpoint bezwarunkowo zwraca `403 Token expired` zamiast przejść do weryfikacji aktywnej sesji Supabase (`supabase.auth.getUser()`), przez co wszystkie filmy HLS przestają działać po 24 godzinach!
  2. W `src/utils/url-safety.ts:103-111` `resolveSafeOrigin()` sprawdza nagłówek `Origin` żądania przed zmienną środowiskową `NEXT_PUBLIC_SITE_URL`, co umożliwia zatrucie linków powrotnych Stripe/OAuth.
  3. W `src/actions/videos.ts:346` (`updateVideoEverywhere`) użyto surowej interpolacji `.or(\`r2_url.eq.${video.r2_url},...\`)` zamiast bezpiecznego helpera `buildSafeVideoReplicationFilter()` z linii `35-45` tego samego pliku!
- **Gotowy blok kodu z poprawką (`src/utils/url-safety.ts:95-115` oraz `src/actions/videos.ts:343-348`):**
```typescript
// 1. e:\Projekty\sejf-vod\src\actions\videos.ts (linie 343-348)
  const safeFilter = buildSafeVideoReplicationFilter(video.r2_url, video.hls_url);
  if (!safeFilter) {
    return { error: 'Brak poprawnego klucza replikacji wideo.' };
  }
  const { error: updateError } = await supabase
    .from('videos')
    .update(updatePayload)
    .or(safeFilter);
```
```typescript
// 2. e:\Projekty\sejf-vod\src\utils\url-safety.ts (linie 95-115 — priorytet dla NEXT_PUBLIC_SITE_URL)
export function resolveSafeOrigin(requestHeaders?: { get(name: string): string | null } | null): string {
  const envCandidates = [process.env.NEXT_PUBLIC_SITE_URL, process.env.NEXT_PUBLIC_APP_URL];
  for (const envUrl of envCandidates) {
    if (envUrl && envUrl.trim().length > 0) {
      try {
        const parsed = new URL(envUrl.trim());
        if (parsed.protocol === 'http:' || parsed.protocol === 'https:') {
          return parsed.origin;
        }
      } catch {
        // Ignoruj błędny URL środowiskowy
      }
    }
  }
  const rawOrigin = requestHeaders?.get('origin');
  if (rawOrigin) {
    try {
      const parsed = new URL(rawOrigin);
      if ((parsed.protocol === 'http:' || parsed.protocol === 'https:') && parsed.hostname === 'localhost') {
        return parsed.origin;
      }
    } catch {
      // Ignoruj
    }
  }
  return 'http://localhost:3000';
}
```
