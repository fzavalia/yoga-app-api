FROM composer:1.8.5 as dependencies

COPY . .

RUN composer install

FROM php:7.3.6-apache

# enable mod_rewrite so laravel routing can work

RUN a2enmod rewrite

# change apache document root to laravel public folder

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# system dependencies

RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

# copy app

COPY --from=dependencies /app .

# permissions

RUN chown -R www-data:www-data ./storage ./bootstrap/cache