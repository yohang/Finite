ARG PHP_VERSION=8.3

FROM php:${PHP_VERSION}-alpine

ARG DEPENDENCIES=highest

RUN set -eux; \
    apk add --no-cache acl libzip; \
    apk add --no-cache --virtual .build-deps ${PHPIZE_DEPS} zlib-dev libzip-dev linux-headers; \
    docker-php-ext-install zip; \
    pecl install xdebug;\
    docker-php-ext-enable xdebug; \
    apk del .build-deps;

RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json /app/

RUN set -eux; \
    composer install --no-interaction; \
    if [ "${DEPENDENCIES}" = "lowest" ]; then COMPOSER_MEMORY_LIMIT=-1 composer update --prefer-lowest --no-interaction; fi; \
    if [ "${DEPENDENCIES}" = "highest" ]; then COMPOSER_MEMORY_LIMIT=-1 composer update --no-interaction; fi

COPY ./examples /app/examples
COPY ./src /app/src
COPY ./tests /app/tests
COPY ./phpunit.xml.dist /app/
COPY ./psalm.xml /app/
