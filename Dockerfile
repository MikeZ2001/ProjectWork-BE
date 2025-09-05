# ProjectWork-BE/Dockerfile
FROM php:8.3-cli

WORKDIR /var/www/html

# ---- System deps & headers (no libonig on PHP 8) ----
# - ca-certificates: for TLS (PDO MySQL to TiDB)
# - libicu-dev:      for intl
# - libzip-dev + zlib1g-dev: for zip
# - libpng-dev + libjpeg62-turbo-dev + libfreetype6-dev + libwebp-dev: for gd
RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl zip unzip ca-certificates \
    libicu-dev libzip-dev zlib1g-dev \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev libwebp-dev \
 && update-ca-certificates \
 && rm -rf /var/lib/apt/lists/*

# ---- PHP extensions ----
# Configure GD to use JPEG/FreeType/WebP; then install all needed exts
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install -j$(nproc) \
    pdo_mysql mbstring exif pcntl bcmath intl zip gd

# ---- Composer ----
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1 COMPOSER_MEMORY_LIMIT=-1

# ---- App files ----
COPY . .
# never ship local env files
RUN rm -f .env .env.production

# Install prod deps & discover packages (no dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction \
 && php artisan package:discover --ansi || true

# Permissions
RUN chmod -R 775 storage bootstrap/cache

# Render will set $PORT at runtime
ENV PORT=8080
EXPOSE 8080

# Always refresh autoload & clear caches on boot
CMD composer dump-autoload -o && \
    php artisan optimize:clear && \
    php artisan serve --host=0.0.0.0 --port=${PORT}
