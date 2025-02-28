#!/bin/bash
cd /var/www/lottery

# Installing Node dependencies
echo "Installing Node dependencies..."
/usr/bin/npm install

echo "Building React app..."
/usr/bin/node run build

# Copy React build to Apache server root
echo "Copying React build to Apache server root..."
sudo cp -r dist/* /var/www/html/

echo "Changing ownership of files in /var/www..."
sudo chown -R ubuntu:www-data /var/www/*

# Restart services
echo "Restarting Apache2 service..."
sudo systemctl restart apache2

echo "Deployment complete!"
