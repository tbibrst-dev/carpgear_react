#!/bin/bash
# Navigate to the deployment directory
cd /var/www/lottery

# Remove specific files if necessary (be cautious)
sudo rm -f package.json
sudo rm -f firebase-messaging-sw.ts
