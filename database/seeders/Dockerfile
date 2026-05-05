FROM php:8.2-cli

# System dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# App kopieren
WORKDIR /app
COPY . .

# Dependencies installieren
RUN composer install --no-dev --optimize-autoloader

# Laravel vorbereiten
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Port
EXPOSE 10000

# Start
CMD php artisan serve --host=0.0.0.0 --port=10000