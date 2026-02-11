FROM dunglas/frankenphp:1-php8.4-alpine

RUN apk add --no-cache \
    nodejs \
    npm \
    git \
    openssh-client \
    zsh \
    nano \
    shadow

RUN install-php-extensions \
    pcntl \
    pdo_pgsql \
    pgsql \
    redis \
    gd \
    intl \
    zip \
    opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-scripts --no-autoloader --no-interaction

COPY . .

RUN mkdir -p storage/app/archives/infrastructure \
             storage/app/archives/identity \
             storage/app/archives/projects && \
    chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

RUN composer dump-autoload --optimize && \
    chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80 443 8000 5173