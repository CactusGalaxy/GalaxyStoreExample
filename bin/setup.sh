#!/bin/bash

if [ ! -f composer.json ]; then
    echo "Please make sure to run this script from the root directory of this repo."
    exit 1
fi

composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
npm install
npm run build
