FROM php:8.2-alpine3.18

RUN set -eux; \
    apk add --no-cache acl libzip; \
    apk add --no-cache --virtual .build-deps ${PHPIZE_DEPS} zlib-dev libzip-dev linux-headers; \
    docker-php-ext-install zip; \
    pecl install xdebug;\
    docker-php-ext-enable xdebug; \
    apk del .build-deps;

RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
