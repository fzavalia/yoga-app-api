FROM composer:1.8.5 as dependencies

COPY composer.json composer.lock ./
COPY database/seeds database/seeds
COPY database/factories database/factories

RUN composer install --no-dev --no-scripts

FROM php:7.3.6-apache

# enable mod_rewrite so laravel routing can work

RUN a2enmod rewrite

# change apache document root to laravel public folder

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# copy app

COPY . .
COPY --from=dependencies /app/vendor vendor

# permissions

RUN chown -R www-data:www-data ./storage ./bootstrap/cache