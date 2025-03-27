FROM php:8.4-fpm

# Update package lists and install dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    zip \
    nano \
    unzip \
    libpq-dev \
    git \
    && docker-php-ext-install pdo pdo_pgsql opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install Composer (multi-stage build)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy dependencies
COPY composer.* ./
RUN composer install --no-dev --no-scripts --no-autoloader

# Copy application files
COPY . .
RUN composer dump-autoload --optimize

# Set permissions for storage and cache directories
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R ug+rwx /var/www/storage /var/www/bootstrap/cache

# Copy PHP configuration
COPY .docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

EXPOSE 9000
CMD ["php-fpm"]