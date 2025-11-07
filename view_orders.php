<?php
session_start();
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if(!isset($_GET['post_id'])) {
    header("Location: my_posts.php");
    exit();
}

$post_id = $_GET['post_id'];
$user_id = $_SESSION['user_id'];

// Kiểm tra xem bài đăng có thuộc về user không
$sql = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$post_id, $user_id]);
$post = $stmt->fetch();

if(!$post) {
    echo "<script>alert('Bài đăng không tồn tại!'); window.location='my_posts.php';</script>";
    exit();
}

// Lấy danh sách đơn đặt cho bài đăng này
$sql = "SELECT orders.*, users.full_name, users.username, users.email, users.phone 
        FROM orders 
        JOIN users ON orders.buyer_id = users.id 
        WHERE orders.post_id = ? 
        ORDER BY orders.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$post_id]);
$orders = $stmt->fetchAll();

// Xử lý duyệt/từ chối đơn
if(isset($_POST['action'])) {
    $order_id = $_POST['order_id'];
    $action = $_POST['action'];
    
    if($action == 'approve') {
        $sql = "UPDATE orders SET status = 'approved' WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$order_id]);
        
        // Cập nhật trạng thái bài đăng thành 'received'
        $sql = "UPDATE posts SET status = 'received' WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$post_id]);
        
        echo "<script>alert('Đã duyệt đơn đặt nhận!');</script>";
    } elseif($action == 'reject') {
        $sql = "UPDATE orders SET status = 'rejected' WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$order_id]);
        echo "<script>alert('Đã từ chối đơn đặt nhận!');</script>";
    }
    
    // Load lại danh sách
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id]);
    $orders = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaf - Quản lý đơn đặt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar { background-color: #28a745 !important; margin-bottom: 20px; }
        .btn-leaf { background-color: #28a745; border-color: #28a745; color: white; }
        .order-card { margin-bottom: 15px; }
        .status-pending { color: #ffc107; }
        .status-approved { color: #28a745; }
        .status-rejected { color: #dc3545; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-leaf"></i> Leaf
            </a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
                <a class="nav-link" href="my_posts.php">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <a class="nav-link" href="logout.php">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Đơn đặt nhận cho: <?php echo $post['title']; ?></h2>
        
        <?php if(count($orders) > 0): ?>
            <div class="row">
                <?php foreach($orders as $order): ?>
                <div class="col-md-6">
                    <div class="card order-card">
                        <div class="card-body">
    <h5 class="card-title"><?php echo $order['full_name']; ?></h5>
    <p class="text-muted">@<?php echo $order['username']; ?></p>
    
    <div class="mb-2">
        <strong>📧 Email:</strong> 
        <a href="mailto:<?php echo $order['email']; ?>"><?php echo $order['email']; ?></a>
    </div>
    
    <?php if($order['phone']): ?>
        <div class="mb-2">
            <strong>📞 SĐT:</strong> 
            <?php echo $order['phone']; ?>
            <?php if($order['phone']): ?>
                <a href="https://zalo.me/<?php echo $order['phone']; ?>" target="_blank" class="btn btn-success btn-sm ms-2">
                    <i class="fab fa-facebook-messenger"></i> Zalo
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php 
    // Lấy thông tin Facebook từ bảng users
    $sql_fb = "SELECT facebook_link FROM users WHERE id = ?";
    $stmt_fb = $pdo->prepare($sql_fb);
    $stmt_fb->execute([$order['buyer_id']]);
    $user_info = $stmt_fb->fetch();
    ?>
    
    <?php if($user_info && $user_info['facebook_link']): ?>
        <div class="mb-2">
            <strong>👤 Facebook:</strong>
            <a href="<?php echo $user_info['facebook_link']; ?>" target="_blank" class="btn btn-primary btn-sm ms-2">
                <i class="fab fa-facebook"></i> Facebook
            </a>
        </div>
    <?php endif; ?>
    
    <div class="mb-2">
        <strong>📊 Trạng thái:</strong>
        <span class="status-<?php echo $order['status']; ?>">
            <?php 
            if($order['status'] == 'pending') echo '🟡 Đang chờ duyệt';
            elseif($order['status'] == 'approved') echo '✅ Đã được duyệt';
            else echo '❌ Đã bị từ chối';
            ?>
        </span>
    </div>
    
    <div class="mb-2">
        <strong>📅 Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
    </div>
    
    <?php if($order['message']): ?>
        <div class="mb-3">
            <strong>💬 Lời nhắn:</strong>
            <p class="text-muted">"<?php echo $order['message']; ?>"</p>
        </div>
    <?php endif; ?>

    <?php if($order['status'] == 'pending' && $post['status'] == 'available'): ?>
        <div class="mt-3">
            <form method="POST" class="d-inline">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <input type="hidden" name="action" value="approve">
                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Xác nhận duyệt đơn này?')">
                    <i class="fas fa-check"></i> Duyệt
                </button>
            </form>
            
            <form method="POST" class="d-inline">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <input type="hidden" name="action" value="reject">
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Xác nhận từ chối đơn này?')">
                    <i class="fas fa-times"></i> Từ chối
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h4>Chưa có đơn đặt nhận nào</h4>
                <p class="text-muted">Chưa có ai đặt nhận bài đăng của bạn.</p>
                <a href="my_posts.php" class="btn btn-leaf">Quay lại</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>