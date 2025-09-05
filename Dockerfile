# Use official PHP image with FPM
FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies + CA certs + build tools + zip
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    ca-certificates \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && update-ca-certificates

# Install Composer early so subsequent steps can use it
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Environment for Composer in containers
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u 1000 -d /home/dev dev \
    && mkdir -p /home/dev/.composer \
    && chown -R dev:dev /home/dev

# Copy application files
COPY . .

# Add entrypoint and make it executable
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set permissions
RUN chown -R dev:dev /var/www/html \
    && chmod -R 755 storage \
    && chmod -R 755 bootstrap/cache

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-progress
RUN composer dump-autoload --optimize

# Expose port (only needed if using artisan serve, not FPM)
EXPOSE 8000

# Use entrypoint and start Laravel
ENTRYPOINT ["sh", "/usr/local/bin/entrypoint.sh"]
CMD ["sh", "-lc", "php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]
