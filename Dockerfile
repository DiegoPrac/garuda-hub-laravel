# ==========================================
# GarudaHub - Laravel 13
# Docker deployment untuk Render
# ==========================================

# Tahap 1: Build frontend Vite
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package*.json ./

RUN if [ -f package-lock.json ]; then \
      npm ci --no-audit --no-fund; \
    else \
      npm install --no-audit --no-fund; \
    fi

COPY . .

RUN npm run build


# Tahap 2: PHP 8.3 + Apache
FROM php:8.3-apache-bookworm

WORKDIR /var/www/html

# Install dependensi PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    && docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    bcmath \
    zip \
    intl \
    opcache \
    && rm -rf /var/lib/apt/lists/*

# Konfigurasi PHP untuk production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo "expose_php=Off" > "$PHP_INI_DIR/conf.d/security.ini"

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Install dependency Laravel
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-scripts \
    --optimize-autoloader

# Salin kode aplikasi
COPY . .

# Salin hasil build frontend
COPY --from=frontend /app/public/build ./public/build

# Perbarui autoload dan daftarkan package Laravel
RUN composer dump-autoload --no-dev --optimize --no-scripts \
    && php artisan package:discover --ansi


# Apache harus mengarah ke folder public Laravel
RUN a2enmod rewrite headers \
    && sed -i 's!DocumentRoot /var/www/html!DocumentRoot /var/www/html/public!' \
       /etc/apache2/sites-available/000-default.conf \
    && printf '<Directory /var/www/html/public>\nAllowOverride All\nRequire all granted\n</Directory>\n' \
       > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Gunakan port default Render
RUN sed -i 's/^Listen 80$/Listen 10000/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' \
       /etc/apache2/sites-available/000-default.conf

# Folder yang diperlukan Laravel
RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

EXPOSE 10000

CMD ["apache2-foreground"]
