#!/bin/bash
set -e

# Ensure uploads directory exists and is writable
mkdir -p /var/www/html/uploads/posts
chown -R www-data:www-data /var/www/html/uploads || true
chmod -R 755 /var/www/html/uploads || true

# Parse MySQL connection info from MYSQL_URL or use individual vars
if [ -n "$MYSQL_URL" ]; then
    echo "Using MYSQL_URL for connection..."
    # Extract from mysql://user:pass@host:port/dbname
    DB_HOST=$(echo $MYSQL_URL | sed -E 's|mysql://[^:]+:[^@]+@([^:]+):.*|\1|')
    DB_PORT=$(echo $MYSQL_URL | sed -E 's|mysql://[^:]+:[^@]+@[^:]+:([0-9]+)/.*|\1|')
    DB_USER=$(echo $MYSQL_URL | sed -E 's|mysql://([^:]+):.*|\1|')
    DB_PASS=$(echo $MYSQL_URL | sed -E 's|mysql://[^:]+:([^@]+)@.*|\1|')
    DB_NAME=$(echo $MYSQL_URL | sed -E 's|mysql://[^/]+/(.*)|\1|')
fi

# Railway internal networking - skip ping, just wait a bit for MySQL to be ready
echo "Waiting for MySQL to initialize..."
echo "DB_HOST=$DB_HOST"
echo "DB_PORT=$DB_PORT"
echo "DB_USER=$DB_USER"
echo "DB_NAME=$DB_NAME"

# Simple sleep instead of ping (Railway uses private networking)
sleep 5
echo "✅ Proceeding with database connection..."

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
