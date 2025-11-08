# 🌿 Leaf - Hệ thống trao đổi đồ dùng học tập

Leaf là một web app PHP cho phép sinh viên trao đổi, mua bán hoặc tặng đồ dùng học tập. 

## 📋 Tính năng chính

- 👥 Đăng ký/đăng nhập tài khoản
- 📝 Đăng bài với ảnh và thông tin chi tiết
- 🔄 Trao đổi/bán/tặng đồ dùng học tập
- 👑 Trang quản trị cho admin
- 📱 Responsive design (Bootstrap 5)

## 🛠️ Stack công nghệ

- PHP 8.1
- MySQL/MariaDB
- PDO cho database
- Bootstrap 5 + Font Awesome
- Apache/Nginx

## 🚀 Khởi động môi trường phát triển

### 1. Cài đặt yêu cầu
- PHP 8.1+
- MySQL/MariaDB
- Apache/Nginx hoặc PHP built-in server
- Git (để clone repo)

### 2. Clone & Cấu hình
```bash
# Clone repo
git clone https://github.com/nhtuanh20708-coder/Leaf.git
cd Leaf

# Copy file môi trường mẫu
cp .env.example .env
# Chỉnh sửa .env theo cấu hình của bạn
```

### 3. Tạo database & tables
```bash
# Tạo database
mysql -u root -p -e "CREATE DATABASE traodododung_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import schema
mysql -u root -p traodododung_db < schema.sql
```

### 4. Tạo thư mục uploads
```bash
mkdir -p uploads/posts
chmod -R 755 uploads
```

### 5. Chạy development server
```bash
# Sử dụng PHP built-in server
php -S localhost:8000

# HOẶC với Docker
docker compose up -d
```

### 6. Tạo admin user
```bash
# Chạy script tạo admin
php seed_admin.php
# Ghi nhớ username/password được in ra
```

Truy cập http://localhost:8000 và đăng nhập với tài khoản admin vừa tạo.

## 🚀 Triển khai lên Render.com

### 1. Chuẩn bị Database

1. Chuẩn bị MySQL Database (Render chỉ hỗ trợ PostgreSQL managed):
   - DigitalOcean Managed MySQL
   - Amazon RDS
   - PlanetScale
   - Hoặc MySQL trên VPS riêng

2. Ghi nhớ thông tin kết nối:
   - Host
   - Port (thường là 3306)
   - Username có quyền tạo database
   - Password

### 2. Tạo Web Service

1. Đăng nhập Render Dashboard
2. Click "New" → "Web Service"
3. Kết nối với GitHub repo
4. Basic Configuration:
   - Environment: "Docker"
   - Branch: main
   - Instance Type: Starter (hoặc cao hơn)

5. Click "Create Web Service" để tạo service

### 4. Cấu hình Environment Variables

Trong service settings → Environment:

Database Variables:
- `DB_HOST`: Host của MySQL server
- `DB_PORT`: Port của MySQL (default 3306)
- `DB_NAME`: traodododung_db (hoặc tên khác)
- `DB_USER`: Username MySQL
- `DB_PASS`: Password MySQL

Admin Account Variables (bắt buộc để tạo admin):
- `ADMIN_USER`: Username cho admin (e.g., admin)
- `ADMIN_PASS`: Strong password cho admin
- `ADMIN_EMAIL`: Email cho admin account
- `ADMIN_FULL_NAME`: Tên đầy đủ của admin
- `ADMIN_PHONE`: Số điện thoại admin (optional)

### 5. Deploy và Setup Database

1. Click "Create Web Service"
2. Đợi build & deploy hoàn tất

3. Import database schema:
   ```bash
   # Kết nối và import schema từ máy local của bạn
   mysql -h YOUR_DB_HOST -u YOUR_DB_USER -p < schema.sql
   ```
   File schema.sql sẽ:
   - Tạo database nếu chưa có
   - Tạo các tables cần thiết
   - An toàn để chạy lại (dùng IF NOT EXISTS)

4. SSH vào container (trong Render dashboard):
   - Click "Shell"
   - Tạo admin account:
   ```bash
   php seed_admin.php
   ```

5. Xác nhận mọi thứ hoạt động:
   - Truy cập URL được cấp
   - Đăng nhập với thông tin admin
   - Thử upload ảnh (kiểm tra disk mount)

### 7. Kiểm tra Security

1. Xác nhận tất cả env vars đã được set
2. Test upload và xem ảnh
3. Kiểm tra admin login
4. Đổi admin password sau lần đăng nhập đầu tiên

## 📝 Phát triển

### Cấu trúc thư mục

```
├── admin/                 # Trang quản trị
├── config/               # Cấu hình (database)
├── uploads/              # Upload files
│   └── posts/           # Ảnh bài đăng
├── index.php            # Trang chủ
├── login.php            # Đăng nhập
├── register.php         # Đăng ký
└── ...
```

### Database schema

Xem `schema.sql` để biết cấu trúc database đầy đủ. Các bảng chính:

- `users`: Người dùng & admin
- `posts`: Bài đăng
- `orders`: Đơn đặt hàng

## 🔒 Security

- Passwords được hash với `password_hash()`
- SQL injection prevention với PDO prepared statements
- Upload validation cho images
- Role-based access control

## 📜 License

[MIT License](LICENSE)

## 🤝 Contributing

1. Fork repo
2. Tạo branch (`git checkout -b feature/something`)
3. Commit changes (`git commit -am 'Add something'`)
4. Push branch (`git push origin feature/something`)
5. Tạo Pull Request

## 🐛 Known Issues & TODOs

- [ ] Thêm CSRF protection cho forms
- [ ] Escape output để prevent XSS
- [ ] Rate limiting cho API endpoints
- [ ] Validation uploads (kích thước, mime type)