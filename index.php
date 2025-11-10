<?php
session_start();
require_once 'config/database.php';

// Lấy danh sách bài đăng đã được duyệt
$sql = "SELECT posts.*, users.username, users.full_name 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        WHERE posts.post_status = 'approved' 
        ORDER BY posts.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$posts = $stmt->fetchAll();

// Kiểm tra hồ sơ đã hoàn thiện chưa
$profile_complete = false;
if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql_user = "SELECT full_name, email, phone, facebook_link FROM users WHERE id = ?";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute([$user_id]);
    $user_info = $stmt_user->fetch();
    
    // Kiểm tra các trường bắt buộc
    if($user_info && !empty($user_info['full_name']) && !empty($user_info['email']) && !empty($user_info['phone'])) {
        $profile_complete = true;
    }
}
?>
 
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaf - Trao Đổi Đồ Dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar { background-color: #28a745 !important; margin-bottom: 20px; }
        .btn-leaf { background-color: #28a745; border-color: #28a745; color: white; }
        .btn-leaf:hover { background-color: #218838; border-color: #1e7e34; }
        .post-card { margin-bottom: 20px; transition: transform 0.2s; }
        .post-card:hover { transform: translateY(-5px); }
        .post-image { height: 200px; object-fit: cover; width: 100%; }
        .price-tag { font-size: 1.2em; font-weight: bold; color: #28a745; }
        .profile-warning { border-left: 4px solid #ffc107; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-leaf"></i> Leaf
            </a>
            <div class="navbar-nav">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a class="nav-link" href="profile.php">
                        <i class="fas fa-user"></i> <?php echo $_SESSION['username']; ?>
                    </a>
                    <a class="nav-link" href="search.php">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </a>
                    <a class="nav-link" href="my_posts.php">
                        <i class="fas fa-list"></i> Bài đăng của tôi
                    </a>
                    <a class="nav-link" href="my_orders.php">
                        <i class="fas fa-shopping-cart"></i> Đơn đã nhận
                    </a>
                    
                    <!-- MENU QUẢN TRỊ CHO ADMIN -->
                    <?php if($_SESSION['role'] == 'admin'): ?>
                        <a class="nav-link" href="admin/dashboard.php" style="color: #ff6b6b; font-weight: bold;">
                            <i class="fas fa-crown"></i> Quản trị
                        </a>
                    <?php endif; ?>
                    
                    <a class="nav-link" href="logout.php">Đăng xuất</a>
                <?php else: ?>
                    <a class="nav-link" href="register.php">Đăng ký</a>
                    <a class="nav-link" href="login.php">Đăng nhập</a>
                    <a class="nav-link" href="search.php">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </a>
                <?php endif; ?>

                <!-- MENU CHO TẤT CẢ MỌI NGƯỜI -->
                <a class="nav-link" href="guide.php">
                    <i class="fas fa-book"></i> Hướng dẫn
                </a>
                <a class="nav-link" href="share.php">
                    <i class="fas fa-share-alt"></i> Chia sẻ
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Cảnh báo chưa hoàn thiện hồ sơ -->
        <?php if(isset($_SESSION['user_id']) && !$profile_complete): ?>
            <div class="alert alert-warning profile-warning">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                    <div>
                        <h5 class="mb-1">⚠️ Vui lòng hoàn thiện hồ sơ!</h5>
                        <p class="mb-0">
                            Bạn cần cập nhật <strong>Họ tên, Email và Số điện thoại</strong> để có thể 
                            <strong>đăng bài và đặt nhận</strong> đồ dùng.
                            <a href="profile.php" class="alert-link">Cập nhật ngay →</a>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Banner hướng dẫn cho người mới -->
        <?php if(!isset($_SESSION['user_id'])): ?>
            <div class="alert alert-info d-flex align-items-center">
                <i class="fas fa-info-circle fa-2x me-3"></i>
                <div>
                    <h5 class="mb-1">🎓 Mới sử dụng Leaf?</h5>
                    <p class="mb-0">Xem <a href="guide.php" class="alert-link">hướng dẫn sử dụng</a> để bắt đầu ngay!</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Phần thông báo chào mừng -->
        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="alert alert-success">
                Chào mừng <strong><?php echo $_SESSION['username']; ?></strong>! 
                Bạn đã đăng nhập với vai trò <strong><?php echo $_SESSION['role']; ?></strong>.
                <?php if($profile_complete): ?>
                    <span class="badge bg-success ms-2">✅ Hồ sơ đã hoàn thiện</span>
                <?php else: ?>
                    <span class="badge bg-warning ms-2">⚠️ Cần hoàn thiện hồ sơ</span>
                <?php endif; ?>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Hồ sơ</h5>
                            <p class="card-text">Quản lý thông tin cá nhân</p>
                            <a href="profile.php" class="btn btn-leaf">Đi đến</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Đăng bài</h5>
                            <p class="card-text">Đăng đồ dùng muốn trao đổi</p>
                            <?php if($profile_complete): ?>
                                <a href="create_post.php" class="btn btn-leaf">Đăng ngay</a>
                            <?php else: ?>
                                <button class="btn btn-secondary" onclick="alert('Vui lòng hoàn thiện hồ sơ trước khi đăng bài!')">Đăng ngay</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <h4>Chào mừng đến với Leaf!</h4>
                <p>Đăng nhập để đăng bài và trao đổi đồ dùng học tập.</p>
            </div>
        <?php endif; ?>

        <!-- Phần danh sách bài đăng -->
        <h2 class="mb-4">📚 Danh sách đồ dùng</h2>
        
        <?php if(count($posts) > 0): ?>
            <div class="row">
                <?php foreach($posts as $post): ?>
                <div class="col-md-4">
                    <div class="card post-card">
                        <?php if($post['image'] && file_exists('uploads/posts/' . $post['image'])): ?>
                            <img src="uploads/posts/<?php echo $post['image']; ?>" class="card-img-top post-image" alt="<?php echo $post['title']; ?>">
                        <?php else: ?>
                            <div class="card-img-top post-image bg-light d-flex align-items-center justify-content-center">
                                <div class="text-center">
                                    <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                    <p class="text-muted small">Chưa có ảnh</p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="view_post.php?id=<?php echo $post['id']; ?>" class="text-decoration-none text-dark">
                                    <?php echo $post['title']; ?>
                                </a>
                            </h5>
                            <p class="card-text"><?php echo substr($post['description'], 0, 100); ?>...</p>
                            
                            <div class="mb-2">
                                <span class="badge bg-success"><?php echo $post['type']; ?></span>
                                <span class="badge bg-info"><?php echo $post['category']; ?></span>
                                <span class="badge bg-warning"><?php echo $post['status']; ?></span>
                            </div>
                            
                            <?php if($post['price'] > 0): ?>
                                <div class="price-tag"><?php echo number_format($post['price']); ?> VNĐ</div>
                            <?php else: ?>
                                <div class="price-tag text-success">Miễn phí</div>
                            <?php endif; ?>
                            
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> <?php echo $post['full_name']; ?> |
                                    <i class="fas fa-clock"></i> <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                                </small>
                            </div>
                            
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <?php if($profile_complete): ?>
                                    <a href="order.php?post_id=<?php echo $post['id']; ?>" class="btn btn-leaf w-100 mt-2" onclick="return confirm('Bạn có chắc muốn đặt nhận sản phẩm này?')">
                                        <i class="fas fa-shopping-cart"></i> Đặt nhận
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100 mt-2" onclick="alert('Vui lòng hoàn thiện hồ sơ trước khi đặt nhận!')">
                                        <i class="fas fa-shopping-cart"></i> Đặt nhận
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-secondary w-100 mt-2">
                                    <i class="fas fa-shopping-cart"></i> Đăng nhập để đặt nhận
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h4>Chưa có bài đăng nào</h4>
                <p class="text-muted">Hãy là người đầu tiên đăng bài trao đổi đồ dùng!</p>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if($profile_complete): ?>
                        <a href="create_post.php" class="btn btn-leaf">Đăng bài ngay</a>
                    <?php else: ?>
                        <button class="btn btn-secondary" onclick="alert('Vui lòng hoàn thiện hồ sơ trước khi đăng bài!')">Đăng bài ngay</button>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="btn btn-leaf">Đăng nhập để đăng bài</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Floating Share Button -->
    <div class="position-fixed" style="bottom: 20px; right: 20px; z-index: 1000;">
        <a href="share.php" class="btn btn-leaf btn-lg rounded-circle shadow" 
           style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-share-alt"></i>
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
