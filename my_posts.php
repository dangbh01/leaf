<?php
session_start();
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy danh sách bài đăng của user
$sql = "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$my_posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaf - Bài đăng của tôi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar { background-color: #28a745 !important; margin-bottom: 20px; }
        .btn-leaf { background-color: #28a745; border-color: #28a745; color: white; }
        .post-card { margin-bottom: 20px; }
        .badge-pending { background-color: #ffc107; color: black; }
        .badge-approved { background-color: #28a745; }
        .badge-rejected { background-color: #dc3545; }
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
                <a class="nav-link" href="my_posts.php">
                    <i class="fas fa-list"></i> Bài đăng của tôi
                </a>
                <a class="nav-link" href="my_orders.php">
                    <i class="fas fa-shopping-cart"></i> Đơn đã nhận
                </a>
                <a class="nav-link" href="logout.php">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="mb-4"><i class="fas fa-list"></i> Bài đăng của tôi</h2>
        
        <?php if(count($my_posts) > 0): ?>
            <div class="row">
                <?php foreach($my_posts as $post): ?>
                <div class="col-md-6">
                    <div class="card post-card">
                        <?php if($post['image'] && file_exists('uploads/posts/' . $post['image'])): ?>
                            <img src="uploads/posts/<?php echo $post['image']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo $post['title']; ?>">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $post['title']; ?></h5>
                            <p class="card-text"><?php echo $post['description']; ?></p>
                            
                            <div class="mb-2">
                                <span class="badge bg-success"><?php echo $post['type']; ?></span>
                                <span class="badge bg-info"><?php echo $post['category']; ?></span>
                                <span class="badge bg-warning"><?php echo $post['status']; ?></span>
                                <span class="badge <?php 
                                    if($post['post_status'] == 'pending') echo 'badge-pending';
                                    elseif($post['post_status'] == 'approved') echo 'badge-approved';
                                    else echo 'badge-rejected';
                                ?>">
                                    <?php echo $post['post_status']; ?>
                                </span>
                            </div>
                            
                            <?php if($post['price'] > 0): ?>
                                <div class="price-tag"><?php echo number_format($post['price']); ?> VNĐ</div>
                            <?php else: ?>
                                <div class="price-tag text-success">Miễn phí</div>
                            <?php endif; ?>
                            
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
                                </small>
                            </div>

                            <!-- Hiển thị số lượng người đã đặt nhận -->
                            <?php
                            $sql_orders = "SELECT COUNT(*) as order_count FROM orders WHERE post_id = ?";
                            $stmt_orders = $pdo->prepare($sql_orders);
                            $stmt_orders->execute([$post['id']]);
                            $order_count = $stmt_orders->fetch()['order_count'];
                            ?>

                            <div class="mt-3">
                                <span class="badge bg-primary">
                                    <i class="fas fa-users"></i> <?php echo $order_count; ?> người đã đặt nhận
                                </span>
                            </div>

                            <div class="mt-3">
                                <a href="view_orders.php?post_id=<?php echo $post['id']; ?>" class="btn btn-leaf btn-sm">
                                    <i class="fas fa-eye"></i> Xem đơn đặt
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h4>Chưa có bài đăng nào</h4>
                <p class="text-muted">Hãy đăng bài để trao đổi đồ dùng!</p>
                <a href="create_post.php" class="btn btn-leaf">Đăng bài ngay</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>