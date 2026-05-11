#!/usr/bin/env sh
set -eu

cd /var/www/html

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY must be set in Render before the container starts." >&2
    exit 1
fi

echo "[BOOT] APP_ENV=$APP_ENV"
echo "[BOOT] DB_CONNECTION=${DB_CONNECTION:-not set}"
echo "[BOOT] DB_URL=${DB_URL:-not set}"
echo "[BOOT] DATABASE_URL=${DATABASE_URL:-not set}"

db_url="${DB_URL:-${DATABASE_URL:-}}"

if [ -z "$db_url" ]; then
    echo "[BOOT] ERROR: No database URL found. Cannot proceed." >&2
    echo "[BOOT] Render must provide DB_URL or DATABASE_URL via render.yaml fromDatabase" >&2
    exit 1
fi

echo "[BOOT] Found database URL, forcing pgsql"
export DB_URL="$db_url"
export DATABASE_URL="$db_url"
export DB_CONNECTION=pgsql
export DB_PORT="${DB_PORT:-5432}"

echo "[BOOT] Final DB_CONNECTION=$DB_CONNECTION DB_PORT=$DB_PORT"

php artisan config:cache

attempt=1
until php artisan migrate --force; do
    if [ "$attempt" -ge 10 ]; then
        echo "Database migrations failed after 10 attempts." >&2
        exit 1
    fi

    echo "Waiting for the database to become available..."
    attempt=$((attempt + 1))
    sleep 3
done

php artisan route:cache
php artisan view:cache

export PORT="${PORT:-8080}"
envsubst '$PORT' < /tmp/nginx.conf.template > /tmp/nginx.conf

php-fpm -F &
exec nginx -c /tmp/nginx.conf -g 'daemon off;'