<?php
session_start();
require_once 'config/database.php';

// Thu thập tham số tìm kiếm và lọc
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$types = isset($_GET['type']) && is_array($_GET['type']) ? array_filter($_GET['type']) : [];
$categories = isset($_GET['category']) && is_array($_GET['category']) ? array_filter($_GET['category']) : [];

// Danh sách cố định cho hiển thị
$allTypes = ['Bán','Cho mượn','Tặng','Trao đổi'];
$allCategories = ['Sách','Bút','Máy tính','Vở','Balô','Đồng phục','Dụng cụ học tập','Khác'];

// Xây dựng câu truy vấn động
$where = ["posts.post_status = 'approved'"]; // chỉ hiển thị bài đã duyệt
$params = [];

if($keyword !== '') {
    $where[] = "(posts.title LIKE ? OR posts.description LIKE ?)";
    $like = '%'.$keyword.'%';
    $params[] = $like; $params[] = $like;
}

if(count($types) > 0) {
    // Giới hạn giá trị để tránh injection (đã dùng prepared nhưng vẫn lọc)
    $validTypes = array_intersect($types, $allTypes);
    if(count($validTypes)) {
        $placeholders = implode(',', array_fill(0, count($validTypes), '?'));
        $where[] = "posts.type IN ($placeholders)";
        foreach($validTypes as $t) { $params[] = $t; }
    }
}

if(count($categories) > 0) {
    $validCategories = array_intersect($categories, $allCategories);
    if(count($validCategories)) {
        $placeholders = implode(',', array_fill(0, count($validCategories), '?'));
        $where[] = "posts.category IN ($placeholders)";
        foreach($validCategories as $c) { $params[] = $c; }
    }
}

$sql = "SELECT posts.*, users.full_name, users.username FROM posts JOIN users ON posts.user_id = users.id";
if(count($where)) {
    $sql .= ' WHERE '.implode(' AND ', $where);
}
$sql .= ' ORDER BY posts.created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm & Lọc - Leaf</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar { background-color: #28a745 !important; margin-bottom: 15px; }
        .btn-leaf { background-color: #28a745; border-color: #28a745; color:#fff; }
        .btn-leaf:hover { background-color:#218838; }
        .post-image { height:180px; object-fit:cover; width:100%; }
        .filter-trigger { position:relative; }
        .offcanvas-bottom { height:65vh; border-radius:16px 16px 0 0; }
        .badge { font-size: .75rem; }
        .search-bar-wrapper { position:sticky; top:0; z-index:1030; background:#fff; padding-top:5px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-leaf"></i> Leaf</a>
        <div class="navbar-nav">
            <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Trang chủ</a>
            <a class="nav-link active" href="search.php"><i class="fas fa-search"></i> Tìm kiếm</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a class="nav-link" href="profile.php"><i class="fas fa-user"></i> <?php echo $_SESSION['username']; ?></a>
                <a class="nav-link" href="logout.php">Đăng xuất</a>
            <?php else: ?>
                <a class="nav-link" href="login.php">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container">
    <div class="search-bar-wrapper">
        <form method="GET" class="d-flex mb-2">
            <input type="text" name="q" value="<?php echo htmlspecialchars($keyword); ?>" class="form-control me-2" placeholder="Tìm theo tên hoặc mô tả...">
            <?php foreach($types as $t) { echo '<input type="hidden" name="type[]" value="'.htmlspecialchars($t).'">'; } ?>
            <?php foreach($categories as $c) { echo '<input type="hidden" name="category[]" value="'.htmlspecialchars($c).'">'; } ?>
            <button class="btn btn-leaf"><i class="fas fa-search"></i></button>
            <button class="btn btn-outline-secondary ms-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#filters"><i class="fas fa-filter"></i></button>
        </form>
        <div class="small text-muted mb-2">Có <?php echo count($results); ?> kết quả.</div>
    </div>

    <div class="row">
        <?php if(count($results) == 0): ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4>Không tìm thấy kết quả</h4>
                <p class="text-muted">Thử từ khóa khác hoặc bỏ bớt bộ lọc.</p>
            </div>
        <?php endif; ?>
        <?php foreach($results as $post): ?>
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card h-100">
                    <?php if($post['image'] && file_exists('uploads/posts/' . $post['image'])): ?>
                        <img src="uploads/posts/<?php echo $post['image']; ?>" class="card-img-top post-image" alt="<?php echo htmlspecialchars($post['title']); ?>">
                    <?php else: ?>
                        <div class="card-img-top post-image bg-light d-flex align-items-center justify-content-center">
                            <span class="text-muted small">Chưa có ảnh</span>
                        </div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title mb-1">
                            <a href="view_post.php?id=<?php echo $post['id']; ?>" class="text-decoration-none text-dark"><?php echo htmlspecialchars($post['title']); ?></a>
                        </h6>
                        <div class="mb-1">
                            <span class="badge bg-success"><?php echo $post['type']; ?></span>
                            <span class="badge bg-info"><?php echo $post['category']; ?></span>
                        </div>
                        <p class="text-muted flex-grow-1 mb-2" style="font-size:.85rem;"><?php echo htmlspecialchars(substr($post['description'],0,90)); ?>...</p>
                        <div class="fw-bold <?php echo ($post['price']>0?'text-success':'text-primary'); ?> mb-2">
                            <?php echo $post['price']>0? number_format($post['price']).' VNĐ':'Miễn phí'; ?>
                        </div>
                        <small class="text-muted"><i class="fas fa-user"></i> <?php echo htmlspecialchars($post['full_name']); ?> • <i class="fas fa-clock"></i> <?php echo date('d/m', strtotime($post['created_at'])); ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Offcanvas Bộ lọc -->
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="filters">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Lọc</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <form method="GET" id="filterForm">
        <input type="hidden" name="q" value="<?php echo htmlspecialchars($keyword); ?>">
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Hình thức</strong>
                <button type="button" class="btn btn-sm btn-link" onclick="toggleGroup('typeGroup')"><i class="fas fa-chevron-down"></i></button>
            </div>
            <div id="typeGroup" class="row g-2">
                <?php foreach($allTypes as $t): ?>
                    <div class="col-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="type[]" value="<?php echo $t; ?>" id="type_<?php echo $t; ?>" <?php echo in_array($t,$types)?'checked':''; ?>>
                            <label class="form-check-label" for="type_<?php echo $t; ?>"><?php echo $t; ?></label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <hr>
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Loại sản phẩm</strong>
                <button type="button" class="btn btn-sm btn-link" onclick="toggleGroup('catGroup')"><i class="fas fa-chevron-down"></i></button>
            </div>
            <div id="catGroup" class="row g-2">
                <?php foreach($allCategories as $c): ?>
                    <div class="col-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="category[]" value="<?php echo $c; ?>" id="cat_<?php echo $c; ?>" <?php echo in_array($c,$categories)?'checked':''; ?>>
                            <label class="form-check-label" for="cat_<?php echo $c; ?>"><?php echo $c; ?></label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-outline-danger" onclick="clearFilters()">Xóa tất cả</button>
            <div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Đóng</button>
                <button type="submit" class="btn btn-leaf">Xong</button>
            </div>
        </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function clearFilters(){
    document.querySelectorAll('#filters input[type=checkbox]').forEach(cb=>cb.checked=false);
    document.getElementById('filterForm').submit();
}
function toggleGroup(id){
    const el = document.getElementById(id);
    el.style.display = (el.style.display==='none') ? 'flex' : 'none';
}
</script>
</body>
</html>