<p align="center">
  <img src="assets/img/logo.webp" alt="Raricart Logo" width="280" onerror="this.src='favicon.ico'" />
</p>

<h1 align="center">Raricart.pl — Mobilne Live Food Stations (Lejek 2026)</h1>

<p align="center">
  <strong>Komercyjny system automatyzacji wycen, ofertowania i pozyskiwania leadów dla cateringu live cooking klasy premium.</strong><br>
  Mobilne stacje deserowo-przekąskowe na wesela, gale i eventy firmowe (Mini Pancakes, Lody Włoskie, Deski Serów).
</p>

<p align="center">
  <a href="https://github.com/TwojaStronaWWW/raricart/actions/workflows/ci.yml"><img src="https://github.com/TwojaStronaWWW/raricart/actions/workflows/ci.yml/badge.svg" alt="CI Quality Gate"></a>
  <img src="https://img.shields.io/badge/PHP-8.2%20%7C%208.3%20Native-777bb4?logo=php&logoColor=white" alt="PHP 8 Native">
  <img src="https://img.shields.io/badge/Frontend-Vanilla%20JS%20(ES6%2B)%20%7C%20CSS-yellow?logo=javascript&logoColor=black" alt="Vanilla Stack">
  <img src="https://img.shields.io/badge/Baza%20Danych-Zero%20SQL%20%7C%20Flat--File-success" alt="Zero SQL Flat-File">
  <img src="https://img.shields.io/badge/Testy%20Jednostkowe-70%2F70%20(100%25%20PASS)-brightgreen" alt="70 Testów Zaliczone">
  <img src="https://img.shields.io/badge/Pokrycie%20Kodu-%3E90%25-brightgreen" alt="Pokrycie Kodu">
  <img src="https://img.shields.io/badge/Lighthouse-SEO%2090%2B%20%7C%20a11y%2090%2B-orange?logo=lighthouse" alt="Lighthouse CI">
  <img src="https://img.shields.io/badge/Licencja-Zastrze%C5%BCona%20%2F%20Raricart-red" alt="Prawa Zastrzeżone">
</p>

<p align="center">
  <a href="https://raricart.pl" target="_blank">
    <img src="https://img.shields.io/badge/%F0%9F%8C%90_ZOBACZ_SERWIS_NA_%C5%BBYWO-raricart.pl-ff5e62?style=for-the-badge" alt="Odwiedź Raricart.pl" />
  </a>
  <a href="https://test.raricart.pl" target="_blank">
    <img src="https://img.shields.io/badge/%F0%9F%9B%A0%EF%B8%8F_%C5%9ARODOWISKO_TESTOWE-test.raricart.pl-2b5876?style=for-the-badge" alt="Środowisko Staging" />
  </a>
</p>

---

## 📸 Podgląd Platformy

<p align="center">
  <img src="assets/img/hero-preview.webp" alt="Podgląd serwisu Raricart.pl" width="850" onerror="this.style.display='none'" style="border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.15);" />
</p>

---

## 🎯 Czym jest Raricart?

**Raricart.pl** to nowoczesna platforma internetowa i dedykowany silnik konwersji (Lejek 2026), stworzony z myślą o skalowaniu sprzedaży usług cateringowych live food stations w segmencie B2B (eventy firmowe, targi, gale) oraz B2C (wesela, urodziny, jubileusze).

Projekt łączy wysmakowany design wizualny w stylu modern luxury z inżynierskim rygorem architektonicznym:
- **0 ciężkich bibliotek w produkcji** – brak zbędnych frameworków JS/CSS, maksymalna wydajność i szybkość ładowania.
- **Baza Flat-File (Zero SQL)** – przechowywanie danych w zoptymalizowanych plikach JSON i CSV, zabezpieczonych blokadami atomowymi `flock(LOCK_EX)`.
- **Zintegrowany Panel Administracyjny (`/admin`)** – edycja treści, zarządzanie galerią i statusem stacji, automatyczna konwersja WebP oraz eksport leadów z kwalifikacją `🔥 HOT`.

> ⚡ **Gwarancja Wydajności i Czystości Kodu:**  
> Architektura spełnia rygorystyczne normy wydajnościowe Google Core Web Vitals, standardy dostępności WCAG 2.1 oraz pełne szyfrowanie ciasteczek sesyjnych (`HttpOnly, Secure, SameSite=Strict`).

