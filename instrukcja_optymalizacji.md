# Optymalizacja Ładowania Obrazów - Raport

## Wprowadzone Zmiany

### 1. Naprawa Cache (Pamięć Podręczna)
Zmieniliśmy konfigurację serwera, aby przeglądarki mogły zapisywać obrazy w pamięci podręcznej.
-   **`.htaccess`**: Usunięto blokady cache i dodano instrukcję `max-age=31536000` (1 rok) dla plików statycznych.
-   **`index.php`**: Usunięto nagłówki PHP, które wymuszały czyszczenie cache przy każdym odświeżeniu strony.

### 2. Optymalizacja Obrazów (WebP)
Wdrożyliśmy system automatycznej konwersji do nowoczesnego formatu WebP, który jest znacznie lżejszy niż JPG/PNG.
-   **`admin/upload.php`**: Każde NOWE zdjęcie wgrywane przez panel administratora zostanie automatycznie:
    -   Zmniejszone do szerokości max 1920px (jeśli jest większe).
    -   Przekonwertowane na format WebP (Quality 85).
-   **`admin/optimize_existing.php`**: Stworzono skrypt do jednorazowej naprawy STARYCH zdjęć (tych, które już są na serwerze).

### 3. Leniwe Ładowanie (Lazy Loading)
Zmieniliśmy sposób wyświetlania kart oferty na stronie głównej.
-   Zamiast `background-image` (CSS), używamy teraz tagów `<img>` z atrybutem `loading="lazy"`.
-   Dzięki temu przeglądarka pobiera te zdjęcia dopiero wtedy, gdy użytkownik do nich przewinie (oszczędność transferu i szybszy start strony).

---

## Jak Uruchomić Optymalizację Starych Zdjęć?

Ponieważ nie mamy dostępu do plików produkcyjnych, musisz wykonać tę operację po wgraniu zmian na serwer.

1.  Wgraj wszystkie pliki na serwer FTP (zwłaszcza folder `site/admin`).
2.  Zaloguj się do panelu administratora (lub po prostu bądź zalogowany).
3.  Wejdź w przeglądarce na adres:
    `https://raricart.pl/site/admin/optimize_existing.php`
4.  Zobaczysz raport z działania skryptu (ile zdjęć zmniejszono i przekonwertowano).
5.  Gdy skrypt skończy pracę, możesz go usunąć z serwera (opcjonalnie).

---

## Weryfikacja

### Jak sprawdzić czy działa?
1.  Otwórz stronę w trybie Incognito.
2.  Otwórz Narzędzia Deweloperskie (F12) -> Zakładka **Network** (Sieć).
3.  Zaznacz filtr **Img**.
4.  Odśwież stronę.
5.  Kliknij na dowolne zdjęcie z oferty lub "O nas".
    -   **Type**: Powinno być `webp` (nie jpeg/png).
    -   **Size**: Powinien być znacznie mniejszy (np. 50-100KB zamiast 1-2MB).
    -   **Cache-Control** (w nagłówkach po prawej): Powinno być `max-age=31536000`.

### Wynik
Dzięki tym zmianom strona powinna ładować się znacznie szybciej, a wynik w Google PageSpeed Insights powinien wzrosnąć.
