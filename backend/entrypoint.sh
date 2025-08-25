#!/bin/sh
set -e

php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true
exec php artisan serve --host=0.0.0.0 --port=8000
