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

# Entrypoint ausführbar machen
RUN chmod +x /app/entrypoint.sh

# Port
EXPOSE 10000

# Startscript
ENTRYPOINT ["/bin/sh", "/app/entrypoint.sh"]