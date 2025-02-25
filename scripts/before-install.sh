#!/bin/bash
# Navigate to the deployment directory
cd /var/www/lottery && shopt -s extglob dotglob && rm -rf !(competition|.*)
