# ===========================
# STAGE 1 : Builder Composer
# ===========================
FROM composer:2 AS vendor
WORKDIR /app

COPY composer.json composer.lock ./

# Ici on ajoute juste l'option --ignore-platform-req=ext-mongodb
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --optimize-autoloader --ignore-platform-req=ext-mongodb --no-scripts



# ===========================
# STAGE 2 : Image PHP/Apache
# ===========================
FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev libssl-dev pkg-config \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu mongodb \
    && docker-php-ext-enable apcu mongodb \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

WORKDIR /var/www/html

# Copie uniquement vendor depuis composer
COPY --from=vendor /app/vendor ./vendor

# Pour un déploiement prod complet, décommente :
# COPY . .
