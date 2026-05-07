#!/bin/sh

echo "🚀 Laravel starting (Render + Supabase)..."

php artisan config:clear
php artisan cache:clear

php artisan migrate --force

# Seeder nur wenn nötig
php artisan db:seed --class=DatabaseSeeder --force

php artisan serve --host=0.0.0.0 --port=10000