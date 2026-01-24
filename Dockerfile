FROM dunglas/frankenphp:1-php8.4-alpine

RUN apk add --no-cache nodejs npm

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

COPY . .
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80 443 8000 5173