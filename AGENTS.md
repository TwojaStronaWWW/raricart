# Context: "Raricart.pl" Web App

**Stack:** PHP 8 Native, Vanilla JS (ES6+), Vanilla CSS (Tokens).
**Data:** Flat-file JSON (`assets/data/*.json`, NO SQL), CSV Leads (`admin/leady.csv`), PHP GD (WebP).
**Infra:** Docker, Apache LiteSpeed.

## Critical Architecture Rules

1. **SoC:** Strict split between Views (`index.php`, `parts/*.php`) and Logic (`/admin/`, `/api/`). NO inline CSS/JS.
2. **Concurrency:** ALWAYS use `LOCK_EX` or atomic writes (temp file + `rename()`) for JSON/CSV mutations to prevent corruption.
3. **Security:** Cookies MUST be `HttpOnly`, `Secure`, `SameSite=Strict`. ALL admin POST requests require `auth.php`, `X-CSRF-Token` validation, and strict sanitization.
4. **Leads (`/api/contact.php`):** Support partial drafts. Mark `🔥 HOT` if budget >= 5000 PLN or guests >= 100. Write CSV using UTF-8 BOM (`\xEF\xBB\xBF`) and `flock(LOCK_EX)`.
5. **Media (`admin/upload.php`):** Strict MIME verification. Force WebP conversion (max 1920px, Q85, hashed filenames). Respect `target` payload for dynamic destination directories.
6. **Cache:** Send `header("X-LiteSpeed-Purge: *");` immediately after any JSON database mutation.
