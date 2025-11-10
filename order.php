<?php
session_start();
require_once 'config/database.php';

// Kiểm tra đăng nhập
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kiểm tra nếu có post_id được gửi đến
if(isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    $user_id = $_SESSION['user_id'];
    
    // Kiểm tra xem bài đăng có tồn tại không
    $sql = "SELECT * FROM posts WHERE id = ? AND post_status = 'approved'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    
    if(!$post) {
        echo "<script>alert('Bài đăng không tồn tại!'); window.location='index.php';</script>";
        exit();
    }
    
    // Kiểm tra xem user đã đặt nhận bài này chưa
    $sql = "SELECT * FROM orders WHERE post_id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id, $user_id]);
    $existing_order = $stmt->fetch();
    
    if($existing_order) {
        echo "<script>alert('Bạn đã đặt nhận bài đăng này rồi!'); window.location='index.php';</script>";
        exit();
    }
    
    // Tạo đơn đặt nhận
    $sql = "INSERT INTO orders (post_id, user_id, message) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    $message = "Tôi muốn đặt nhận sản phẩm này";
    
    if($stmt->execute([$post_id, $user_id, $message])) {
        echo "<script>alert('Đặt nhận thành công! Người đăng sẽ liên hệ với bạn.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Lỗi đặt nhận! Vui lòng thử lại.'); window.location='index.php';</script>";
    }
} else {
    header("Location: index.php");
    exit();
}
?>