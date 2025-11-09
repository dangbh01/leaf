<?php
session_start();
require_once 'config/database.php';

// Kiểm tra nếu chưa đăng nhập thì về trang đăng nhập
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Lấy thông tin user từ database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Xử lý khi form được gửi
if($_POST) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $phone = $_POST['phone'];
    $facebook_link = $_POST['facebook_link'];
    $homeroom_teacher = $_POST['homeroom_teacher'];
    $class = $_POST['class'];
    
    $sql = "UPDATE users SET full_name=?, email=?, role=?, phone=?, facebook_link=?, homeroom_teacher=?, class=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    
    if($stmt->execute([$full_name, $email, $role, $phone, $facebook_link, $homeroom_teacher, $class, $user_id])) {
        echo "<script>alert('Cập nhật thông tin thành công!');</script>";
        // Cập nhật lại session
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $role;
    } else {
        echo "<script>alert('Lỗi cập nhật!');</script>";
    }
    
    // Load lại thông tin user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaf - Hồ sơ cá nhân</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar { background-color: #28a745 !important; margin-bottom: 20px; }
        .btn-leaf { background-color: #28a745; border-color: #28a745; color: white; }
        .btn-leaf:hover { background-color: #218838; border-color: #1e7e34; }
        .profile-card { max-width: 800px; margin: 0 auto; }
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
                    <i class="fas fa-user"></i> <?php echo $user['username']; ?>
                </a>
                <a class="nav-link" href="logout.php">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="profile-card">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-user-circle"></i> Hồ sơ cá nhân</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" 
                                       value="<?php echo $user['full_name']; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?php echo $user['email']; ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                                <select name="role" class="form-control" required>
                                    <option value="student" <?php echo ($user['role'] == 'student') ? 'selected' : ''; ?>>Học sinh</option>
                                    <option value="teacher" <?php echo ($user['role'] == 'teacher') ? 'selected' : ''; ?>>Giáo viên</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control" 
                                       value="<?php echo $user['phone']; ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Link Facebook</label>
                            <input type="text" name="facebook_link" class="form-control" 
                                   value="<?php echo $user['facebook_link']; ?>" 
                                   placeholder="https://facebook.com/username">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giáo viên chủ nhiệm</label>
                                <input type="text" name="homeroom_teacher" class="form-control" 
                                       value="<?php echo $user['homeroom_teacher']; ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lớp</label>
                                <input type="text" name="class" class="form-control" 
                                       value="<?php echo $user['class']; ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" value="<?php echo $user['username']; ?>" disabled>
                            <small class="text-muted">Tên đăng nhập không thể thay đổi</small>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-leaf btn-lg">
                                <i class="fas fa-save"></i> Cập nhật thông tin
                            </button>
                            <a href="index.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- PHẦN THỐNG KÊ MỚI -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">📊 Bài đăng</h5>
                            <?php
                            $sql_posts = "SELECT COUNT(*) as total_posts FROM posts WHERE user_id = ?";
                            $stmt_posts = $pdo->prepare($sql_posts);
                            $stmt_posts->execute([$user_id]);
                            $total_posts = $stmt_posts->fetch()['total_posts'];
                            ?>
                            <h3 class="text-success"><?php echo $total_posts; ?></h3>
                            <p class="card-text">Tổng bài đăng</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">🛒 Đơn đã nhận</h5>
                            <?php
                            $sql_orders = "SELECT COUNT(*) as total_orders FROM orders WHERE user_id = ?";
                            $stmt_orders = $pdo->prepare($sql_orders);
                            $stmt_orders->execute([$user_id]);
                            $total_orders = $stmt_orders->fetch()['total_orders'];
                            ?>
                            <h3 class="text-info"><?php echo $total_orders; ?></h3>
                            <p class="card-text">Đơn đã đặt</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">⭐ Vai trò</h5>
                            <h3 class="text-warning">
                                <?php 
                                if($user['role'] == 'admin') echo '👑 Admin';
                                elseif($user['role'] == 'teacher') echo '📚 Giáo viên';
                                else echo '🎒 Học sinh';
                                ?>
                            </h3>
                            <p class="card-text">Trong hệ thống</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>