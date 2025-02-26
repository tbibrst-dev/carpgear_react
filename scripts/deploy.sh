#!/bin/bash

echo "Pulling latest code from GitHub..."
cd /var/www/lottery

# Reset any local changes
sudo git reset --hard

# Pull the latest code
sudo git pull origin master

# Install dependencies & build React

/home/ubuntu/.nvm/versions/node/v20.18.3/bin/npm install
/home/ubuntu/.nvm/versions/node/v20.18.3/bin/npm run build

# Copy React build to Apache server root
sudo cp -r dist/* /var/www/html/

# Restart services
sudo systemctl restart apache2
