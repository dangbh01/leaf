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

# Import schema using PHP instead of mysql command
echo "Setting up database schema..."
php -r "
\$mysql_url = getenv('MYSQL_URL');
if (\$mysql_url) {
    \$url_parts = parse_url(\$mysql_url);
    \$host = \$url_parts['host'] ?? 'localhost';
    \$port = \$url_parts['port'] ?? '3306';
    \$dbname = ltrim(\$url_parts['path'] ?? '/railway', '/');
    \$username = \$url_parts['user'] ?? 'root';
    \$password = \$url_parts['pass'] ?? '';
    
    try {
        \$dsn = \"mysql:host={\$host};port={\$port};dbname={\$dbname};charset=utf8mb4\";
        \$pdo = new PDO(\$dsn, \$username, \$password);
        \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if tables exist
        \$tables = \$pdo->query('SHOW TABLES')->fetchAll();
        if (empty(\$tables)) {
            echo \"Importing schema...\n\";
            \$schema = file_get_contents('/var/www/html/schema.sql');
            \$pdo->exec(\$schema);
            echo \"✅ Schema imported successfully\n\";
            
            // Run seed_admin.php
            echo \"Creating admin user...\n\";
            include '/var/www/html/seed_admin.php';
        } else {
            echo \"✅ Database tables already exist\n\";
        }
    } catch (Exception \$e) {
        echo \"⚠️ Database setup error: \" . \$e->getMessage() . \"\n\";
        echo \"Will retry when Apache starts...\n\";
    }
}
"

# If a command is provided, run it; otherwise default to apache foreground
if [ "$#" -gt 0 ]; then
  exec "$@"
else
  exec docker-php-entrypoint apache2-foreground
fi
