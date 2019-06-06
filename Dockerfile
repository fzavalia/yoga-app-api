FROM composer:1.8.5 AS build

WORKDIR /app

COPY . .

RUN composer install

FROM php:7.3.6-apache

WORKDIR /app

COPY --from=build /app .

RUN sed -ri -e 's!/var/www/html!/app/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/app/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf