#!/bin/bash
cd /var/www/html/plorg.net
cp /var/www/html/plorg.net/dist/. /var/www/html/plorg.net/dist_public/. -r
cp /var/www/html/plorg.net/dist/.htaccess /var/www/html/plorg.net/dist_public/.htaccess
cp /var/www/html/plorg.net/public/.htpasswd /var/www/html/plorg.net/dist
cp /var/www/html/plorg.net/public/.htpasswd /var/www/html/plorg.net/dist_public
