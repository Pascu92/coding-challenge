FROM php:8.4-cli-alpine
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    icu-dev \
    libzip-dev

RUN docker-php-ext-install intl zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock symfony.lock* /app/

RUN composer install --no-interaction --no-progress --prefer-dist --no-scripts || true

COPY . /app
RUN composer install --no-interaction --no-progress --prefer-dist

RUN mkdir -p var/cache var/log

CMD ["php", "-v"]
