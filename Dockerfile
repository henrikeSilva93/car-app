FROM php:8.2-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    sqlite3 \
    libsqlite3-dev

RUN docker-php-ext-install pdo_sqlite pdo_mysql mbstring exif pcntl bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY package*.json ./
RUN npm install

COPY composer.json ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY . .

RUN php artisan key:generate --force

RUN composer dump-autoload --optimize-autoloader --no-dev
RUN php artisan package:discover --ansi

RUN chmod -R 755 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
