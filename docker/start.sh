#!/bin/sh

set -eu

# Render exposes the HTTP port through $PORT (10000 by default). Apache's
# image defaults to port 80, so configure both its listener and virtual host
# immediately before it starts.
APACHE_PORT="${PORT:-80}"
sed -ri "s/^Listen [0-9]+$/Listen ${APACHE_PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${APACHE_PORT}>/" /etc/apache2/sites-available/000-default.conf

php artisan storage:link --force
php artisan migrate --force

if [ "${APP_SEED_ON_DEPLOY:-false}" = "true" ]; then
    php artisan db:seed --force
fi

php artisan config:cache
php artisan view:clear

exec apache2-foreground
