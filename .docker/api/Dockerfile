FROM php:7.4-apache

COPY . /var/www/html
COPY .docker/api/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN docker-php-ext-install pdo_mysql bcmath
RUN apt-get update
RUN apt-get install -y git zip unzip

RUN pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  docker-php-ext-enable redis

RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

WORKDIR /var/www/html
