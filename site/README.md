# Dokumentacja Strony Raricart

To jest repozytorium projektu strony internetowej Raricart.

## Struktura Katalogów

*   `admin/` - Panel administracyjny.
*   `api/` - Skrypty API obsługujące formularze i dynamiczne akcje.
*   `assets/` - Zasoby statyczne (CSS, JS, Obrazy) oraz dane konfiguracyjne.
*   `parts/` - Fragmenty kodu PHP (np. nagłówek, stopka).

## Panel Administratora

Panel administratora znajduje się pod adresem: `/admin`.

### Logowanie i Hasła

System nie posiada domyślnego hasła "zaszytego" w kodzie. 

Podczas **pierwszego uruchomienia** panelu administratora (`/admin`), zostaniesz automatycznie przekierowany do strony konfiguracyjnej (`setup.php`), gdzie należy ustawić hasło dostępu.

Hasło (w postaci bezpiecznego skrótu/hasha) jest przechowywane w pliku:
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
