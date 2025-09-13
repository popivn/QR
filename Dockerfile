# Use the official PHP image as a base image
FROM php:8.2-fpm

# Install system dependencies, Node.js, npm
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    libjpeg-dev \
    libfreetype6-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Cài Chromium (Headless Chrome/Chromium cho chrome-php/chrome & Browsershot)
RUN apt-get update && apt-get install -y chromium \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Create appuser
RUN groupadd -g 1000 appuser && useradd -u 1000 -ms /bin/bash -g appuser appuser

# Create Laravel cache directories
RUN mkdir -p bootstrap/cache \
    && mkdir -p storage/framework/{cache,sessions,views} \
    && mkdir -p storage/logs \
    && mkdir -p storage/app/tmp_bulk_pdfs

# Set proper permissions (run as root before switching user)
RUN chown -R appuser:appuser /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Make artisan executable if it exists
RUN if [ -f /var/www/artisan ]; then chmod +x /var/www/artisan; fi

# Debug: List files to verify artisan exists
RUN ls -la /var/www/ | head -20

# Switch to non-root user
USER appuser

# Expose port
EXPOSE 8000

# Start Laravel with absolute path
CMD ["php", "/var/www/artisan", "serve", "--host=0.0.0.0", "--port=8000"]