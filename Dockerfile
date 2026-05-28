FROM richarvey/nginx-php-fpm:3.1.6

ENV WEBROOT /var/www/html/public

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN php artisan storage:link || true

RUN chmod -R 777 storage bootstrap/cache

EXPOSE 80