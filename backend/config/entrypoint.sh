#!/bin/sh
set -e
cd /var/www/html

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_FILE="${DB_DATABASE:-database/database.sqlite}"
    mkdir -p "$(dirname "$DB_FILE")"
    touch "$DB_FILE"
    chown -R www-data:www-data "$(dirname "$DB_FILE")"
fi

php artisan migrate --force

if [ "$SEED_DATABASE" = "true" ]; then
    php artisan db:seed --force
fi

if [ -n "$ADMIN_EMAIL" ] && [ -n "$ADMIN_PASSWORD" ]; then
    php artisan shop:create-admin "$ADMIN_EMAIL" --password="$ADMIN_PASSWORD"
fi

php artisan l5-swagger:generate || true

chown -R www-data:www-data storage bootstrap/cache
exec "$@"
