FROM php:8.2-cli

WORKDIR /var/www

# dependencias
RUN apt-get update && apt-get install -y \
    git curl unzip libzip-dev zip \
    && docker-php-ext-install pdo pdo_mysql zip

# instalar composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copiar proyecto
COPY . .

# instalar dependencias
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# permisos
RUN chmod -R 775 storage bootstrap/cache

# puerto
EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000
