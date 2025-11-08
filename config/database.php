<?php
// Railway MySQL connection support
$mysql_url = getenv('MYSQL_URL');

if ($mysql_url) {
    // Parse Railway MYSQL_URL: mysql://user:pass@host:port/dbname
    $url_parts = parse_url($mysql_url);
    $host = $url_parts['host'] ?? 'localhost';
    $port = $url_parts['port'] ?? '3306';
    $dbname = ltrim($url_parts['path'] ?? '/traodododung_db', '/');
    $username = $url_parts['user'] ?? 'root';
    $password = $url_parts['pass'] ?? '';
} else {
    // Use individual environment variables (for local dev or other platforms)
    $host = getenv('DB_HOST') ?: 'localhost';
    $port = getenv('DB_PORT') ?: '3306';
    $dbname = getenv('DB_NAME') ?: 'traodododung_db';
    $username = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASS') ?: '';
}

// Build DSN including port if provided
$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // set default fetch mode to associative
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // In production you might log this instead of die()
    die("Lỗi kết nối database: " . $e->getMessage());
}
?>