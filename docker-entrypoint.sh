#!/bin/sh
set -e
export COMPOSER_ALLOW_SUPERUSER=1
cd /var/www/html

mkdir -p storage/framework/sessions \
  storage/framework/views \
  storage/framework/cache/data \
  storage/logs \
  bootstrap/cache

chmod -R 777 storage bootstrap/cache

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

exec "$@"
