FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN php artisan optimize:clear && php artisan view:clear

RUN chmod -R 777 storage bootstrap/cache

EXPOSE 80