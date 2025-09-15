# =========================
# Base PHP-FPM + Nginx
# =========================
FROM php:8.2-fpm

# Cài đặt các gói cần thiết
RUN apt-get update && apt-get install -y \
    nginx supervisor git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install mbstring exif pcntl bcmath gd zip pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy file composer trước để tận dụng cache
COPY composer.json composer.lock /var/www/

# Copy toàn bộ source code
COPY . /var/www

# Cài đặt dependencies PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Chạy lại scripts sau khi có source
RUN composer run-script post-install-cmd || true

# Quyền cho storage & cache
RUN chmod -R 755 /var/www/storage /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www

# Laravel optimize
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache || true

# Copy Nginx config
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

# Copy Supervisor config
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Mở port
EXPOSE 8080

# Start bằng Supervisor (quản lý cả nginx + php-fpm)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
