#!/bin/bash

cd /home/master/applications/ewnectezvn/public_html
git pull origin main

composer install --no-interaction --prefer-dist --optimize-autoloader

php artisan config:cache
php artisan route:cache
php artisan view:cache