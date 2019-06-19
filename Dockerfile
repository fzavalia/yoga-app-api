FROM composer:1.8.5 as dependencies

COPY composer.json composer.json
COPY composer.lock composer.lock
COPY database/seeds database/seeds
COPY database/factories database/factories

RUN composer install --no-ansi --no-dev --no-interaction --no-progress --no-scripts --optimize-autoloader

FROM php:7.3.6-apache

# enable mod_rewrite so laravel routing can work

RUN a2enmod rewrite

# change apache document root to laravel public folder

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# system dependencies

RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

# copy app

COPY . .

# copy dependencies from previous stage 

COPY --from=dependencies /app/vendor vendor

# create default files for the required elements to prevent an error

RUN touch ./storage/oauth-private.key && touch ./storage/oauth-public.key && touch ./.env

# permissions

RUN chown -R www-data:www-data ./storage ./bootstrap/cache

CMD php artisan migrate --force && apache2-foreground