<?php
session_start();
require_once '../config/database.php';

// Kiểm tra xem user đã đăng nhập và có phải admin không
if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Lấy thông tin user từ database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Kiểm tra nếu không phải admin
if($user['role'] != 'admin') {
    echo "<script>alert('Bạn không có quyền truy cập trang quản trị!'); window.location='../index.php';</script>";
    exit();
}
?>