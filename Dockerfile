FROM php:8.4-cli-alpine AS base
WORKDIR /var/www/html
RUN apk add --no-cache icu-libs \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev \
    && docker-php-ext-install intl opcache \
    && apk del .build-deps
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

FROM base AS vendor
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

FROM vendor AS wayfinder
COPY . .
RUN php artisan wayfinder:generate --with-form

FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY resources ./resources
COPY --from=wayfinder /var/www/html/resources/js/actions ./resources/js/actions
COPY --from=wayfinder /var/www/html/resources/js/routes ./resources/js/routes
COPY --from=wayfinder /var/www/html/resources/js/wayfinder ./resources/js/wayfinder
COPY public ./public
COPY vite.config.ts tsconfig.json ./
ARG VITE_APP_NAME=Xelqun
ENV VITE_APP_NAME=$VITE_APP_NAME \
    WAYFINDER_COMMAND=true
RUN npm run build:ssr
EXPOSE 13714

FROM base
COPY --from=vendor /var/www/html/vendor ./vendor
COPY --from=assets /app/public/build ./public/build
COPY --from=assets /app/bootstrap/ssr ./bootstrap/ssr
COPY . .
RUN ln -s ../storage/app/public public/storage \
    && mkdir -p storage/app/private storage/app/public storage/app/demo storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache
USER www-data
EXPOSE 8000
