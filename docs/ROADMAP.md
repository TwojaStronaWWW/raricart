# 🗺️ ROADMAPA ROZWOJU PROJEKTU: RARICART.PL (2026 - 2027)
## Strategiczny plan rozwoju technologicznego i produktowego platformy mobilnych stacji live food

---

## 1. WIZJA I CELE ARCHITEKTONICZNE
Platforma **Raricart.pl** to bezkompromisowe połączenie najwyższej estetyki wizualnej (Lejek 2026) z ultra-lekką, odporną na awarie architekturą produkcyjną:
- **Zero SQL Bloat:** Architektura flat-file oparta o zoptymalizowane struktury JSON oraz rejestry CSV z blokowaniem atomowym (`flock(LOCK_EX)`).
- **Zasada pojedynczej odpowiedzialności (SoC):** Ścisłe rozdzielenie warstwy widoku (`index.php`, `pakiety.php`, komponenty w `/parts/`) od logiki biznesowej (`/api/`, `/admin/`). Brak inline CSS/JS.
- **Bezpieczeństwo klasy Enterprise:** Pliki cookies z flagami `HttpOnly, Secure, SameSite=Strict`, tokeny CSRF weryfikowane przez `hash_equals()`, ścisła sanityzacja wejścia i obsługa leadów z zabezpieczeniem przed spamem i wyścigami procesów (*race conditions*).
- **Automatyzacja i Jakość:** 100% native Node.js test runner (`node:test`) bez obcych zależności w `node_modules`, zintegrowane testy jednostkowe PHP 8, automatyczny audyt Lighthouse CI oraz potoki CI/CD w GitHub Actions.

---

## 2. ETAPY WDRAŻANIA I KAMIEŃ MILOWY (MILESTONES)

```
[Q3 2026] FAZA 1: FUNDAMENTY & LEJEK 2026  (UKOŃCZONE)
       │
       ▼
[Q4 2026] FAZA 2: JAKOŚĆ, CI/CD & TESTY JEDNOSTKOWE  (UKOŃCZONE / W BIEŻĄCYM WYDANIU)
       │
       ▼
[Q1 2027] FAZA 3: KALKULATOR EVENTOWY & INTERAKTYWNY KALENDARZ B2B
       │
       ▼
[Q2 2027] FAZA 4: AUTOMATYZACJA OFERTOWANIA, GENERATOR PDF & WIELOJĘZYCZNOŚĆ EN/DE
       │
       ▼
[Q3 2027] FAZA 5: PORTAL KLIENTA B2B & ANALITYKA KONWERSJI LEJKÓW
```

---

### FAZA 1: Fundamenty, Nowy Lejek 2026 & Chmura R2 [STATUS: UKOŃCZONE ✅]
- [x] Całkowity redesign UX/UI serwisu (sekcje Hero Split, Trust Bar, Live Stations, Process, Audiences, Why Raricart, Realizations, Google Reviews, FAQ, Contact).
- [x] Migracja biblioteki mediów do Cloudflare R2 i wdrożenie automatycznej konwersji do WebP (max 1920px, jakość 85, unikalne hashe).
- [x] Bezpieczny panel administratora (`/admin/`) z obsługą sesji, hashowaniem haseł Argon2id/Bcrypt oraz synchronizacją leadów do R2.
- [x] Obsługa szkiców formularza (draft autosave), ratelimiter IP oraz mechanizm Daily Digest dla porzuconych formularzy.
- [x] Oznaczenie leadów o wysokim priorytecie (`🔥 HOT` dla budżetu >= 5000 PLN lub liczby gości >= 100).
- [x] Błyskawiczny cache LiteSpeed i natychmiastowe unieważnianie nagłówkiem `X-LiteSpeed-Purge: *` po każdej edycji zawartości.

---

### FAZA 2: Standard Jakości, Testy Jednostkowe & CI/CD [STATUS: UKOŃCZONE ✅]
*Wzorowane na sprawdzonych standardach repozytoriów `gal-anonim` oraz `_site-template`.*
- [x] **Zestaw testów jednostkowych repozytorium (Repo Hygiene):**
  - Weryfikacja integralności `package.json`, `.githooks/pre-commit`, `.lighthouserc.json`, `.github/dependabot.yml`.
  - Skanowanie w poszukiwaniu pozostałości po konfliktach gita (`<<<<<<<`, `>>>>>>>`).
  - Walidacja poprawności formatów JSON danych (`content.json`, `status.json`, `gallery.json`, `reviews.json`, `config.json`).
- [x] **Testy spójności kontraktu Frontend (HTML, CSS, JS):**
  - Weryfikacja semantyki, tagów `<head>`, linków kanonicznych, protokołów Open Graph i Twitter Card.
  - Weryfikacja obecności wszystkich 13 sekcji Lejka 2026 i hierarchii DOM (W3C).
  - Weryfikacja stylów CSS dla dynamicznych klas JS (`.active`, `.lightbox-open`, itp.).
