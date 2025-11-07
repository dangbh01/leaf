<?php
require_once 'auth.php';

// Lấy số liệu thống kê
$sql_users = "SELECT COUNT(*) as total_users FROM users";
$stmt_users = $pdo->prepare($sql_users);
$stmt_users->execute();
$total_users = $stmt_users->fetch()['total_users'];

$sql_posts = "SELECT COUNT(*) as total_posts FROM posts";
$stmt_posts = $pdo->prepare($sql_posts);
$stmt_posts->execute();
$total_posts = $stmt_posts->fetch()['total_posts'];

$sql_pending = "SELECT COUNT(*) as pending_posts FROM posts WHERE post_status = 'pending'";
$stmt_pending = $pdo->prepare($sql_pending);
$stmt_pending->execute();
$pending_posts = $stmt_pending->fetch()['pending_posts'];

$sql_orders = "SELECT COUNT(*) as total_orders FROM orders";
$stmt_orders = $pdo->prepare($sql_orders);
$stmt_orders->execute();
$total_orders = $stmt_orders->fetch()['total_orders'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaf - Quản trị</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar { background-color: #28a745 !important; margin-bottom: 20px; }
        .sidebar { background-color: #f8f9fa; min-height: 100vh; }
        .stat-card { border-radius: 10px; padding: 20px; margin-bottom: 20px; color: white; }
        .stat-card.users { background: linear-gradient(45deg, #FF6B6B, #FF8E53); }
        .stat-card.posts { background: linear-gradient(45deg, #4ECDC4, #44A08D); }
        .stat-card.pending { background: linear-gradient(45deg, #FFD93D, #FF9C3D); }
        .stat-card.orders { background: linear-gradient(45deg, #6B73FF, #000DFF); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-leaf"></i> Leaf - Quản trị
            </a>
            <div class="navbar-nav">
                <a class="nav-link" href="../index.php">
                    <i class="fas fa-home"></i> Về trang chủ
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 sidebar p-4">
                <h4 class="mb-4">📊 Bảng điều khiển</h4>
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Tổng quan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_posts.php">
                            <i class="fas fa-list"></i> Duyệt bài đăng
                            <?php if($pending_posts > 0): ?>
                                <span class="badge bg-danger ms-2"><?php echo $pending_posts; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_users.php">
                            <i class="fas fa-users"></i> Quản lý người dùng
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main content -->
            <div class="col-md-9 p-4">
                <h2 class="mb-4">📈 Tổng quan hệ thống</h2>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card users">
                            <div class="text-center">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <h3><?php echo $total_users; ?></h3>
                                <p>Tổng người dùng</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card posts">
                            <div class="text-center">
                                <i class="fas fa-list fa-3x mb-3"></i>
                                <h3><?php echo $total_posts; ?></h3>
                                <p>Tổng bài đăng</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card pending">
                            <div class="text-center">
                                <i class="fas fa-clock fa-3x mb-3"></i>
                                <h3><?php echo $pending_posts; ?></h3>
                                <p>Bài chờ duyệt</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card orders">
                            <div class="text-center">
                                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                <h3><?php echo $total_orders; ?></h3>
                                <p>Đơn đặt nhận</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">🚀 Hành động nhanh</h5>
                            </div>
                            <div class="card-body">
                                <a href="manage_posts.php" class="btn btn-warning w-100 mb-2">
                                    <i class="fas fa-check-circle"></i> Duyệt bài đăng
                                    <?php if($pending_posts > 0): ?>
                                        <span class="badge bg-danger"><?php echo $pending_posts; ?> bài chờ duyệt</span>
                                    <?php endif; ?>
                                </a>
                                <a href="manage_users.php" class="btn btn-info w-100 mb-2">
                                    <i class="fas fa-user-cog"></i> Quản lý người dùng
                                </a>
                                <a href="../index.php" class="btn btn-secondary w-100">
                                    <i class="fas fa-eye"></i> Xem trang người dùng
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">ℹ️ Thông tin hệ thống</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Phiên bản:</strong> Leaf 1.0</p>
                                <p><strong>Người dùng hiện tại:</strong> <?php echo $user['username']; ?></p>
                                <p><strong>Vai trò:</strong> Quản trị viên</p>
                                <p><strong>Thời gian:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>