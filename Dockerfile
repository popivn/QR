FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx supervisor git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install mbstring exif pcntl bcmath gd zip pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock /var/www/
COPY . /var/www

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && composer run-script post-install-cmd || true

RUN chmod -R 755 /var/www/storage /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache || true

# Xóa config mặc định của nginx và copy config Laravel
RUN rm -f /etc/nginx/sites-enabled/default /etc/nginx/conf.d/default.conf
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
