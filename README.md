# Raricart – https://raricart.pl/

> **Komercyjny system automatyzacji wycen, pozyskiwania leadów i zarządzania mediami z dedykowanym panelem administracyjnym w PHP oraz integracją z magazynem Cloudflare R2 Object Storage.**  
> Zrealizowany dla marki **Raricart Live Food Experience** – łączący dynamiczne kalkulatory ofertowe, bezbazodanową architekturę Flat-File CMS oraz skalowalną infrastrukturę CDN.

<p align="center">
  <img src="https://img.shields.io/badge/Serwis%20Live-Raricart.pl-d90429?logo=safari&logoColor=white" alt="Raricart Live">
  <img src="https://img.shields.io/badge/Backend-PHP%208.x%20Modular%20API-777bb4?logo=php&logoColor=white" alt="PHP 8.x">
  <img src="https://img.shields.io/badge/Storage-Cloudflare%20R2%20Object%20Storage-orange?logo=cloudflare&logoColor=white" alt="Cloudflare R2">
  <img src="https://img.shields.io/badge/Media%20CDN-media.raricart.pl-blue?logo=fastly&logoColor=white" alt="Media CDN">
  <img src="https://img.shields.io/badge/Frontend-Vanilla%20JS%20%7C%20CSS%20Grid%20%7C%20BEM-yellow?logo=javascript&logoColor=black" alt="Vanilla Frontend">
  <img src="https://img.shields.io/badge/Security-Password%20Hash%20%7C%20Cache%20Purge-success?logo=shield&logoColor=white" alt="Security">
</p>

<p align="center">
  <a href="https://raricart.pl/" target="_blank">
    <img src="https://img.shields.io/badge/%F0%9F%8C%90_ODWIED%C5%B9_RARICART.PL-d90429?style=for-the-badge" alt="Odwiedź Raricart.pl" />
  </a>
</p>

---

## 🎯 Cel Projektu i Wartość Biznesowa

Raricart to nowoczesna platforma biznesowa wspierająca proces sprzedaży i budowania wizerunku dla usług cateringu premium i live cookingu:
- **Skrócenie cyklu sprzedaży:** Wdrożenie interaktywnego kalkulatora wycen pozwala klientom na natychmiastowe oszacowanie budżetu wydarzenia (dobór pakietu, liczba gości, opcje dodatkowe) i automatyczne przesłanie ustrukturyzowanego leada handlowego.
- **Odciążenie serwera aplikacyjnego:** Migracja multimediów i zdjęć wysokiej rozdzielczości do chmury **Cloudflare R2 Object Storage** (`media.raricart.pl`), gwarantująca błyskawiczne ładowanie strony bez ograniczeń przepustowości hostingu.
- **Bezpieczny i lekki CMS:** Autorski panel administratora umożliwiający edycję cenników, pakietów i galerii bez narzutu ciężkich platform typu WordPress.

---

## 🛠️ Stack Technologiczny i Architektura

