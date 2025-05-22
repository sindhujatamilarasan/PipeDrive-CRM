# Use PHP 8.1 FPM as base image
FROM php:8.1-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    nginx supervisor git unzip libzip-dev libonig-dev curl zip \
    && docker-php-ext-install zip mbstring

# Copy Composer from official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy Laravel project files
COPY . .

# Set correct permissions for Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy entrypoint script
COPY entrypoint.sh /entrypoint.sh

# Make it executable
RUN chmod +x /entrypoint.sh

# Use entrypoint to start container
ENTRYPOINT ["/entrypoint.sh"]


# Clear and cache Laravel configuration
RUN php artisan config:clear \
 && php artisan cache:clear \
 && php artisan config:cache

# Copy nginx config
COPY ./nginx.conf /etc/nginx/sites-available/default

# Copy supervisord config
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose HTTP port
EXPOSE 80

# Start services using Supervisor
CMD ["/usr/bin/supervisord", "-n"]
