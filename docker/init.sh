#/bin/bash
echo "Starting PHP development environment"
cd /var/www/dbdiff
composer update

php-fpm
