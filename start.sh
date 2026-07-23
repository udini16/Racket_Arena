#!/bin/bash

# Clear Laravel caches
php artisan optimize:clear

# Force migrations to run without asking for confirmation
php artisan migrate --force

# Start the Apache web server
apache2-foreground