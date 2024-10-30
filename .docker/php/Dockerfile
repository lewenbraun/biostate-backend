FROM php:8.3-fpm

# Установка зависимостей и PHP расширений
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libldap2-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libpq-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql gd exif pcntl ldap

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Настройка рабочей директории
WORKDIR /var/www

# Копирование файлов приложения
COPY . /var/www

# Установка зависимостей приложения
RUN composer install --ignore-platform-req=ext-ldap

# Настройка прав
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Копирование пользовательских конфигураций PHP
COPY .docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

EXPOSE 9000
CMD ["php-fpm"]
