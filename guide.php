<?php
session_start();
require_once 'config/database.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaf - Hướng dẫn sử dụng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar { background-color: #28a745 !important; margin-bottom: 20px; }
        .btn-leaf { background-color: #28a745; border-color: #28a745; color: white; }
        .guide-card { max-width: 900px; margin: 0 auto; }
        .step-card { border-left: 4px solid #28a745; margin-bottom: 20px; }
        .step-number { background: #28a745; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }
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
                <a class="nav-link" href="guide.php">
                    <i class="fas fa-book"></i> Hướng dẫn
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
        <div class="guide-card">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="text-success">📖 Hướng dẫn sử dụng Leaf</h1>
                <p class="lead">Học cách sử dụng ứng dụng trao đổi đồ dùng học tập</p>
            </div>

            <!-- Quick Start -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-rocket"></i> Bắt đầu nhanh</h4>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="step-number mx-auto mb-3">1</div>
                            <h5>Đăng ký tài khoản</h5>
                            <p class="text-muted">Tạo tài khoản mới hoặc đăng nhập</p>
                        </div>
                        <div class="col-md-4">
                            <div class="step-number mx-auto mb-3">2</div>
                            <h5>Hoàn thiện hồ sơ</h5>
                            <p class="text-muted">Thêm thông tin cá nhân và liên hệ</p>
                        </div>
                        <div class="col-md-4">
                            <div class="step-number mx-auto mb-3">3</div>
                            <h5>Bắt đầu sử dụng</h5>
                            <p class="text-muted">Đăng bài hoặc tìm đồ dùng</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- For New Users -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-plus"></i> Cho người mới bắt đầu</h4>
                </div>
                <div class="card-body">
                    <div class="step-card p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="step-number me-3">1</div>
                            <h5 class="mb-0">Đăng ký tài khoản</h5>
                        </div>
                        <p><strong>Bước 1:</strong> Vào trang chủ → <strong>Ấn "Đăng ký"</strong></p>
                        <p><strong>Bước 2:</strong> Điền đầy đủ thông tin: Họ tên, Tên đăng nhập, Email, Mật khẩu</p>
                        <p><strong>Bước 3:</strong> Ấn nút <span class="badge bg-success">Đăng ký</span></p>
                    </div>

                    <div class="step-card p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="step-number me-3">2</div>
                            <h5 class="mb-0">Hoàn thiện hồ sơ</h5>
                        </div>
                        <p><strong>Bước 1:</strong> Đăng nhập → <strong>Ấn vào tên của bạn</strong> trên menu</p>
                        <p><strong>Bước 2:</strong> Điền đầy đủ thông tin:</p>
                        <ul>
                            <li>Vai trò (Học sinh/Giáo viên)</li>
                            <li>Số điện thoại (quan trọng để liên hệ)</li>
                            <li>Link Facebook</li>
                            <li>Lớp và giáo viên chủ nhiệm</li>
                        </ul>
                        <p><strong>Bước 3:</strong> Ấn nút <span class="badge bg-success">Cập nhật thông tin</span></p>
                    </div>
                </div>
            </div>

            <!-- How to Post -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Cách đăng bài</h4>
                </div>
                <div class="card-body">
                    <div class="step-card p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="step-number me-3">1</div>
                            <h5 class="mb-0">Tạo bài đăng mới</h5>
                        </div>
                        <p><strong>Bước 1:</strong> Đăng nhập → <strong>Ấn "Đăng ngay"</strong> trên trang chủ</p>
                        <p><strong>Bước 2:</strong> Điền thông tin sản phẩm:</p>
                        <ul>
                            <li><strong>Tên sản phẩm:</strong> Tên rõ ràng, dễ hiểu</li>
                            <li><strong>Loại sản phẩm:</strong> Chọn đúng loại (Sách, Bút, Máy tính...)</li>
                            <li><strong>Hình thức:</strong> Chọn Tặng, Trao đổi, Bán, Cho mượn</li>
                            <li><strong>Giá:</strong> Nhập giá nếu là bán, để 0 nếu tặng</li>
                            <li><strong>Mô tả:</strong> Mô tả chi tiết tình trạng sản phẩm</li>
                            <li><strong>Ảnh:</strong> Chọn ảnh rõ nét của sản phẩm</li>
                        </ul>
                        <p><strong>Bước 3:</strong> Ấn nút <span class="badge bg-success">Đăng bài</span></p>
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Lưu ý:</strong> Bài đăng sẽ được kiểm duyệt trước khi hiển thị công khai
                        </div>
                    </div>
                </div>
            </div>

            <!-- How to Order -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0"><i class="fas fa-shopping-cart"></i> Cách đặt nhận đồ dùng</h4>
                </div>
                <div class="card-body">
                    <div class="step-card p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="step-number me-3">1</div>
                            <h5 class="mb-0">Tìm kiếm đồ dùng</h5>
                        </div>
                        <p><strong>Bước 1:</strong> Vào trang chủ để xem danh sách đồ dùng</p>
                        <p><strong>Bước 2:</strong> Ấn vào <strong>tiêu đề bài đăng</strong> để xem chi tiết</p>
                        <p><strong>Bước 3:</strong> Xem kỹ thông tin, ảnh và mô tả sản phẩm</p>
                    </div>

                    <div class="step-card p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="step-number me-3">2</div>
                            <h5 class="mb-0">Đặt nhận sản phẩm</h5>
                        </div>
                        <p><strong>Bước 1:</strong> Trên trang chi tiết hoặc trang chủ, <strong>Ấn "Đặt nhận"</strong></p>
                        <p><strong>Bước 2:</strong> Xác nhận đặt nhận khi hiện hộp thoại</p>
                        <p><strong>Bước 3:</strong> Chờ người đăng bài duyệt đơn của bạn</p>
                    </div>

                    <div class="step-card p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="step-number me-3">3</div>
                            <h5 class="mb-0">Theo dõi đơn hàng</h5>
                        </div>
                        <p><strong>Bước 1:</strong> Vào menu → <strong>Ấn "Đơn đã nhận"</strong></p>
                        <p><strong>Bước 2:</strong> Xem trạng thái đơn hàng:</p>
                        <ul>
                            <li>🟡 <strong>Đang chờ duyệt:</strong> Người bán chưa phản hồi</li>
                            <li>✅ <strong>Đã được duyệt:</strong> Liên hệ người bán để nhận hàng</li>
                            <li>❌ <strong>Đã bị từ chối:</strong> Tìm sản phẩm khác</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- For Sellers -->
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-store"></i> Cho người bán/người đăng bài</h4>
                </div>
                <div class="card-body">
                    <div class="step-card p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="step-number me-3">1</div>
                            <h5 class="mb-0">Quản lý bài đăng</h5>
                        </div>
                        <p><strong>Bước 1:</strong> Vào menu → <strong>Ấn "Bài đăng của tôi"</strong></p>
                        <p><strong>Bước 2:</strong> Xem tất cả bài đăng bạn đã đăng</p>
                        <p><strong>Bước 3:</strong> Xem số lượng người đã đặt nhận mỗi bài</p>
                    </div>

                    <div class="step-card p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="step-number me-3">2</div>
                            <h5 class="mb-0">Duyệt đơn đặt hàng</h5>
                        </div>
                        <p><strong>Bước 1:</strong> Trong "Bài đăng của tôi", <strong>Ấn "Xem đơn đặt"</strong></p>
                        <p><strong>Bước 2:</strong> Xem danh sách người đã đặt nhận</p>
                        <p><strong>Bước 3:</strong> Chọn người bạn muốn bán/tặng:</p>
                        <ul>
                            <li>Ấn <span class="badge bg-success">Duyệt</span> để chấp nhận</li>
                            <li>Ấn <span class="badge bg-danger">Từ chối</span> để từ chối</li>
                        </ul>
                        <p><strong>Bước 4:</strong> Liên hệ với người được duyệt qua Zalo/Facebook</p>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h4 class="mb-0"><i class="fas fa-lightbulb"></i> Mẹo sử dụng hiệu quả</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>📸 Cho ảnh đẹp</h6>
                            <ul>
                                <li>Chụp ảnh rõ nét, đủ sáng</li>
                                <li>Chụp nhiều góc độ sản phẩm</li>
                                <li>Ảnh thật, không dùng ảnh mạng</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>📝 Mô tả chi tiết</h6>
                            <ul>
                                <li>Ghi rõ tình trạng sản phẩm</li>
                                <li>Nói rõ lý do tặng/bán</li>
                                <li>Thông tin liên hệ đầy đủ</li>
                            </ul>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6>🤝 Giao dịch an toàn</h6>
                            <ul>
                                <li>Gặp mặt ở nơi công cộng</li>
                                <li>Kiểm tra sản phẩm kỹ trước khi nhận</li>
                                <li>Giữ thái độ lịch sự, tôn trọng</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>🚀 Sử dụng nhanh</h6>
                            <ul>
                                <li>Lưu link trang chủ vào bookmark</li>
                                <li>Cập nhật thông tin liên hệ thường xuyên</li>
                                <li>Theo dõi đơn hàng trong "Đơn đã nhận"</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="text-center mt-5">
                <h4>Sẵn sàng bắt đầu chưa?</h4>
                <p class="text-muted mb-4">Tham gia cộng đồng Leaf ngay hôm nay!</p>
                <div class="d-flex justify-content-center gap-3">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="index.php" class="btn btn-leaf btn-lg">
                            <i class="fas fa-home"></i> Về trang chủ
                        </a>
                        <a href="create_post.php" class="btn btn-success btn-lg">
                            <i class="fas fa-plus"></i> Đăng bài ngay
                        </a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-leaf btn-lg">
                            <i class="fas fa-user-plus"></i> Đăng ký ngay
                        </a>
                        <a href="login.php" class="btn btn-success btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Đăng nhập
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>