#!/usr/bin/env sh
set -eu

cd /var/www/html

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY must be set in Render before the container starts." >&2
    exit 1
fi

if [ -z "${DB_CONNECTION:-}" ] && [ -n "${DB_URL:-}${DATABASE_URL:-}" ]; then
    db_url="${DB_URL:-$DATABASE_URL}"
    case "$db_url" in
        postgres://*|postgresql://*)
            export DB_CONNECTION=pgsql
            ;;
        mysql://*)
            export DB_CONNECTION=mysql
            ;;
        mariadb://*)
            export DB_CONNECTION=mariadb
            ;;
    esac
fi

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