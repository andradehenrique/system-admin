FROM php:8.2-fpm

ARG uid

RUN usermod -u ${uid} www-data && groupmod -g ${uid} www-data;

RUN apt-get update && \
    apt-get install -y libpng-dev git zlib1g-dev libzip-dev zip rpl && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install pdo_mysql gd zip

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY php.ini $PHP_INI_DIR/conf.d/

USER www-data

EXPOSE 9000
CMD ["php-fpm"]