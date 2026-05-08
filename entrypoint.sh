#!/bin/sh

echo "🚀 Laravel starting (Render + Supabase)..."

php artisan optimize:clear

php artisan migrate --force

php artisan db:seed --class=DatabaseSeeder --force

php artisan config:cache
php artisan route:cache

php artisan serve --host=0.0.0.0 --port=10000