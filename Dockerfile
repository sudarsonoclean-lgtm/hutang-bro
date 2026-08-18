FROM php:8.2-fpm

# Install dependencies sistem & ekstensi PHP
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring gd

WORKDIR /var/www

COPY . .

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# Jalankan server
CMD php artisan serve --host=0.0.0.0 --port=8080
EXPOSE 8080