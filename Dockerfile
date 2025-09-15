FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx supervisor git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install mbstring exif pcntl bcmath gd zip pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Tạo thư mục cho php-fpm socket
RUN mkdir -p /var/run/php

# Cấu hình php-fpm sử dụng socket thay vì TCP port 9000
RUN sed -i 's|listen = 9000|listen = /var/run/php/php-fpm.sock|' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's|;listen.owner = www-data|listen.owner = www-data|' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's|;listen.group = www-data|listen.group = www-data|' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's|;listen.mode = 0660|listen.mode = 0660|' /usr/local/etc/php-fpm.d/www.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock /var/www/
COPY . /var/www

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && composer run-script post-install-cmd || true

RUN chmod -R 755 /var/www/storage /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chown -R www-data:www-data /var/run/php \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache || true

# Xóa config mặc định của nginx và copy config Laravel
RUN rm -f /etc/nginx/sites-enabled/default /etc/nginx/conf.d/default.conf
COPY ./nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Tạo thư mục log cho nginx
RUN mkdir -p /var/log/nginx && chown -R www-data:www-data /var/log/nginx

COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