---

## ✨ Kluczowe Możliwości i Moduły

### 🍧 Stacje Kulinarne (Live Stations)
| Stacja | Specyfikacja Oferty | Opcje i Personalizacja |
| :--- | :--- | :--- |
| **Pancakes Station** | Holenderskie puszyste mini pancakes wypiekane na oczach gości | Sosy rzemieślnicze, świeże owoce, personalizowane dodatki |
| **Ice Cream Station** | Rzemieślnicze lody włoskie z mobilnej maszyny gastronomicznej | Wybór smaków premium, posypki chrupiące, polewy ciepłe |
| **Cheese & Wine Station** | Selekcja europejskich serów dojrzewających i wędlin gourmet | Winogrona, orzechy, konfitury figowe, pieczywo rzemieślnicze |

### 🧠 Inteligentny Lejek Konwersji 2026
* **Kwalifikacja Leadów w Czasie Rzeczywistym (`🔥 HOT`):**
  * Budżet $\ge 5000$ PLN lub liczba gości $\ge 100$ automatycznie oznacza zapytanie jako priorytetowe.
  * Zapis do pliku CSV z nagłówkiem UTF-8 BOM (`\xEF\xBB\xBF`) dla bezbłędnego otwierania w programie Microsoft Excel.
* **Obsługa Szkiców i Ochrona Przed Porzuceniem (Autosave Drafts):**
  * System zapisuje częściowo wypełnione formularze w tle (`/api/drafts/`), chroniąc przed utratą kontaktu w przypadku zamknięcia karty.
* **Ochrona Przed Spamem i Race Conditions:**
  * Rate-limiting oparty o unikalne tokeny i adresację IP.
  * Blokada wyłączna `flock(LOCK_EX)` eliminująca wyścigi procesów podczas równoczesnych zgłoszeń.

### 🖼️ Zarządzanie Mediami (MediaService & R2)
* **Wymuszona Optymalizacja WebP:** Automatyczne skalowanie obrazów (maksymalnie 1920px), kompresja stratna Q85 i hashowanie nazw plików.
* **Podwójny Magazyn Danych:** Integracja z Cloudflare R2 Object Storage z automatycznym lokalnym fallbackiem w przypadku braku łączności.
* **Natychmiastowe Czyszczenie Cache:** Emisja nagłówka `X-LiteSpeed-Purge: *` natychmiast po każdej edycji treści lub galerii.

---

## 🏛️ Architektura Techniczna i Standardy

```plaintext
raricart/
├── .github/
│   ├── dependabot.yml              # Comiesięczne aktualizacje GitHub Actions i npm
│   └── workflows/
│       ├── ci.yml                  # Quality Gate + PHP Lint + Testy + Lighthouse CI
│       ├── deploy-staging.yml      # Wdrożenie SFTP na test.raricart.pl (z bramą CI)
│       └── deploy-production.yml   # Wdrożenie produkcyjne + automatyczny git release tag
├── .githooks/
│   ├── pre-commit                  # Blokada wycieku sekretów (.env) + npm run check
│   └── commit-msg                  # Strażnik Conventional Commits (standard Gal Anonim)
├── admin/                          # Autorski panel administracyjny (SoC Logic)
│   ├── auth.php                    # Hashowanie haseł Bcrypt/Argon2id, ochrona CSRF
│   ├── dashboard.php               # Pulpit sterowania stacjami i mediami
│   └── leady.csv                   # Baza kontaktów z UTF-8 BOM i blokadą LOCK_EX
├── api/                            # Usługi backendowe
│   ├── contact.php                 # Przetwarzanie zapytań ofertowych i autosave szkiców
│   └── MediaService.php            # Silnik przetwarzania grafiki (GD / WebP / R2)
├── assets/
│   ├── css/                        # styles.css z pełnym Design Systemem w :root
│   ├── data/                       # Bezpieczna baza Flat-File (content.json, status.json)
│   └── js/                         # Modułowy JavaScript ES6+ (cookies, i18n, modals)
├── parts/                          # Czyste komponenty widoku PHP (SoC Views)
├── tests/
│   ├── helpers/mock-dom.js         # Autorski lekki silnik Mock DOM (bez jsdom)
│   ├── unit/                       # Testy jednostkowe Node.js i PHP
│   └── lint_php.php                # Wieloplatformowy skaner składni PHP (63 pliki)
├── .gitattributes                  # Normalizacja LF dla hooków i skryptów bash
├── .lighthouserc.json              # Progi jakościowe Lighthouse CI (a11y/SEO >= 0.90)
├── favicon.ico                     # Wielorozdzielczy favicon (16x16, 32x32, 48x48)
├── site.webmanifest                # Manifest PWA (theme-color, standalone)
└── robots.txt & sitemap.xml        # Kompletna konfiguracja indeksowania SEO
```

