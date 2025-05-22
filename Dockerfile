# Use PHP 8.1 FPM base
FROM php:8.1-fpm

# Install dependencies and Nginx
RUN apt-get update && apt-get install -y \
    nginx supervisor git unzip libzip-dev libonig-dev curl zip \
    && docker-php-ext-install pdo_mysql zip mbstring

# Copy Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Clear and cache Laravel config
RUN php artisan config:clear \
 && php artisan cache:clear \
 && php artisan config:cache

# Copy nginx config
COPY ./nginx.conf /etc/nginx/sites-available/default

# Copy supervisor config
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port 80 for HTTP
EXPOSE 80

# Start both PHP-FPM and Nginx using Supervisor
CMD ["/usr/bin/supervisord", "-n"]