- [x] **Testy jednostkowe modułów JavaScript (ES6+):**
  - Moduł tłumaczeń i wielojęzyczności (`i18n.js`, `translations.js`).
  - Moduł cookies i analityki RODO (`cookies.js`).
  - Narzędzia pomocnicze (`utils.js` - blokada scrolla body).
  - Obsługa okien modalnych ofert (`modals.js`).
- [x] **Testy jednostkowe PHP Backend:**
  - Walidacja reguł bezpieczeństwa sesji i tokenów CSRF w `auth.php`.
  - Weryfikacja reguł hot-leadów, sanityzacji i zapisu do CSV z BOM UTF-8 w `contact.php`.
  - Weryfikacja przetwarzania mediów w `MediaService.php` (rygorystyczne typy MIME, WebP, wymiary).
- [x] **Zarządzanie robotami sieciowymi i indeksowaniem:**
  - Utworzenie fizycznego pliku `robots.txt` z blokadą katalogów administracyjnych (`/admin/`, `/api/`, `/drafts/`, `/config/`).
  - Wygenerowanie kompletnej mapy witryny `sitemap.xml` z deklaracją priorytetów i obrazów.
- [x] **Potok CI Quality Gate (`.github/workflows/ci.yml`):**
  - Automatyczne uruchamianie PHP Lint, JS Syntax Check, testów PHP oraz testów Node.js przy każdym pushu i PR.

---

### FAZA 3: Kalkulator Eventowy & Dostępność w Czasie Rzeczywistym [STATUS: PLANOWANE - Q1 2027 ⏳]
- [ ] **Interaktywny selektor i kalkulator wyceny stacji:**
  - Dynamiczny konfigurator pozwalający klientowi zaznaczyć 1, 2 lub 3 stacje (Mini Pancakes, Lody Włoskie, Deska Serów).
  - Suwak liczby gości z automatyczną estymacją rekomendowanej przepustowości stanowisk.
  - Natychmiastowe przekazanie wyliczonej konfiguracji do pól formularza kontaktowego.
- [ ] **Kalendarz dostępności terminów:**
  - Wizualny podgląd wolnych weekendów w sezonie ślubnym i firmowym (zielony/żółty/czerwony).
  - Moduł w panelu administracyjnym do szybkiego blokowania zarezerwowanych dat bez konieczności edycji kodu.
- [ ] **Zaawansowany Rate-Limiting z pamięcią weryfikacji honeypot:**
  - Usprawnienie ochrony przed botami spamującymi bez uciążliwych dla użytkownika captch.

---

### FAZA 4: Automatyzacja Ofertowania & Ekspansja Językowa [STATUS: PLANOWANE - Q2 2027 ⏳]
- [ ] **Bezserwerowy Generator PDF dla Ofert Indywidualnych:**
  - Automatyczne generowanie eleganckiego, markowego pliku PDF z ofertą (zestawienie stacji, harmonogram serwisu, warunki techniczne, cennik).
  - Opcja pobrania spersonalizowanej oferty bezpośrednio po wysłaniu formularza lub z linku w wiadomości e-mail.
- [ ] **Pełna wielojęzyczność (EN / DE):**
  - Rozbudowa bazy tłumaczeń w `translations.js` o język niemiecki dla klientów zagranicznych i przygranicznych eventów B2B.
  - Wdrożenie alternatywnych tagów `hreflang` w nagłówkach HTML dla optymalizacji SEO międzynarodowego.
- [ ] **Optymalizacja Core Web Vitals:**
  - Wdrożenie adaptacyjnego ładowania klatek wideo w tle w zależności od przepustowości łącza użytkownika (`navigator.connection.effectiveType`).

---

### FAZA 5: Portal Agencji B2B & Analityka [STATUS: PLANOWANE - Q3 2027 ⏳]
- [ ] **Strefa Partnerska B2B dla Wedding Plannerów i Agencji:**
  - Dedykowany moduł ze zniżkami agencyjnymi, pakietami zbiorczymi i materiałami promocyjnymi w wysokiej rozdzielczości do prezentacji klientom.
- [ ] **Zaawansowany panel statystyk i analityki konwersji:**
  - Wizualizacja wskaźnika porzuceń formularza na poszczególnych etapach (pole po polu).
  - Statystyki najpopularniejszych kombinacji stacji kulinarnych w ujęciu rocznym.
  - Eksport zagregowanych raportów do formatu XLSX / CSV z automatycznym szyfrowaniem archiwum.

---

## 3. PROCEDURA TESTOWANIA I WERYFIKACJI WYDAŃ (RUNBOOK)

Każde wdrożenie nowego kodu na produkcję musi bezwzględnie przejść lokalną oraz zdalną bramę jakości:

```bash
# 1. Sprawdzenie poprawności składni JavaScript
npm run check:syntax

# 2. Sprawdzenie poprawności składni wszystkich plików PHP
npm run check:php

# 3. Uruchomienie testów jednostkowych Node.js (Repo Hygiene, HTML, CSS, JS)
npm test

# 4. Uruchomienie testów jednostkowych PHP Backend
npm run test:php

# 5. Całościowa weryfikacja jednym poleceniem
npm run check
```

---
*Dokument utrzymywany i aktualizowany przez zespół techniczny Raricart.pl.*
