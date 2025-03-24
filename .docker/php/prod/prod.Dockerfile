FROM php:8.4

# Install dependencies
RUN apk --no-cache add \
    curl \
    zip \
    nano \
    unzip \
    postgresql-dev \
    git \
    && docker-php-ext-install pdo pdo_pgsql opcache

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
