<?php
// seed_admin.php - Create admin user for Leaf
require_once 'config/database.php';

// Get admin credentials from environment variables or use defaults for local dev
$admin = [
    'username' => getenv('ADMIN_USER') ?: die("❌ Error: ADMIN_USER environment variable is required\n"),
    'password' => getenv('ADMIN_PASS') ?: die("❌ Error: ADMIN_PASS environment variable is required\n"),
    'email' => getenv('ADMIN_EMAIL') ?: die("❌ Error: ADMIN_EMAIL environment variable is required\n"),
    'full_name' => getenv('ADMIN_FULL_NAME') ?: 'Administrator',
    'phone' => getenv('ADMIN_PHONE') ?: '',
    'role' => 'admin'
];

// Check if admin already exists
$check_sql = "SELECT id FROM users WHERE username = ?";
$stmt = $pdo->prepare($check_sql);
$stmt->execute([$admin['username']]);

if ($stmt->fetch()) {
    die("❌ Admin user '{$admin['username']}' already exists!\n");
}

// Create admin user with hashed password
$sql = "INSERT INTO users (username, password, email, full_name, phone, role) 
        VALUES (?, ?, ?, ?, ?, ?)";

try {
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([
        $admin['username'],
        password_hash($admin['password'], PASSWORD_DEFAULT),
        $admin['email'],
        $admin['full_name'],
        $admin['phone'],
        $admin['role']
    ]);

    if ($success) {
        echo "✅ Admin user created successfully!\n";
        echo "Username: {$admin['username']}\n";
        echo "Password: {$admin['password']}\n";
        echo "\nLogin at: http://your-domain/login.php\n";
    } else {
        echo "❌ Failed to create admin user.\n";
    }
} catch (PDOException $e) {
    die("❌ Error: " . $e->getMessage() . "\n");
}