# 🍨 Raricart.pl - Mobilne Live Food Stations
[![CI Quality Gate](https://github.com/TwojaStronaWWW/raricart/actions/workflows/ci.yml/badge.svg)](https://github.com/TwojaStronaWWW/raricart/actions/workflows/ci.yml)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%20%7C%208.3-777bb4.svg)](https://www.php.net/)
[![Node Tests](https://img.shields.io/badge/Unit%20Tests-66%20Passed-brightgreen.svg)](https://github.com/TwojaStronaWWW/raricart)
[![Coverage](https://img.shields.io/badge/Coverage-95%25-brightgreen.svg)](https://github.com/TwojaStronaWWW/raricart)
[![Architecture](https://img.shields.io/badge/Architecture-Zero%20SQL%20%7C%20Flat--File-blue.svg)](https://github.com/TwojaStronaWWW/raricart)

Oficjalne repozytorium platformy **[Raricart.pl](https://raricart.pl)** – catering live cooking i mobilne stacje deserowo-przekąskowe na wesela, gale oraz eventy firmowe (Mini Pancakes, Lody Włoskie, Deski Serów).

Projekt łączy bezkompromisową estetykę wizualną **Lejka 2026** z architekturą o zerowym długu technologicznym: natywny PHP 8, modułowy Vanilla JS (ES6+), zero bibliotek w `node_modules` w produkcji, brak SQL (flat-file JSON/CSV) oraz potok CI/CD wzorowany na projektach `Gal Anonim` i `_site-template`.

---

## 🗺️ Strategiczna Mapa Rozwoju (Roadmap)
Szczegółowy plan rozwoju technologicznego i produktowego platformy na lata 2026–2027 (od MVP po portal B2B) znajduje się w pliku:
👉 **[docs/ROADMAP.md](docs/ROADMAP.md)**

---

## 🛡️ Brama Jakości & Testy Jednostkowe (Quality Gate)

Projekt posiada kompletny zestaw automatycznych testów jednostkowych i integracyjnych uruchamianych lokalnie oraz w potoku GitHub Actions (`.github/workflows/ci.yml`).

### Dostępne polecenia:

```bash
# 1. Pełna weryfikacja (składnia JS + PHP Lint + testy Node + testy PHP)
npm run check

# 2. Testy jednostkowe Node.js (Repo Hygiene, HTML, CSS, JS Modules)
npm test

# 3. Raport pokrycia kodu (Code Coverage)
npm run test:coverage

# 4. Testy jednostkowe PHP Backend (Hot Leads, Concurrency, CSRF, Media)
npm run test:php

# 5. Sprawdzenie składni plików PHP (PHP Lint na wszystkich plikach projektu)
npm run check:php

# 6. Weryfikacja składni JavaScript
npm run check:syntax

# 7. Aktywacja ochrony pre-commit hook w lokalnym git
npm run prepare
```

---

## 📁 Struktura Katalogów

*   `admin/` – Panel administracyjny (zarządzanie treściami, galerią, statusem i pobieranie leadów).
*   `api/` – Endpointy backendowe (obsługa leadów `contact.php`, synchronizacja z Cloudflare R2, dynamiczny robots).
*   `assets/` – Zasoby statyczne:
    *   `css/` – Arkusze stylów (`styles.css` z tokenami projektowymi, `pakiety-style.css`).
    *   `data/` – Bezpieczne pliki bazy flat-file (`content.json`, `gallery.json`, `status.json`, `config.json`).
    *   `fonts/` – Lokalne fonty (`Comfortaa`, `Playfair`).
    *   `js/` – Modułowy JavaScript ES6+ (`modules/`).
*   `docs/` – Dokumentacja techniczna i strategiczna (`ROADMAP.md`, `PROJEKT_REDESIGN_2026.md`).
*   `parts/` – Czyste komponenty widoku PHP (SoC – strict view split).
*   `tests/` – Zestaw testów jednostkowych (`unit/`, `helpers/mock-dom.js`).
*   `.github/` – Potoki CI/CD (`ci.yml`, `deploy-staging.yml`, `deploy-production.yml`, `dependabot.yml`).

---

## ⚙️ Panel Administratora

Panel administratora znajduje się pod adresem: `/admin`.

### Logowanie i Hasła

System nie posiada domyślnego hasła "zaszytego" w kodzie. 

Podczas **pierwszego uruchomienia** panelu administratora (`/admin`), zostaniesz automatycznie przekierowany do strony konfiguracyjnej (`setup.php`), gdzie należy ustawić hasło dostępu.

Hasło (w postaci bezpiecznego skrótu Bcrypt/Argon2id) jest przechowywane w pliku:
`assets/data/config.json`

### Resetowanie Hasła

W przypadku zapomnienia hasła, procedurę resetowania można przeprowadzić ręcznie:

1.  Zaloguj się na serwer (FTP/SSH) lub uzyskaj dostęp do plików lokalnych.
2.  Usuń lub zmień nazwę pliku: `assets/data/config.json`.
3.  Wejdź ponownie na adres `/admin` w przeglądarce.
4.  System wykryje brak konfiguracji i wyświetli formularz ustawienia nowego hasła.

> **Ważne:** Usunięcie pliku `config.json` zresetuje tylko hasło administratora. Nie wpłynie na treści strony, galerię ani inne ustawienia.

### Zmiana Hasła (dla zalogowanych)

Jeśli znasz obecne hasło i chcesz je zmienić, możesz to zrobić bezpośrednio z poziomu panelu administratora, korzystając z odpowiedniej opcji w ustawieniach.
