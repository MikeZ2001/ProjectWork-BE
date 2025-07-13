# Use official PHP image with FPM
FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u 1000 -d /home/dev dev \
    && mkdir -p /home/dev/.composer \
    && chown -R dev:dev /home/dev

# Copy application files
COPY . .

# Set permissions
RUN chown -R dev:dev /var/www/html \
    && chmod -R 755 storage \
    && chmod -R 755 bootstrap/cache

# Change current user to dev


# Install Laravel dependencies
RUN composer install --no-interaction --optimize-autoloader

RUN composer dump-autoload --optimize

# Expose port (only needed if using artisan serve, not FPM)
EXPOSE 8000

# Start Laravel dev server (for local dev)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
