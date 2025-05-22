# Use PHP 8.1 FPM as base image
FROM php:8.1-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    nginx supervisor git unzip libzip-dev libonig-dev curl zip \
    && docker-php-ext-install zip mbstring

# Copy Composer from official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application source
COPY . .

# ✅ Create necessary Laravel storage subdirectories
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache

# Copy PHP-FPM pool config
COPY ./www.conf /usr/local/etc/php-fpm.d/www.conf

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy entrypoint script
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Copy Nginx config
COPY nginx.conf /etc/nginx/sites-available/default

# Copy Supervisor config
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port 80
EXPOSE 80

# Set entrypoint
ENTRYPOINT ["/entrypoint.sh"]
