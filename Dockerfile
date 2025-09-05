# ProjectWork-BE/Dockerfile
FROM php:8.3-fpm

WORKDIR /var/www/html

# ---- System deps & headers (no libonig on PHP 8) ----
# - ca-certificates: for TLS (PDO MySQL to TiDB)
# - libicu-dev:      for intl
# - libzip-dev + zlib1g-dev: for zip
# - libpng-dev + libjpeg62-turbo-dev + libfreetype6-dev + libwebp-dev: for gd
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# ---- Composer ----
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1 COMPOSER_MEMORY_LIMIT=-1

# ---- App files ----
COPY . .
# never ship local env files
RUN rm -f .env .env.production

# Install prod deps & discover packages (no dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction \
 && php artisan config:clear || true \
 && php artisan route:clear || true \
 && php artisan view:clear || true

# Permissions
RUN chmod -R 775 storage bootstrap/cache

# Render will set $PORT at runtime
ENV PORT=8080
EXPOSE 8080

# Always refresh autoload & clear caches on boot
CMD composer dump-autoload -o && \
    php artisan optimize:clear && \
    php artisan serve --host=0.0.0.0 --port=${PORT}
