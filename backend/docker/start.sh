#!/bin/sh
set -e

composer install --no-interaction

if ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force
fi
# env_file passed the empty key to the container before it was generated
export APP_KEY="$(grep '^APP_KEY=' .env | cut -d= -f2-)"

php artisan migrate --force
exec php artisan serve --host=0.0.0.0 --port=8000 --no-reload
