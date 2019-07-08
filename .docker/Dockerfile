FROM php:7.3-apache

COPY . /.docker/api/php
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /.docker/api/php

RUN docker-php-ext-install pdo_mysql bcmath \
    && chown -R www-data:www-data /.docker/api/php \
    && a2enmod rewrite
