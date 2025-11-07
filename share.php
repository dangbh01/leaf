<?php
session_start();
require_once 'config/database.php';

$website_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/index.php';
$website_url_encoded = urlencode($website_url);
$share_message = urlencode('🌿 Leaf - Ứng dụng trao đổi đồ dùng học tập. Truy cập ngay: ' . $website_url);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaf - Chia sẻ ứng dụng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar { background-color: #28a745 !important; margin-bottom: 20px; }
        .btn-leaf { background-color: #28a745; border-color: #28a745; color: white; }
        .share-card { max-width: 600px; margin: 0 auto; text-align: center; }
        .qr-code { border: 10px solid white; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
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
        <div class="share-card">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0"><i class="fas fa-share-alt"></i> Chia sẻ Leaf</h3>
                </div>
                <div class="card-body p-5">
                    <!-- QR Code -->
                    <div class="mb-4">
                        <h4>📱 Quét QR Code để truy cập</h4>
                        <div class="qr-code d-inline-block">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo $website_url_encoded; ?>" 
                                 alt="QR Code Leaf" class="img-fluid">
                        </div>
                        <p class="text-muted mt-2">Quét mã QR bằng camera điện thoại</p>
                    </div>

                    <hr>

                    <!-- Link chia sẻ -->
                    <div class="mb-4">
                        <h4>🔗 Copy link chia sẻ</h4>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="shareLink" 
                                   value="<?php echo $website_url; ?>" 
                                   readonly>
                            <button class="btn btn-leaf" onclick="copyLink()">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                        <p class="text-muted">Chia sẻ link này với bạn bè</p>
                    </div>

                    <hr>

                    <!-- Chia sẻ mạng xã hội -->
                    <div class="mb-4">
                        <h4>🌐 Chia sẻ trên mạng xã hội</h4>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <!-- Facebook -->
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $website_url_encoded; ?>" 
                               target="_blank" class="btn btn-primary mb-2">
                                <i class="fab fa-facebook"></i> Facebook
                            </a>
                            
                            <!-- Zalo -->
                            <a href="https://zalo.me/share?text=<?php echo $share_message; ?>" 
                               target="_blank" class="btn btn-info mb-2">
                                <i class="fab fa-facebook-messenger"></i> Zalo
                            </a>
                            
                            <!-- Copy text -->
                            <button class="btn btn-secondary mb-2" onclick="copyText()">
                                <i class="fas fa-comment"></i> Copy tin nhắn
                            </button>
                        </div>
                    </div>

                    <!-- Thông tin app -->
                    <div class="alert alert-info">
                        <h5>🌿 Leaf - Trao Đổi Đồ Dùng Học Tập</h5>
                        <p class="mb-1">✅ Đăng bài trao đổi đồ dùng</p>
                        <p class="mb-1">✅ Đặt nhận đồ dùng miễn phí</p>
                        <p class="mb-1">✅ Kết nối học sinh trong trường</p>
                        <p class="mb-0">✅ Dễ dàng sử dụng</p>
                    </div>

                    <!-- Nút hành động -->
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <a href="index.php" class="btn btn-leaf">
                            <i class="fas fa-home"></i> Về trang chủ
                        </a>
                        <a href="guide.php" class="btn btn-success">
                            <i class="fas fa-book"></i> Hướng dẫn sử dụng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyLink() {
            const linkInput = document.getElementById('shareLink');
            linkInput.select();
            linkInput.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(linkInput.value);
            alert('✅ Đã copy link!');
        }

        function copyText() {
            const text = `🌿 Leaf - Ứng dụng trao đổi đồ dùng học tập

✅ Đăng bài trao đổi đồ dùng
✅ Đặt nhận đồ dùng miễn phí  
✅ Kết nối học sinh trong trường
✅ Dễ dàng sử dụng

Truy cập ngay: <?php echo $website_url; ?>`;
            
            navigator.clipboard.writeText(text).then(function() {
                alert('✅ Đã copy tin nhắn!');
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>