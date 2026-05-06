FROM php:8.2-cli

# System packages
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Arbeitsordner
WORKDIR /app

# Ganze Laravel App kopieren
COPY . .

# Composer installieren
RUN composer install --no-dev --optimize-autoloader

# Laravel Cache
RUN php artisan config:clear
RUN php artisan cache:clear

# Port
EXPOSE 10000

# Start
CMD sh -c "\
php artisan migrate --force && \
php artisan db:seed --class=PropertySeeder --force && \
php artisan serve --host=0.0.0.0 --port=10000"