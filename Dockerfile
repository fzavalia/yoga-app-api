FROM php:7.3.6-apache

ARG AWS_ACCESS_KEY_ID
ARG AWS_SECRET_ACCESS_KEY
ARG S3_OAUTH_PUBLIC_KEY_PATH
ARG S3_OAUTH_PRIVATE_KEY_PATH

# update

RUN apt-get update

# os dependencies

RUN apt-get install -y \
    python3 \
    python3-pip \
    unzip \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev

# composer

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer

# php extensions

RUN docker-php-ext-install zip pdo_pgsql

# aws cli

RUN pip3 install awscli --upgrade --user

# install dependencies

COPY database/seeds database/seeds
COPY database/factories database/factories

COPY composer.json composer.lock ./

RUN composer install --no-dev --no-scripts

COPY . .

# Download oauth keys

RUN ~/.local/bin/aws s3 cp ${S3_OAUTH_PUBLIC_KEY_PATH} ./storage && \
    ~/.local/bin/aws s3 cp ${S3_OAUTH_PRIVATE_KEY_PATH} ./storage

# permissions

RUN chown -R www-data:www-data ./storage ./bootstrap/cache

# enable mod_rewrite

RUN a2enmod rewrite

# change apache document root

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf