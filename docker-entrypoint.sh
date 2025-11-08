#!/bin/bash
set -e

# Ensure uploads directory exists and is writable
mkdir -p /var/www/html/uploads/posts
chown -R www-data:www-data /var/www/html/uploads || true
chmod -R 755 /var/www/html/uploads || true

# If a command is provided, run it; otherwise default to apache foreground
if [ "$#" -gt 0 ]; then
  exec "$@"
else
  exec docker-php-entrypoint apache2-foreground
fi
