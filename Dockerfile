FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./

RUN npm ci

COPY resources ./resources
COPY vite.config.js ./

RUN npm run build

FROM richarvey/nginx-php-fpm:3.1.6

ENV WEBROOT /var/www/html/public

WORKDIR /var/www/html

COPY . .
COPY --from=frontend /app/public/build ./public/build
COPY nginx-site.conf /etc/nginx/sites-enabled/default.conf

RUN composer install --no-dev --optimize-autoloader

RUN php artisan l5-swagger:generate

RUN php artisan storage:link || true

RUN chmod -R 777 storage bootstrap/cache

EXPOSE 80
