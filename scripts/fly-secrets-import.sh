#!/usr/bin/env bash
set -euo pipefail

APP="${FLY_APP:-ali-charisma-ecommerce}"
ENV_FILE="${1:-.env}"

if [[ ! -f "$ENV_FILE" ]]; then
    echo "Missing $ENV_FILE" >&2
    exit 1
fi

if ! command -v flyctl >/dev/null 2>&1; then
    echo "Install flyctl first: https://fly.io/docs/flyctl/install/" >&2
    exit 1
fi

get_env() {
    grep -E "^${1}=" "$ENV_FILE" | head -1 | cut -d= -f2- | sed 's/^"\(.*\)"$/\1/'
}

APP_KEY="$(get_env APP_KEY)"
DB_USERNAME="$(get_env DB_USERNAME)"
DB_PASSWORD="$(get_env DB_PASSWORD)"

for var in APP_KEY DB_USERNAME DB_PASSWORD; do
    if [[ -z "${!var}" ]]; then
        echo "Missing $var in $ENV_FILE" >&2
        exit 1
    fi
done

flyctl secrets set \
    APP_KEY="$APP_KEY" \
    DB_USERNAME="$DB_USERNAME" \
    DB_PASSWORD="$DB_PASSWORD" \
    --app "$APP"

echo "Secrets imported to $APP"
