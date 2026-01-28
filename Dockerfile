FROM dunglas/frankenphp:1-php8.4-alpine

# 1. Install Node & NPM (Sudah benar)
RUN apk add --no-cache nodejs npm

# 2. Install PHP Extensions (Sudah benar)
RUN install-php-extensions \
    pcntl \
    pdo_pgsql \
    pgsql \
    redis \
    gd \
    intl \
    zip \
    opcache

# 3. Ambil Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 4. TRICK: Copy composer files dulu agar Docker bisa caching layer vendor
# Ini mempercepat build jika kode berubah tapi vendor tidak.
COPY composer.json composer.lock ./

# 5. Jalankan install (Pastikan phpseclib sudah ada di composer.json)
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-scripts --no-autoloader --no-interaction

# 6. Copy seluruh project
COPY . .

# 7. Generate Autoloader & Atur Izin
RUN composer dump-autoload --optimize && \
    chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80 443 8000 5173