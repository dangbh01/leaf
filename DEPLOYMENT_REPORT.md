# 📋 BÁO CÁO QUÁN TRỌNG: CHUYỂN ĐỔI PROJECT TỪ XAMPP SANG RAILWAY.APP

**Ngày tạo:** November 11, 2025  
**Project:** Leaf - Hệ thống trao đổi đồ dùng học tập  
**Trạng thái:** ✅ Ready for Production  
**Platform Deploy:** Railway.app (https://railway.app)

---

## 📌 TÓM TẮT CHUYỂN ĐỔI

Dự án Leaf ban đầu là một **ứng dụng PHP chạy local trên XAMPP** nhưng hiện nay đã được **cấu hình hoàn toàn để deploy lên Railway.app** - một nền tảng cloud miễn phí hỗ trợ Docker.

**Thay đổi chính:**
- ✅ Containerization với Docker
- ✅ Hỗ trợ biến môi trường (Environment Variables)
- ✅ Tự động setup database schema
- ✅ Tự động tạo admin user khi deploy
- ✅ Hỗ trợ Railway MYSQL_URL format
- ✅ Apache rewrite enabled
- ✅ Database UTF-8 Unicode support

---

## 🔄 CÁC FILE VÀ THAY ĐỔI CHÍNH

### 1️⃣ **Dockerfile** - Container Image
**Vị trí:** `/Dockerfile`

**Mục đích:** Định nghĩa cách build ứng dụng PHP

**Nội dung chính:**
```dockerfile
FROM php:8.1-apache
# Cài đặt:
# - pdo, pdo_mysql: Kết nối database
# - zip, unzip: Hỗ trợ file compression
# - mysql-client: Công cụ MySQL CLI
# - Apache rewrite: URL rewriting (SEO friendly)
```

**Chuyên biệt Railway:**
- Base image `php:8.1-apache` hỗ trợ port động
- Cấp quyền `www-data` cho thư mục uploads
- Copy `docker-entrypoint.sh` làm script khởi động

---

### 2️⃣ **docker-entrypoint.sh** - Script Khởi Động ⭐ QUAN TRỌNG
**Vị trí:** `/docker-entrypoint.sh`

**Mục đích:** Tự động cấu hình khi container khởi động

**Chức năng tự động:**

#### a) Cấu hình Apache PORT (Railway-specific)
```bash
# Railway cấp quyền chỉ qua biến $PORT
# Script tự động cập nhật Apache ports.conf
if [ -n "$PORT" ]; then
    sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
fi
```

#### b) Parse MYSQL_URL (Railway format)
**Railway trả về:** `mysql://user:password@host:port/dbname`

Script phân tích URL và trích xuất:
- DB_HOST
- DB_PORT
- DB_USER
- DB_PASS
- DB_NAME

```bash
# Ví dụ:
# MYSQL_URL=mysql://root:pass123@mysql.railway.internal:3306/railway
# Kết quả: host=mysql.railway.internal, port=3306, user=root, ...
```

#### c) Tạo thư mục uploads
```bash
mkdir -p /var/www/html/uploads/posts
chown -R www-data:www-data /var/www/html/uploads
chmod -R 755 /var/www/html/uploads
```

#### d) Tự động Import Schema
Sử dụng PHP thay vì `mysql` command (vì Railway không cấp quyền shell trực tiếp):

```php
// Kiểm tra nếu database trống
$tables = $pdo->query('SHOW TABLES')->fetchAll();
if (empty($tables)) {
    // Import schema.sql
    $schema = file_get_contents('/var/www/html/schema.sql');
    $pdo->exec($schema);
}
```

#### e) Tự động Tạo Admin User
Gọi `seed_admin.php` nếu admin chưa tồn tại

**Biến môi trường cần thiết:**
- `ADMIN_USER` - Username admin
- `ADMIN_PASS` - Password admin (hash bằng password_hash)
- `ADMIN_EMAIL` - Email admin
- `ADMIN_FULL_NAME` - Tên đầy đủ
- `ADMIN_PHONE` - Số điện thoại

---

### 3️⃣ **config/database.php** - Kết Nối Database ⭐ QUAN TRỌNG
**Vị trí:** `/config/database.php`

**Thay đổi chính:** Hỗ trợ 2 cách kết nối

#### Cách 1: MYSQL_URL (Railway khuyên dùng)
```php
$mysql_url = getenv('MYSQL_URL');
if ($mysql_url) {
    // Parse: mysql://user:pass@host:port/dbname
    $url_parts = parse_url($mysql_url);
    $host = $url_parts['host'];
    $port = $url_parts['port'];
    $dbname = ltrim($url_parts['path'], '/');
    $username = $url_parts['user'];
    $password = $url_parts['pass'];
}
```

#### Cách 2: Biến riêng lẻ (Fallback)
```php
else {
    $host = getenv('DB_HOST') ?: 'localhost';
    $port = getenv('DB_PORT') ?: '3306';
    $dbname = getenv('DB_NAME') ?: 'traodododung_db';
    $username = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASS') ?: '';
}
```

**Kết quả:** Cùng PDO connection với charset utf8mb4
```php
$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
$pdo = new PDO($dsn, $username, $password);
```

---

### 4️⃣ **seed_admin.php** - Tạo Admin User
**Vị trị:** `/seed_admin.php`

**Mục đích:** Script để tạo admin user từ biến môi trường

**Quá trình:**
1. Lấy thông tin admin từ ENV vars
2. Kiểm tra nếu admin đã tồn tại
3. Hash password bằng `password_hash()`
4. INSERT vào database

**Gọi từ:** `docker-entrypoint.sh`

---

### 5️⃣ **schema.sql** - Cấu trúc Database
**Vị trí:** `/schema.sql`

**Bảng chính:**

#### users
```sql
- id (INT PRIMARY KEY)
- username (VARCHAR UNIQUE)
- password (VARCHAR - hash)
- email, full_name, phone
- facebook_link, homeroom_teacher, class
- role (enum: 'user', 'admin') DEFAULT 'user'
- created_at (TIMESTAMP)
```

#### posts
```sql
- id (INT PRIMARY KEY)
- user_id (FK → users)
- title, description, image
- category, type (exchange/buy/give)
- price (DECIMAL)
- post_status (pending/approved/rejected)
- created_at (TIMESTAMP)
```

#### orders
```sql
- id (INT PRIMARY KEY)
- post_id (FK → posts)
- user_id (FK → users)
- message, status
- created_at (TIMESTAMP)
```

**Charset:** utf8mb4 (hỗ trợ emoji, ký tự đặc biệt)

---

### 6️⃣ **docker-compose.yml** - Local Development
**Vị trí:** `/docker-compose.yml`

**Mục đích:** Cho phép dev chạy project local với Docker

**Services:**
- **web:** PHP 8.1 + Apache (port 8080)
- **db:** MySQL 8.0 (port 3306)

**Volumes:**
- `.:/var/www/html` - Code binding
- `mysql_data:/var/lib/mysql` - Database persistence

**Sử dụng local:**
```bash
docker compose up -d
docker compose exec web php seed_admin.php
```

---

### 7️⃣ **RAILWAY_DEPLOYMENT.md** - Hướng Dẫn Chi Tiết
**Vị trí:** `/RAILWAY_DEPLOYMENT.md`

**Nội dung:**
- 📋 Yêu cầu tiên quyết (GitHub account)
- 🚀 Bước 1: Chuẩn bị code
- 🚀 Bước 2: Tạo project Railway
- 🚀 Bước 3: Thêm MySQL database
- 🚀 Bước 4: Cấu hình biến môi trường
- 🚀 Bước 5: Kiểm tra deploy
- 🚀 Bước 6: Login & test

---

### 8️⃣ **QUICK_DEPLOY.md** - Deploy Nhanh (10 phút)
**Vị trí:** `/QUICK_DEPLOY.md`

**Tóm gọn quá trình deploy:**
1. Push code lên GitHub
2. Tạo project Railway
3. Thêm MySQL
4. Set env vars
5. Generate domain
6. Login

---

### 9️⃣ **README.md** - Hướng Dẫn Chung
**Vị trí:** `/README.md`

**Nội dung:**
- Tính năng ứng dụng
- Stack công nghệ
- Hướng dẫn local dev (Docker + PHP built-in)
- Hướng dẫn deploy Railway

---

## 🔧 BIẾN MÔI TRƯỜNG (Environment Variables)

### Railway Platform
Khi deploy lên Railway, cần set các biến này:

#### Tự động từ Railway (MySQL service)
```
MYSQL_URL = ${{MySQL.MYSQL_URL}}  # Railway tự động cấp
```

**Hoặc** các biến riêng:
```
DB_HOST = ${{MySQL.MYSQL_HOST}}
DB_PORT = ${{MySQL.MYSQL_PORT}}
DB_NAME = ${{MySQL.MYSQL_DATABASE}}
DB_USER = ${{MySQL.MYSQL_USER}}
DB_PASS = ${{MySQL.MYSQL_PASSWORD}}
```

#### Bắt buộc set thủ công
```
ADMIN_USER = admin           # Username admin
ADMIN_PASS = YourPassword123 # Password (mạnh!)
ADMIN_EMAIL = admin@example.com
ADMIN_FULL_NAME = Administrator
ADMIN_PHONE = 0987654321
PORT = 8080  # (Railway tự động cấp)
```

### Local Development
**File: `.env.example`**
```
DB_HOST = localhost
DB_PORT = 3306
DB_NAME = traodododung_db
DB_USER = leaf_user
DB_PASS = leaf_password
ADMIN_USER = admin
ADMIN_PASS = admin123
ADMIN_EMAIL = admin@example.com
ADMIN_FULL_NAME = Administrator
ADMIN_PHONE = 0987654321
```
---

## ✅ CHECKLIST DEPLOYMENT

Khi deploy lên Railway, đảm bảo:

- [ ] Code đã push lên GitHub
- [ ] Repo có tất cả file cần thiết:
  - [ ] Dockerfile
  - [ ] docker-entrypoint.sh (executable)
  - [ ] schema.sql
  - [ ] seed_admin.php
  - [ ] config/database.php
  - [ ] Tất cả PHP files

- [ ] Railway setup:
  - [ ] GitHub account đã kết nối
  - [ ] New project từ GitHub repo
  - [ ] MySQL database được thêm vào
  - [ ] Biến môi trường được set:
    - [ ] ADMIN_USER
    - [ ] ADMIN_PASS (mạnh!)
    - [ ] ADMIN_EMAIL
    - [ ] ADMIN_FULL_NAME
    - [ ] ADMIN_PHONE
    - [ ] MYSQL_URL hoặc các biến DB riêng

- [ ] Deploy verification:
  - [ ] Build thành công (xem Deploy Logs)
  - [ ] "✅ Schema imported successfully" trong logs
  - [ ] "Creating admin user..." trong logs
  - [ ] Domain HTTPS được tạo
  - [ ] Có thể login bằng admin credentials

---

## 🚀 QUICK START COMMANDS

### Local Development with Docker
```bash
# Khởi động
docker compose up -d

# Tạo admin
docker compose exec web php seed_admin.php

# Dừng
docker compose down
```

## 🔐 SECURITY NOTES

### Hiện tại được hỗ trợ
- ✅ Password hashing: `password_hash()` + `PASSWORD_DEFAULT`
- ✅ SQL injection prevention: Prepared statements (PDO)
- ✅ Image upload validation: Extension & location check
- ✅ Session-based auth: Role checking (admin vs user)

### TODO (Chưa implement)
- ❌ Output escaping (htmlspecialchars)
- ❌ CSRF protection (tokens)
- ❌ Rate limiting
- ❌ HTTPS enforcement (Railway tự động cấp)
- ❌ Input validation & sanitization

---

## 📝 ARCHITECTURE OVERVIEW

```
leaf-02/
├── Dockerfile                  ← Build container
├── docker-compose.yml          ← Local dev (Docker)
├── docker-entrypoint.sh        ← Auto setup script ⭐
├── config/
│   └── database.php            ← DB connection (MYSQL_URL support) ⭐
├── seed_admin.php              ← Admin user creation ⭐
├── schema.sql                  ← Database structure
│
├── index.php                   ← Homepage (approved posts)
├── login.php                   ← User login
├── register.php                ← User registration
├── create_post.php             ← Create post with image
├── my_posts.php                ← User's posts
├── my_orders.php               ← User's orders
├── order.php                   ← Place order/interest
├── view_post.php               ← View post detail
├── view_orders.php             ← View order detail
├── profile.php                 ← User profile
├── search.php                  ← Search posts
├── share.php                   ← Share post
├── guide.php                   ← Help/guide
├── logout.php                  ← Logout
│
├── admin/
│   ├── auth.php                ← Admin protection (session check)
│   ├── dashboard.php           ← Admin stats
│   ├── manage_posts.php        ← Approve/reject posts
│   ├── manage_users.php        ← User management
│
├── uploads/posts/              ← Image uploads (must be writable)
│
├── README.md                   ← General guide
├── QUICK_DEPLOY.md             ← 10-minute deploy
└── RAILWAY_DEPLOYMENT.md       ← Detailed deploy guide
```

---

## 📚 FLOW: TỪ LOCAL XAMPP SANG RAILWAY

### Trước đây (Local XAMPP)
```
1. Start XAMPP (Apache + MySQL)
2. Copy files vào htdocs
3. Create database manually
4. Import schema manually
5. Create admin manually (phys. trong DB)
6. Truy cập http://localhost
```

### Hiện nay (Railway Deployment)
```
1. Push code lên GitHub
2. Railway tự động:
   a) Phát hiện Dockerfile
   b) Build Docker image
   c) Run container
   d) docker-entrypoint.sh chạy:
      - Parse MYSQL_URL
      - Import schema (nếu DB trống)
      - Tạo admin user (từ ENV vars)
   e) Apache lắng nghe trên PORT (Railway cấp)
   f) Application ready!
3. Truy cập https://leaf-production-xxx.up.railway.app
```

### Key Improvement
- ✅ Tự động setup (không manual steps)
- ✅ Reproducible (tất cả config trong code)
- ✅ Scalable (Docker container)
- ✅ Portable (chạy khác nơi mà không đổi code)
- ✅ Free tier (Railway 500 hrs/month)

---

## 🎯 DEPLOYMENT STATUS

| Component | Status | Notes |
|-----------|--------|-------|
| Docker Container | ✅ Ready | PHP 8.1 + Apache |
| Database Connection | ✅ Ready | MYSQL_URL support |
| Schema Auto-import | ✅ Ready | Via PHP in entrypoint |
| Admin Auto-creation | ✅ Ready | Via seed_admin.php |
| File Upload Handling | ✅ Ready | uploads/posts writable |
| Environment Config | ✅ Ready | .env support |
| Railway Integration | ✅ Ready | PORT, MYSQL_URL parsing |
| Local Dev | ✅ Ready | docker-compose.yml |
| Documentation | ✅ Ready | README, QUICK_DEPLOY, RAILWAY_DEPLOYMENT |
