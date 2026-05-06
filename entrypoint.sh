#!/bin/sh

echo "🚀 Starting Laravel Container..."

sleep 5

php artisan config:clear
php artisan cache:clear

php artisan migrate --force

# ✅ NUR EINMAL Seeder ausführen
if [ ! -f /app/.seeded ]; then
  php artisan db:seed --force
  touch /app/.seeded
fi

php artisan serve --host=0.0.0.0 --port=10000