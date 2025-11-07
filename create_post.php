<?php
session_start();
require_once 'config/database.php';

// Kiểm tra nếu chưa đăng nhập thì về trang đăng nhập
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Xử lý khi form được gửi
if($_POST) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $contact_email = $_POST['contact_email'];
    $user_id = $_SESSION['user_id'];
    
    // Xử lý upload ảnh - CODE ĐÃ SỬA
    $image_name = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        
        // Kiểm tra định dạng ảnh
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $image['type'];
        
        if(in_array($file_type, $allowed_types)) {
            // Tạo tên file an toàn
            $file_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $image_name = time() . '_' . uniqid() . '.' . $file_extension;
            $upload_path = 'uploads/posts/' . $image_name;
            
            // Tạo thư mục nếu chưa tồn tại
            if (!file_exists('uploads/posts')) {
                mkdir('uploads/posts', 0777, true);
            }
            
            // Upload ảnh
            if(move_uploaded_file($image['tmp_name'], $upload_path)) {
                // Thành công
            } else {
                echo "<script>alert('Lỗi upload ảnh! Vui lòng thử lại.');</script>";
            }
        } else {
            echo "<script>alert('Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, WebP)!');</script>";
        }
    }
    
    // Lưu vào database
    $sql = "INSERT INTO posts (user_id, title, description, image, category, type, price, contact_email) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if($stmt->execute([$user_id, $title, $description, $image_name, $category, $type, $price, $contact_email])) {
        echo "<script>alert('Đăng bài thành công! Bài của bạn đang chờ duyệt.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Lỗi đăng bài!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaf - Đăng bài</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar { background-color: #28a745 !important; margin-bottom: 20px; }
        .btn-leaf { background-color: #28a745; border-color: #28a745; color: white; }
        .btn-leaf:hover { background-color: #218838; border-color: #1e7e34; }
        .create-post-card { max-width: 800px; margin: 0 auto; }
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
                <a class="nav-link" href="logout.php">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="create-post-card">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle"></i> Đăng bài mới</h4>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="VD: Sách Toán lớp 10" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Loại sản phẩm <span class="text-danger">*</span></label>
                            <select name="category" class="form-control" required>
                                <option value="">-- Chọn loại --</option>
                                <option value="Sách">Sách</option>
                                <option value="Bút">Bút</option>
                                <option value="Máy tính">Máy tính</option>
                                <option value="Vở">Vở</option>
                                <option value="Balô">Balô</option>
                                <option value="Đồng phục">Đồng phục</option>
                                <option value="Dụng cụ học tập">Dụng cụ học tập</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hình thức <span class="text-danger">*</span></label>
                            <select name="type" class="form-control" required>
                                <option value="">-- Chọn hình thức --</option>
                                <option value="Tặng">Tặng</option>
                                <option value="Trao đổi">Trao đổi</option>
                                <option value="Bán">Bán</option>
                                <option value="Cho mượn">Cho mượn</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giá (VNĐ)</label>
                            <input type="number" name="price" class="form-control" placeholder="VD: 50000" min="0" value="0">
                            <small class="text-muted">Để 0 nếu là tặng hoặc trao đổi</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Mô tả chi tiết về sản phẩm..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ảnh sản phẩm</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Chọn ảnh sản phẩm (JPEG, PNG, GIF, WebP - tối đa 2MB)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email liên hệ <span class="text-danger">*</span></label>
                            <input type="email" name="contact_email" class="form-control" value="<?php echo $_SESSION['username'] . '@gmail.com'; ?>" required>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            Bài đăng của bạn sẽ được kiểm duyệt trước khi hiển thị công khai.
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-leaf btn-lg">
                                <i class="fas fa-paper-plane"></i> Đăng bài
                            </button>
                            <a href="index.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>