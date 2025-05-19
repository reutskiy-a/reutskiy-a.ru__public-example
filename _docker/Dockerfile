FROM php:8.4.6-fpm-bookworm

RUN apt-get update && apt-get install -y \
      apt-utils \
      libpq-dev \
      libpng-dev \
      libzip-dev \
      zip unzip \
      git \
      iputils-ping \
      sqlite3 \
      libsqlite3-dev \
      nano && \
      docker-php-ext-install pdo_mysql && \
      docker-php-ext-install pdo_sqlite && \
      docker-php-ext-install bcmath && \
      docker-php-ext-install gd && \
      docker-php-ext-install zip && \
      apt-get clean && \
      rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
      touch /var/log/php_error.log && chmod 777 /var/log/php_error.log

COPY ./_docker/php.ini /usr/local/etc/php/conf.d/php.ini

# install composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

WORKDIR /var/www/reutskiy-a.ru/backend/
