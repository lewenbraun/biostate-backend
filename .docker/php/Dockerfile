FROM php:8.4-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql gd exif pcntl opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer (multi-stage build)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy dependencies
COPY composer.* ./
RUN composer install --no-dev --no-scripts --no-autoloader

# Copy application
COPY . .
RUN composer dump-autoload --optimize

# Permissions handling
RUN mkdir -p /var/www/storage/logs \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R ug+rwx /var/www/storage /var/www/bootstrap/cache \
    && find /var/www/storage -type d -exec chmod 2775 {} \; \
    && find /var/www/storage -type f -exec chmod 664 {} \;

# PHP configuration
COPY .docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

EXPOSE 9000
CMD ["php-fpm"]
