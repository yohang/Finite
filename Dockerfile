ARG PHP_VERSION=8.4

FROM php:${PHP_VERSION}-alpine

ARG DEPENDENCIES=highest

RUN set -eux; \
    apk add --no-cache acl libzip; \
    apk add --no-cache --virtual .build-deps ${PHPIZE_DEPS} zlib-dev libzip-dev; \
    docker-php-ext-install zip opcache; \
    pecl install pcov;\
    docker-php-ext-enable pcov; \
    apk del .build-deps;

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json /app/

RUN set -eux; \
    composer install --no-interaction; \
    if [ "${DEPENDENCIES}" = "lowest" ]; then COMPOSER_MEMORY_LIMIT=-1 composer update --prefer-lowest --no-interaction; fi; \
    if [ "${DEPENDENCIES}" = "highest" ]; then COMPOSER_MEMORY_LIMIT=-1 composer update --no-interaction; fi

COPY --link ./examples /app/examples
COPY --link ./src /app/src
COPY --link ./tests /app/tests
COPY --link ./phpunit.xml.dist /app/
COPY --link ./psalm.xml /app/
