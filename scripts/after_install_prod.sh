#!/bin/bash
cd /var/www/lottery

/usr/bin/npm install
/usr/bin/node run build

# Copy React build to Apache server root
sudo cp -r dist/* /var/www/html/
sudo chown -R ubuntu:www-data /var/www/*

# Restart services
sudo systemctl restart apache2
