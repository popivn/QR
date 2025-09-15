# =========================
# Stage 1: PHP dependencies
# =========================
FROM php:8.2-fpm AS php

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    libpq-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install mbstring exif pcntl bcmath gd zip pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy composer files for caching
COPY composer.json composer.lock /var/www/


# Copy application source
COPY . /var/www

# Install PHP deps (without dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chmod -R 755 /var/www/storage /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www

# Laravel optimize
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# =========================
# Stage 2: Nginx + PHP-FPM
# =========================
FROM nginx:1.25 AS production

# Copy Nginx config
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

# Copy app from PHP stage
COPY --from=php /var/www /var/www

# Copy PHP-FPM socket config
COPY --from=php /usr/local/etc/php-fpm.d/ /usr/local/etc/php-fpm.d/

WORKDIR /var/www

# Expose port
EXPOSE 8080

# Start supervisord (runs php-fpm + nginx together)
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
