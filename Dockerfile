# (Option PROD) Builder Composer
# FROM composer:2 AS vendor
# WORKDIR /app
# COPY composer.json composer.lock ./
# RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --optimize-autoloader

FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev libssl-dev pkg-config \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && pecl install mongodb-2.1.1 \
    && docker-php-ext-enable apcu mongodb \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

WORKDIR /var/www/html

# En dev, le volume va écraser ces fichiers — c’est ok
COPY . .

# (Option PROD) Copie du vendor depuis le stage composer
# COPY --from=vendor /app/vendor ./vendor

# (optionnel) droits si tu écris dans var/ ou cache
# RUN chown -R www-data:www-data /var/www/html
