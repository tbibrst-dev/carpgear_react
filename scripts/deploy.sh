#!/bin/bash

echo "Pulling latest code from GitHub..."
cd /var/www/lottery

# Reset any local changes
sudo git reset --hard

# Pull the latest code
sudo git pull origin master

# Install dependencies & build React
sudo npm install
sudo npm run build

# Copy React build to Apache server root
sudo cp -r dist/* /var/www/html/

# Deploy WordPress (only updating wp-content)
sudo cp -r competition/wp-content/* /var/www/lottery/competition/wp-content/

# Restart services
sudo systemctl restart apache2
