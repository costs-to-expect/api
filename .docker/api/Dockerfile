FROM php:8.2-apache

COPY . /var/www/html
COPY .docker/api/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN docker-php-ext-install pdo_mysql bcmath
RUN apt-get update
RUN apt-get install -y git zip unzip default-mysql-client $PHPIZE_DEPS

RUN pecl install pcov \
    && docker-php-ext-enable pcov \
    && echo "pcov.directory=/var/www/html/app" > /usr/local/etc/php/conf.d/pcov.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

WORKDIR /var/www/html

