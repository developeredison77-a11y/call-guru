FROM richarvey/nginx-php-fpm:3.1.6

ENV WEBROOT /var/www/html/public

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:clear && php artisan route:clear && php artisan view:clear

RUN php artisan storage:link

RUN chmod -R 777 storage bootstrap/cache

EXPOSE 80