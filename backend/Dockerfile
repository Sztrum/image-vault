FROM php:8.4-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libzip-dev libmagickwand-dev \
    && docker-php-ext-install exif pdo_mysql zip \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php.ini /usr/local/etc/php/conf.d/image-vault.ini

WORKDIR /var/www/html

EXPOSE 8000

CMD ["sh", "-c", "composer install --no-interaction && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]
