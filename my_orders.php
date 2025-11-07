<?php
session_start();
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy danh sách đơn đã đặt
$sql = "SELECT orders.*, posts.title, posts.image, posts.type, posts.price, users.full_name as seller_name
        FROM orders 
        JOIN posts ON orders.post_id = posts.id
        JOIN users ON posts.user_id = users.id
        WHERE orders.buyer_id = ?
        ORDER BY orders.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaf - Đơn đã nhận</title>
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
                <a class="nav-link" href="profile.php">
                    <i class="fas fa-user"></i> <?php echo $_SESSION['username']; ?>
                </a>
                <a class="nav-link" href="my_orders.php">
                    <i class="fas fa-shopping-cart"></i> Đơn đã nhận
                </a>
                <a class="nav-link" href="logout.php">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Đơn đã nhận</h2>
        
        <?php if(count($orders) > 0): ?>
            <div class="row">
                <?php foreach($orders as $order): ?>
                <div class="col-md-6">
                    <div class="card order-card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $order['title']; ?></h5>
                            
                            <div class="mb-2">
                                <strong>Trạng thái:</strong>
                                <span class="status-<?php echo $order['status']; ?>">
                                    <?php 
                                    if($order['status'] == 'pending') echo 'Đang chờ duyệt';
                                    elseif($order['status'] == 'approved') echo 'Đã được duyệt';
                                    else echo 'Đã bị từ chối';
                                    ?>
                                </span>
                            </div>
                            
                            <div class="mb-2">
                                <strong>Hình thức:</strong> <?php echo $order['type']; ?>
                            </div>
                            
                            <?php if($order['price'] > 0): ?>
                                <div class="mb-2">
                                    <strong>Giá:</strong> <?php echo number_format($order['price']); ?> VNĐ
                                </div>
                            <?php endif; ?>
                            
                            <div class="mb-2">
                                <strong>Người bán:</strong> <?php echo $order['seller_name']; ?>
                            </div>
                            
                            <div class="mb-2">
                                <strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                            </div>
                            
                            <?php if($order['message']): ?>
                                <div class="mb-2">
                                    <strong>Lời nhắn của bạn:</strong> 
                                    <p class="text-muted"><?php echo $order['message']; ?></p>
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
                <p class="text-muted">Hãy đặt nhận đồ dùng từ trang chủ!</p>
                <a href="index.php" class="btn btn-leaf">Đến trang chủ</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>