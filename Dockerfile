# Use official PHP 8.1 FPM image as base
FROM php:8.1-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libonig-dev curl zip \
    && docker-php-ext-install pdo_mysql zip mbstring

# Install Composer (copy from official composer image)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory inside container
WORKDIR /var/www/html

# Copy all project files into container
COPY . .

# Install PHP dependencies with Composer (no dev for production)
RUN composer install --no-dev --optimize-autoloader

# Clear and cache Laravel config for better performance and to apply any .env changes
RUN php artisan config:clear \
    && php artisan cache:clear \
    && php artisan config:cache

# Expose port 9000 (php-fpm default)
EXPOSE 9000

# Run PHP-FPM server
CMD ["php-fpm"]
