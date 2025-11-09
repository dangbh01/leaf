<?php
require_once 'auth.php';

// Lấy danh sách users
$sql = "SELECT * FROM users ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll();

// Xử lý cập nhật role
if (isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];
    // Ngăn người dùng tự hạ role của chính họ
    if (isset($_SESSION['user_id']) && (string)$user_id === (string)$_SESSION['user_id']) {
        echo "<script>alert('Bạn không thể thay đổi vai trò của chính mình.'); window.location.href = 'manage_users.php';</script>";
        exit;
    }

    // Ngan người dùng thay đổi role của master_admin
    $sql = "SELECT role FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if ($user && $user['role'] === 'master_admin') {
        echo "<script>alert('Bạn không thể thay đổi vai trò của Master Admin.'); window.location.href = 'manage_users.php';</script>";
        exit;
    }

    $sql = "UPDATE users SET role = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$new_role, $user_id]);

    echo "<script>alert('Cập nhật role thành công!'); window.location.reload();</script>";
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaf - Quản lý người dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar {
            background-color: #28a745 !important;
            margin-bottom: 20px;
        }

        .sidebar {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .badge-admin {
            background-color: #dc3545;
        }

        .badge-teacher {
            background-color: #0d6efd;
        }

        .badge-student {
            background-color: #198754;
        }
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
                        <a class="nav-link" href="manage_posts.php">
                            <i class="fas fa-list"></i> Duyệt bài đăng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="manage_users.php">
                            <i class="fas fa-users"></i> Quản lý người dùng
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main content -->
            <div class="col-md-9 p-4">
                <h2 class="mb-4">👥 Quản lý người dùng</h2>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên đăng nhập</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Vai trò</th>
                                        <th>Ngày tạo</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td>
                                                <strong><?php echo $user['username']; ?></strong>
                                                <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                                    <span class="badge bg-info">Bạn</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $user['full_name']; ?></td>
                                            <td><?php echo $user['email']; ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo $user['role']; ?>">
                                                    <?php echo $user['role']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                            <td>
                                                <?php if ((string)$user['id'] === (string)$_SESSION['user_id']): ?>
                                                    <div class="d-flex flex-column">
                                                        <span class="badge bg-secondary"><?php echo $user['role']; ?></span>
                                                        <small class="text-muted">Không thể thay đổi vai trò của chính bạn</small>
                                                    </div>
                                                <?php else: ?>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <?php if ($user['role'] === 'master_admin'): ?>
                                                            <span class="badge bg-danger">Master Admin</span>
                                                        <?php else: ?>

                                                            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                                                <option value="student" <?= $user['role'] == 'student' ? 'selected' : '' ?>>Học sinh</option>
                                                                <option value="teacher" <?= $user['role'] == 'teacher' ? 'selected' : '' ?>>Giáo viên</option>
                                                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Quản trị</option>
                                                                <option value="master_admin" <?= $user['role'] == 'master_admin' ? 'selected' : '' ?>>Master Admin</option>
                                                            </select>
                                                            <input type="hidden" name="update_role" value="1">
                                                        <?php endif; ?>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>