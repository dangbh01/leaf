<?php
require_once 'auth.php';

// Lấy danh sách bài đăng chờ duyệt
$sql = "SELECT posts.*, users.username, users.full_name 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        WHERE posts.post_status = 'pending' 
        ORDER BY posts.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$pending_posts = $stmt->fetchAll();

// Xử lý duyệt/từ chối bài đăng
if(isset($_POST['action'])) {
    $post_id = $_POST['post_id'];
    $action = $_POST['action'];
    
    if($action == 'approve') {
        $sql = "UPDATE posts SET post_status = 'approved' WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$post_id]);
        echo "<script>alert('Đã duyệt bài đăng!'); window.location.reload();</script>";
    } elseif($action == 'reject') {
        $sql = "UPDATE posts SET post_status = 'rejected' WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$post_id]);
        echo "<script>alert('Đã từ chối bài đăng!'); window.location.reload();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaf - Duyệt bài đăng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar { background-color: #28a745 !important; margin-bottom: 20px; }
        .sidebar { background-color: #f8f9fa; min-height: 100vh; }
        .post-card { margin-bottom: 20px; border-left: 5px solid #ffc107; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-leaf"></i> Leaf - Quản trị
            </a>
            <div class="navbar-nav">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Bảng điều khiển
                </a>
                <a class="nav-link" href="../index.php">
                    <i class="fas fa-home"></i> Về trang chủ
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
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Tổng quan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="manage_posts.php">
                            <i class="fas fa-list"></i> Duyệt bài đăng
                            <?php if(count($pending_posts) > 0): ?>
                                <span class="badge bg-danger ms-2"><?php echo count($pending_posts); ?></span>
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
                <h2 class="mb-4">📝 Duyệt bài đăng</h2>
                
                <?php if(count($pending_posts) > 0): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Có <strong><?php echo count($pending_posts); ?></strong> bài đăng đang chờ duyệt
                    </div>
                    
                    <div class="row">
                        <?php foreach($pending_posts as $post): ?>
                        <div class="col-md-6">
                            <div class="card post-card">
                                <?php if($post['image'] && file_exists('../uploads/posts/' . $post['image'])): ?>
                                    <img src="../uploads/posts/<?php echo $post['image']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo $post['title']; ?>">
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
                                    </div>
                                    
                                    <?php if($post['price'] > 0): ?>
                                        <div class="price-tag"><strong>Giá:</strong> <?php echo number_format($post['price']); ?> VNĐ</div>
                                    <?php else: ?>
                                        <div class="price-tag text-success"><strong>Giá:</strong> Miễn phí</div>
                                    <?php endif; ?>
                                    
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> <?php echo $post['full_name']; ?> (@<?php echo $post['username']; ?>)<br>
                                            <i class="fas fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
                                        </small>
                                    </div>

                                    <div class="mt-3">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Xác nhận duyệt bài đăng này?')">
                                                <i class="fas fa-check"></i> Duyệt
                                            </button>
                                        </form>
                                        
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Xác nhận từ chối bài đăng này?')">
                                                <i class="fas fa-times"></i> Từ chối
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h4>Không có bài đăng nào chờ duyệt</h4>
                        <p class="text-muted">Tất cả bài đăng đã được duyệt!</p>
                        <a href="dashboard.php" class="btn btn-leaf">Quay lại bảng điều khiển</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>