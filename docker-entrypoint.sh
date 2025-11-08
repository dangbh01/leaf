#!/bin/bash
set -e

# Ensure uploads directory exists and is writable
mkdir -p /var/www/html/uploads/posts
chown -R www-data:www-data /var/www/html/uploads || true
chmod -R 755 /var/www/html/uploads || true

# Wait for MySQL to be ready
echo "Waiting for MySQL..."
echo "DB_HOST=$DB_HOST"
echo "DB_PORT=$DB_PORT"
echo "DB_USER=$DB_USER"
echo "DB_NAME=$DB_NAME"

# Try to wait for MySQL with timeout
MAX_RETRIES=30
RETRY_COUNT=0

while [ $RETRY_COUNT -lt $MAX_RETRIES ]; do
    if mysqladmin ping -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" --silent 2>/dev/null; then
        echo "✅ MySQL is ready!"
        break
    fi
    RETRY_COUNT=$((RETRY_COUNT + 1))
    echo "Attempt $RETRY_COUNT/$MAX_RETRIES - waiting for MySQL..."
    sleep 2
done

if [ $RETRY_COUNT -eq $MAX_RETRIES ]; then
    echo "⚠️ WARNING: MySQL not responding after $MAX_RETRIES attempts, but continuing anyway..."
fi

# Import schema if tables don't exist
echo "Checking database schema..."
TABLES=$(mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" -D"$DB_NAME" -e "SHOW TABLES;" 2>/dev/null)
if [ $? -eq 0 ] && [ -z "$TABLES" ]; then
    echo "Importing schema..."
    mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < /var/www/html/schema.sql
    
    # Seed admin user after importing schema
    echo "Creating admin user..."
    php /var/www/html/seed_admin.php || echo "⚠️ Admin user creation failed or already exists"
else
    echo "✅ Database tables already exist, skipping schema import"
fi

# If a command is provided, run it; otherwise default to apache foreground
if [ "$#" -gt 0 ]; then
  exec "$@"
else
  exec docker-php-entrypoint apache2-foreground
fi
