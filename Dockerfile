# ProjectWork-BE/Dockerfile
FROM php:8.3-cli

# Workdir
WORKDIR /var/www/html

# System deps
RUN apt-get update && apt-get install -y \
    git curl zip unzip ca-certificates \
    libpng-dev libxml2-dev libzip-dev libicu-dev \
 && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip \
 && update-ca-certificates \
 && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# App files
COPY . .

RUN rm -f .env .env.production

# Prod install (no dev deps), optimize autoload
RUN composer install --no-dev --optimize-autoloader --no-interaction \
 && php artisan config:clear || true \
 && php artisan route:clear || true \
 && php artisan view:clear || true

# Permissions
RUN chmod -R 775 storage bootstrap/cache

# Render sets $PORT; default to 8080 locally
ENV PORT=8080
EXPOSE 8080

CMD composer dump-autoload -o && \
    php artisan optimize:clear && \
    php artisan serve --host=0.0.0.0 --port=${PORT}

