#!/usr/bin/env bash

# Instalamos las dependencias PHP si aún no están
composer install --no-interaction --prefer-dist --optimize-autoloader

# Ejecutamos migraciones
php artisan migrate --force

# Servimos la app
php artisan serve --host=0.0.0.0 --port=10000
