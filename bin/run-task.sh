#!/usr/bin/env bash
# Skrypt: bin/run-task.sh
# Cel: Centralny task-runner CLI dla operacji infrastrukturalnych i migracyjnych

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." >/dev/null 2>&1 && pwd)"
TASK=${1:-""}

case "$TASK" in
    "fetch-server-media")
        php "$PROJECT_ROOT/bin/fetch-server-media.php"
        ;;
    "migrate-local-media-to-r2")
        php "$PROJECT_ROOT/bin/migrate-local-media-to-r2.php"
        ;;
    "test-r2-connection")
        php "$PROJECT_ROOT/bin/test-r2-connection.php"
        ;;
    "cf-audit")
        php "$PROJECT_ROOT/bin/cf-audit.php"
        ;;
    "cf-harden")
        php "$PROJECT_ROOT/bin/cf-harden.php"
        ;;
    *)
        echo "Użycie: ./bin/run-task.sh <nazwa-zadania>"
        echo "Dostępne zadania:"
        echo "  fetch-server-media         - Pobiera wszystkie aktywne zdjęcia z serwera produkcyjnego"
        echo "  migrate-local-media-to-r2  - Konwertuje lokalne zdjęcia do WebP i wysyła do Cloudflare R2"
        echo "  test-r2-connection         - Testuje komunikację i zapis do bucketa R2"
        echo "  cf-audit                   - Wykonuje pełny audyt domen i DNS w Cloudflare"
        echo "  cf-harden                  - Aplikuje optymalny baseline bezpieczeństwa SSL/WAF"
        exit 1
        ;;
esac
