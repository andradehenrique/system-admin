FROM php:8.0-apache

ARG uid

RUN usermod -u ${uid} www-data && groupmod -g ${uid} www-data;

RUN apt-get update && \
  apt-get install -y libpng-dev git zlib1g-dev libzip-dev zip rpl && \
  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install pdo_mysql gd zip

RUN a2dismod mpm_event
RUN a2dismod mpm_worker
RUN a2enmod mpm_prefork
RUN a2enmod rewrite

RUN rpl "AllowOverride None" "AllowOverride All" /etc/apache2/apache2.conf

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY php.ini $PHP_INI_DIR/conf.d/

USER www-data