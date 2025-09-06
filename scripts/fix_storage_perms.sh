#!/usr/bin/env bash
set -euo pipefail
cd /var/www/specification_manager
sudo chown -R www-data:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 2775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 0664 {} \;
sudo find storage bootstrap/cache -type d -exec chmod g+s {} \;
sudo setfacl -R  -m u:www-data:rwX,g:www-data:rwX storage bootstrap/cache
sudo setfacl -dR -m u:www-data:rwX,g:www-data:rwX storage/bootstrap/cache
sudo -u www-data bash -lc 'touch storage/logs/laravel.log && chmod 664 storage/logs/laravel.log'
echo "OK"
