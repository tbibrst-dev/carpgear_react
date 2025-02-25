#!/usr/bin/env bash
set -euo pipefail

cd /var/www/lottery || exit 1
shopt -s dotglob extglob

sudo rm -rf !(competition|.*)
