#!/bin/sh
set -e
cd /var/www/html
if [ ! -f vendor/autoload.php ] || [ ! -f vendor/laravel/framework/src/Illuminate/Collections/functions.php ]; then
  composer install --no-interaction
fi
exec "$@"
