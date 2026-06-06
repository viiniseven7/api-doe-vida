#!/bin/bash

php artisan config:clear
php artisan migrate --force --no-interaction

apache2-foreground