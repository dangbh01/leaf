<?php
session_start();
require_once 'config/database.php';

if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$post_id = $_GET['id'];

// Lấy thông tin bài đăng
$sql = "SELECT posts.*, users.username, users.full_name, users.phone, users.facebook_link 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        WHERE posts.id = ? AND posts.post_status = 'approved'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if(!$post) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaf - <?php echo $post['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar { background-color: #28a745 !important; margin-bottom: 20px; }
        .btn-leaf { background-color: #28a745; border-color: #28a745; color: white; }
        .post-image { max-height: 500px; object-fit: cover; width: 100%; }
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
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a class="nav-link" href="logout.php">Đăng xuất</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Đăng nhập</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <?php if($post['image'] && file_exists('uploads/posts/' . $post['image'])): ?>
                        <img src="uploads/posts/<?php echo $post['image']; ?>" class="card-img-top post-image" alt="<?php echo $post['title']; ?>">
                    <?php else: ?>
                        <div class="card-img-top post-image bg-light d-flex align-items-center justify-content-center">
                            <i class="fas fa-image fa-5x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h1 class="card-title"><?php echo $post['title']; ?></h1>
                        
                        <div class="mb-3">
                            <span class="badge bg-success fs-6"><?php echo $post['type']; ?></span>
                            <span class="badge bg-info fs-6"><?php echo $post['category']; ?></span>
                            <span class="badge bg-warning fs-6"><?php echo $post['status']; ?></span>
                        </div>
                        
                        <?php if($post['price'] > 0): ?>
                            <div class="price-tag fs-3 mb-3"><?php echo number_format($post['price']); ?> VNĐ</div>
                        <?php else: ?>
                            <div class="price-tag text-success fs-3 mb-3">Miễn phí</div>
                        <?php endif; ?>
                        
                        <p class="card-text fs-5"><?php echo nl2br($post['description']); ?></p>
                        
                        <div class="mt-4">
                            <small class="text-muted">
                                <i class="fas fa-user"></i> Đăng bởi: <?php echo $post['full_name']; ?><br>
                                <i class="fas fa-clock"></i> Ngày đăng: <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">📞 Liên hệ người bán</h5>
                    </div>
                    <div class="card-body">
                        <h6><?php echo $post['full_name']; ?></h6>
                        <p class="text-muted">@<?php echo $post['username']; ?></p>
                        
                        <div class="mb-3">
                            <strong>📧 Email:</strong><br>
                            <a href="mailto:<?php echo $post['contact_email']; ?>"><?php echo $post['contact_email']; ?></a>
                        </div>
                        
                        <?php if($post['phone']): ?>
                            <div class="mb-3">
                                <strong>📞 Điện thoại:</strong><br>
                                <?php echo $post['phone']; ?>
                                <?php if($post['phone']): ?>
                                    <a href="https://zalo.me/<?php echo $post['phone']; ?>" target="_blank" class="btn btn-success btn-sm mt-1">
                                        <i class="fab fa-facebook-messenger"></i> Zalo
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($post['facebook_link']): ?>
                            <div class="mb-3">
                                <strong>👤 Facebook:</strong><br>
                                <a href="<?php echo $post['facebook_link']; ?>" target="_blank" class="btn btn-primary btn-sm mt-1">
                                    <i class="fab fa-facebook"></i> Facebook
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $post['user_id']): ?>
                            <div class="mt-4">
                                <a href="order.php?post_id=<?php echo $post['id']; ?>" class="btn btn-leaf btn-lg w-100" onclick="return confirm('Bạn có chắc muốn đặt nhận sản phẩm này?')">
                                    <i class="fas fa-shopping-cart"></i> Đặt nhận ngay
                                </a>
                            </div>
                        <?php elseif(!isset($_SESSION['user_id'])): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Đăng nhập để đặt nhận sản phẩm này
                            </div>
                            <a href="login.php" class="btn btn-leaf w-100">Đăng nhập</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>