---

## 🛡️ Brama Jakości & Testy Jednostkowe (Quality Gate)

Projekt posiada wbudowany silnik testów jednostkowych o zerowej liczbie zewnętrznych bibliotek (`node:test`, `node:assert/strict` oraz natywny runner PHP 8).

### Wyniki Testów:
* **Node.js Test Runner:** 54/54 testów zaliczonych (HTML semantyka, tokeny CSS, moduły JS, repo hygiene).
* **PHP Backend Runner:** 16/16 testów zaliczonych (bezpieczeństwo sesji, CSRF, MediaService, hot leads, blokady `LOCK_EX`).
* **Łączny wynik:** **70/70 zaliczonych testów (100% PASS)** przy czasie wykonania < 1 sekundy.

### Dostępne Polecenia developerskie:

```bash
# 1. Pełna brama jakościowa (składnia JS + PHP Lint + testy Node + testy PHP)
npm run check

# 2. Testy jednostkowe Node.js
npm test

# 3. Raport pokrycia kodu (Code Coverage)
npm run test:coverage

# 4. Testy jednostkowe PHP Backend
npm run test:php

# 5. Skaner poprawności składni PHP (PHP Lint na całym projekcie)
npm run check:php

# 6. Weryfikacja składni JavaScript
npm run check:syntax

# 7. Aktywacja lokalnych hooków Gita (.githooks)
npm run prepare
```

---

## 🚀 Trójstopniowy Pipeline CI/CD (GitHub Actions)

Wdrożenia na serwery hostingowe LiteSpeed odbywają się w pełni automatycznie przez bezpieczny protokół SFTP z zachowaniem kolejkowania współbieżności (`concurrency`):

1. **Push na gałąź `staging`:**
   - Uruchomienie pełnego zestawu testów i walidacji `npm run check`.
   - Audyt jakości **Lighthouse CI** z zapisem artefaktów.
   - Wdrożenie plików na serwer testowy: **`https://test.raricart.pl`**.
2. **Merge do gałęzi `main`:**
   - Weryfikacja testów i potoku Quality Gate.
   - Wdrożenie plików na domenę produkcyjną: **`https://raricart.pl`**.
   - Automatyczne wygenerowanie oficjalnego tagu wydania w gicie w formacie `vYYYY.MM.DD-<commit_sha>`.

---

## 💻 Wymagania i Uruchomienie Lokalne

| Wymaganie | Wersja Minimalna | Zalecana |
| :--- | :--- | :--- |
| **PHP** | 8.2 | 8.3 z rozszerzeniami `gd`, `fileinfo`, `mbstring`, `curl` |
| **Node.js** | 20 LTS | 22 LTS (do uruchamiania natywnego silnika testów) |
| **Serwer WWW** | Dowolny (Apache / LiteSpeed / Docker) | LiteSpeed Web Server z obsługą nagłówków Purge |

### Szybki Start:
```bash
# 1. Sklonuj repozytorium
git clone https://github.com/TwojaStronaWWW/raricart.git
cd raricart

# 2. Zainstaluj hooki gita
npm run prepare

# 3. Uruchom testy
npm run check

# 4. Uruchom wbudowany serwer developerski PHP
php -S localhost:8000
```

---

## 🗺️ Mapa Rozwoju (Roadmap)

Szczegółowy plan rozwoju technologicznego, produktowego i marketingowego platformy na lata 2026–2027 znajduje się w pliku:  
👉 **[docs/ROADMAP.md](docs/ROADMAP.md)**

---

## 👥 Autor i Prawa Autorskie

* **Wdrożenie i Rozwój:** [TwojaStronaWWW](https://github.com/TwojaStronaWWW)
* **Architektura & Engineering:** Krzysztof Początek
* **Prawa autorskie:** &copy; 2026 Raricart.pl. Wszelkie prawa zastrzeżone.