| Warstwa | Technologia / Rozwiązanie | Rola w projekcie |
| :--- | :--- | :--- |
| **Frontend** | Semantyczny HTML5, CSS (BEM / Custom Properties), Vanilla JS (ES6+) | Szybki, responsywny interfejs użytkownika, interaktywne kalkulatory wycen, dynamiczne filtrowanie |
| **Backend & API** | PHP 8.x (Architektura modułowa) | Obsługa formularza leadowego (`api/contact.php`), autoryzacja sesyjna, serwowanie danych JSON |
| **Magazyn Obiektowy** | [Cloudflare R2](https://www.cloudflare.com/developer-platform/r2/) (`api/R2Client.php`) | Przechowywanie zdjęć i plików multimedialnych w standardzie S3 API, eliminacja kosztów transferu danych |
| **Dystrybucja Mediów** | Dedykowany CDN `https://media.raricart.pl/` | Globalny cache krawędziowy Cloudflare, automatyczna konwersja i serwowanie formatów WebP |
| **Baza Danych CMS** | Magazyn Flat-File JSON (`assets/data/*.json`) | Baza danych o zerowym narzucie, odporna na ataki typu SQL Injection |
| **Zarządzanie Cache** | Automatyzacja Cloudflare API (`purge_cache.php`) | Błyskawiczna unieważnienie cache po wprowadzeniu zmian w panelu administracyjnym |

---

## 🚀 Kluczowe Moduły Systemu

### 1. Interaktywny Kalkulator i Pozyskiwanie Leadów (`api/contact.php`, `pakiety.php`)
- Wielokrokowa kalkulacja kosztów z uwzględnieniem liczby uczestników, wariantów menu i usług barmańskich.
- Asynchroniczna walidacja (AJAX/Fetch) z natychmiastowym feedbackiem dla użytkownika.
- Zabezpieczenia antyspamowe (Honeypot, sanityzacja danych wejściowych, limitowanie żądań).

### 2. Autorski Klient Cloudflare R2 (`api/R2Client.php`, `api/MediaService.php`)
- Samodzielnie zaimplementowany klient API kompatybilny z S3 do bezpośredniej komunikacji z bucketami Cloudflare R2.
- Bezpieczny upload zdjęć do galerii z poziomu panelu administratora.
- Generowanie publicznych adresów URL w dedykowanej subdomenie `media.raricart.pl`.

### 3. Panel Zarządzania Treściami (`admin/`)
- Dedykowany dashboard do zarządzania ofertą, galeriami i tekstami.
- Bezpieczne procedury pierwszego uruchomienia (`setup.php`) oraz bezpieczne przechowywanie skrótów haseł (`assets/data/config.json`).
- Brak hardkodowanych haseł produkcyjnych w repozytorium.

---

## 🧪 Diagnostyka i Audyt Jakości (`tests/`)

W repozytorium zaimplementowano skrypty weryfikacyjne gwarantujące niezawodność środowiska produkcyjnego:
- **`tests/api_audit.php`** – automatyczny audyt integralności plików konfiguracyjnych, uprawnień do zapisu oraz dostępności endpointów API.
- **`tests/verify_cache.php`** – weryfikacja nagłówków odpowiedzi HTTP serwera oraz poprawności działania buforowania Cloudflare Edge.

Uruchomienie testów lokalnych:
```bash
# Uruchomienie lokalnego środowiska deweloperskiego
start-dev.bat

# Weryfikacja integralności backendu
php tests/api_audit.php
```

---

## 📁 Struktura Repozytorium

```plaintext
raricart/
├── admin/                      # Autorski panel administratora (CMS)
├── api/                        # Moduły backendowe i serwisy integracji
│   ├── contact.php             # Endpoint formularza wyceny i leadów
│   ├── core.php                # Rdzeń konfiguracji i funkcje pomocnicze
│   ├── get_gallery.php         # Serwowanie galerii w formacie JSON
│   ├── MediaService.php        # Warstwa abstrakcji zarządzania mediami
│   ├── PrivateStorageService.php # Zarządzanie magazynem lokalnym i chmurowym
│   └── R2Client.php            # Własny klient API Cloudflare R2 (S3-compatible)
├── assets/
│   ├── css/                    # Style CSS (responsywność, animacje, motyw ciemny/jasny)
│   ├── data/                   # Bezpieczne pliki danych JSON (oferta, galeria, konfiguracja)
│   └── js/                     # Skrypty kalkulatora wycen, galerii i obsługi interfejsu
├── parts/                      # Reużywalne komponenty szablonów PHP (nagłówki, stopki)
├── tests/                      # Skrypty audytowe (api_audit.php, verify_cache.php)
├── index.php                   # Strona główna z prezentacją doświadczenia kulinarno-eventowego
├── pakiety.php                 # Interaktywny katalog pakietów i kalkulator kosztów
├── purge_cache.php             # Skrypt czyszczenia cache Cloudflare Edge
└── README.md                   # Niniejsza dokumentacja techniczna
```

---

## 🔀 Praca Wieloagentowa (3 Przestrzenie Robocze Git Worktrees)

Projekt posiada repozytorium główne `E:\Projekty\raricart` (`main`) oraz 3 izolowane przestrzenie robocze w `E:\Projekty\_workspaces\`:
- **`E:\Projekty\_workspaces\raricart-ws1` (`ws/raricart-1`)** — Backend PHP 8 (`api/`, `admin/`), integracja Cloudflare R2 (`R2Client.php`, `MediaService.php`) & bezpieczeństwo OWASP/ISO 27001
- **`E:\Projekty\_workspaces\raricart-ws2` (`ws/raricart-2`)** — Frontend (`index.php`, `pakiety.php`, `parts/`, `assets/js/`, `assets/css/`) & kalkulator wycen Live Food Stations
- **`E:\Projekty\_workspaces\raricart-ws3` (`ws/raricart-3`)** — Testy jednostkowe (`node:test` + PHP), SEO/GEO (`llms.txt`, `Schema.org`), Lighthouse CI & potoki CI/CD (`npm run sync`)

---

## 👤 Autor i Kontakt

- **Projekt i Wdrożenie:** Krzysztof Początek
- **Profil GitHub:** [@kpoczatek-dev](https://github.com/kpoczatek-dev)
- **Organizacja:** [Twoja Strona WWW](https://github.com/TwojaStronaWWW)
- **Serwis Produkcyjny:** [https://raricart.pl/](https://raricart.pl/)
- **LinkedIn:** [Krzysztof Początek](https://linkedin.com/in/krzysztof-początek-a94a06166)
