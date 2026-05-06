#!/bin/sh

echo "🚀 Starting Laravel Container..."

sleep 5

# Laravel Cache
php artisan config:clear
php artisan cache:clear

# Migrationen
php artisan migrate --force

# Seeder nur einmal
if [ ! -f /app/.seeded ]; then
  php artisan db:seed --class=PropertySeeder --force
  touch /app/.seeded
fi

# Laravel starten
php artisan serve --host=0.0.0.0 --port=10000