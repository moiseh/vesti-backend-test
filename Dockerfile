FROM php:7.4-apache

RUN a2enmod rewrite

RUN apt update

RUN apt-get install -y libpq-dev

RUN docker-php-ext-install pdo pgsql pdo_pgsql

RUN apt-get install -y zlib1g-dev libzip-dev && docker-php-ext-install zip

RUN apt install -y curl git unzip

RUN cd ~

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php

RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN composer global require "laravel/lumen-installer"

COPY apache2.conf /etc/apache2/apache2.conf
