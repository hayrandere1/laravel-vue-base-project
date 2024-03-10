#!/bin/bash
service cron start
service apache2 start

chown -R www-data.www-data /var/www/base/storage
chown -R www-data.www-data /var/www/base/bootstrap/cache
#chown -R www-data.www-data /var/www/base/resources/lang

cd /var/www/base && php artisan optimize:clear

sleep infinity
