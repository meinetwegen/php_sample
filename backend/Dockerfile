# Build context: ./backend  (this folder contains the Laravel app in ./backend and service configs in ./config)
FROM composer:2 AS vendor
WORKDIR /app
COPY backend/composer.json backend/composer.lock* ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --ignore-platform-reqs
COPY backend/ ./
RUN composer dump-autoload --no-dev --optimize --no-scripts

FROM php:8.4-fpm-alpine
RUN apk add --no-cache nginx supervisor \
    && docker-php-ext-install pdo_mysql opcache

WORKDIR /var/www/html
COPY --from=vendor /app ./
RUN php artisan package:discover --ansi

COPY config/nginx/conf.d/default.conf /etc/nginx/http.d/default.conf
COPY config/php/conf.d/app.ini /usr/local/etc/php/conf.d/app.ini
COPY config/supervisor/supervisord.conf /etc/supervisord.conf
COPY config/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
ENTRYPOINT ["entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
