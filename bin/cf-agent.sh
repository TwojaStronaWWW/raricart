#!/usr/bin/env bash
# Skrypt: bin/cf-agent.sh
# Cel: Interfejs CLI do zarządzania infrastrukturą Cloudflare (REST API v4)

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." >/dev/null 2>&1 && pwd)"
SECRETS_FILE="$PROJECT_ROOT/config/secrets.php"

if [ ! -f "$SECRETS_FILE" ]; then
    echo "BŁĄD: Brak pliku $SECRETS_FILE" >&2
    exit 1
fi

# Ekstrakcja poświadczeń
CF_API_TOKEN=$(grep -oP "(?<=define\('CF_API_TOKEN', ')[^']*" "$SECRETS_FILE" || true)
CF_GLOBAL_API_KEY=$(grep -oP "(?<=define\('CF_GLOBAL_API_KEY', ')[^']*" "$SECRETS_FILE" || true)
CF_EMAIL=$(grep -oP "(?<=define\('CF_EMAIL', ')[^']*" "$SECRETS_FILE" || true)
CF_ACCOUNT_ID=$(grep -oP "(?<=define\('CF_ACCOUNT_ID', ')[^']*" "$SECRETS_FILE" || true)

if [ -z "$CF_API_TOKEN" ] && [ -z "$CF_GLOBAL_API_KEY" ]; then
    echo "BŁĄD: Brak CF_API_TOKEN lub CF_GLOBAL_API_KEY w config/secrets.php" >&2
    exit 1
fi

COMMAND=${1:-""}
TARGET=${2:-""}

cf_api_request() {
    local method=$1
    local endpoint=$2
    local data=${3:-""}

    local curl_opts=(-s -X "$method" "https://api.cloudflare.com/client/v4/$endpoint" -H "Content-Type: application/json")

    if [ -n "$CF_API_TOKEN" ]; then
        curl_opts+=(-H "Authorization: Bearer $CF_API_TOKEN")
    elif [ -n "$CF_GLOBAL_API_KEY" ] && [ -n "$CF_EMAIL" ]; then
        curl_opts+=(-H "X-Auth-Key: $CF_GLOBAL_API_KEY" -H "X-Auth-Email: $CF_EMAIL")
    else
        echo "BŁĄD: Używając CF_GLOBAL_API_KEY musisz także zdefiniować CF_EMAIL." >&2
        exit 1
    fi

    if [ -n "$data" ]; then
        curl_opts+=(-d "$data")
    fi

    curl "${curl_opts[@]}"
}

get_zone_id() {
    local domain=$1
    local response
    response=$(cf_api_request "GET" "zones?name=$domain")
    echo "$response" | grep -oP '"id":"\K[^"]+' | head -n 1
}

case "$COMMAND" in
    "get-zones")
        echo "Pobieranie stref Cloudflare..."
        cf_api_request "GET" "zones?per_page=50" | grep -oP '"name":"\K[^"]+'
        ;;

    "audit")
        echo "=== AUDYT INFRASTRUKTURY CLOUDFLARE ==="
        response=$(cf_api_request "GET" "zones?per_page=50")
        echo "$response"
        ;;

    "purge-cache")
        if [ -z "$TARGET" ]; then
            echo "BŁĄD: Podaj domenę (np. raricart.pl)" >&2
            exit 1
        fi
        ZONE_ID=$(get_zone_id "$TARGET")
        if [ -z "$ZONE_ID" ]; then
            echo "BŁĄD: Nie znaleziono strefy dla $TARGET" >&2
            exit 1
        fi
        echo "Czyszczenie cache dla strefy $TARGET ($ZONE_ID)..."
        cf_api_request "POST" "zones/$ZONE_ID/purge_cache" '{"purge_everything":true}'
        ;;

    "dns-list")
        if [ -z "$TARGET" ]; then
            echo "BŁĄD: Podaj domenę (np. raricart.pl)" >&2
            exit 1
        fi
        ZONE_ID=$(get_zone_id "$TARGET")
        cf_api_request "GET" "zones/$ZONE_ID/dns_records?per_page=100" | grep -oP '"name":"[^"]+","type":"[^"]+","content":"[^"]+"'
        ;;

    *)
        echo "Użycie: ./bin/cf-agent.sh <komenda> [cel]"
        echo "Dostępne komendy:"
        echo "  get-zones              - Wyświetla listę domen na koncie"
        echo "  audit                  - Pobiera pełny zrzut wszystkich stref i ustawień"
        echo "  purge-cache <domena>   - Czyści cały cache (Purge Everything) dla podanej domeny"
        echo "  dns-list <domena>      - Zwraca rekordy DNS dla podanej domeny"
        exit 1
        ;;
esac
