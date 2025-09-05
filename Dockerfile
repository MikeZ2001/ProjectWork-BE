# ProjectWork-BE/Dockerfile
FROM php:8.3-cli

# Workdir
WORKDIR /var/www/html

# System deps
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
  && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
  && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# App files
COPY . .

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

# IMPORTANT: listen on 0.0.0.0 and $PORT
CMD php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=${PORT}
