#!/bin/bash

# Navigate to the deployment directory
cd /var/www/lottery

# Remove the specific file causing the issue
sudo rm -f firebase-messaging-sw.ts
