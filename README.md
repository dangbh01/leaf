# 🌿 Leaf - Hệ thống trao đổi đồ dùng học tập

Leaf là một web app PHP cho phép sinh viên trao đổi, mua bán hoặc tặng đồ dùng học tập. 

## 📋 Tính năng chính

### Người dùng
- 👥 Đăng ký/đăng nhập tài khoản
- 📝 Đăng bài với ảnh và thông tin chi tiết (tên sản phẩm, mô tả, danh mục, loại: trao đổi/bán/tặng, giá)
- 📸 Upload ảnh sản phẩm
- 🔍 Xem danh sách bài đăng đã được duyệt
- � Đặt hàng/đăng ký quan tâm sản phẩm
- 👤 Quản lý hồ sơ cá nhân (họ tên, email, điện thoại, Facebook, giáo viên chủ nhiệm, lớp)
- 📋 Quản lý bài đăng của bản thân
- � Xem đơn hàng đã đặt
- 📤 Chia sẻ bài đăng
- 📖 Xem hướng dẫn sử dụng

### Admin
- 👑 Quản trị hệ thống
- ✅ Duyệt/từ chối bài đăng (post_status: pending/approved/rejected)
- 👥 Quản lý người dùng (xem danh sách, xóa user)
- 📊 Xem thống kê bài đăng và người dùng
- 📋 Xem danh sách đơn hàng

## 🛠️ Stack công nghệ

- **Backend:** PHP 8.1
- **Database:** MySQL/MariaDB với PDO
- **Frontend:** Bootstrap 5 + Font Awesome icons
- **Web Server:** Apache (mod_rewrite enabled)
- **Containerization:** Docker + Docker Compose
- **Deployment:** Railway.app (với MySQL managed service)

## 🚀 Khởi động môi trường phát triển

### Option 1: Docker (Khuyến nghị)

```bash
# Clone repo
git clone https://github.com/dangbh01/leaf.git
cd leaf

# Copy file môi trường mẫu
cp .env.example .env
# Chỉnh sửa .env theo cấu hình của bạn

# Khởi động với Docker Compose
docker compose up -d

# Tạo admin user
docker compose exec web php seed_admin.php
```

Truy cập: http://localhost:8000

### Option 2: PHP Built-in Server

#### 1. Cài đặt yêu cầu
- PHP 8.1+
- MySQL/MariaDB
- Git

#### 2. Clone & Cấu hình
```bash
# Clone repo
git clone https://github.com/dangbh01/leaf.git
cd leaf

# Copy file môi trường mẫu (hoặc dùng config/database.php trực tiếp)
cp .env.example .env
# Chỉnh sửa .env với thông tin database của bạn
```

#### 3. Tạo database & import schema
```bash
# Tạo database
mysql -u root -p -e "CREATE DATABASE traodododung_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import schema
mysql -u root -p traodododung_db < schema.sql
```

#### 4. Tạo thư mục uploads
```bash
mkdir -p uploads/posts
chmod -R 755 uploads
```

#### 5. Chạy development server
```bash
# Sử dụng PHP built-in server
php -S localhost:8000
```

#### 6. Tạo admin user
Chỉnh sửa file `.env` với thông tin admin:
```bash
ADMIN_USER=admin
ADMIN_PASS=YourStrongPassword123!
ADMIN_EMAIL=admin@example.com
ADMIN_FULL_NAME=Administrator
ADMIN_PHONE=0123456789
```

Chạy script tạo admin:
```bash
php seed_admin.php
# Ghi nhớ username/password được in ra
```

Truy cập http://localhost:8000 và đăng nhập với tài khoản admin vừa tạo.

## 🚀 Triển khai lên Railway.app

### Hướng dẫn nhanh

Xem file [`QUICK_DEPLOY.md`](./QUICK_DEPLOY.md) để deploy trong 10 phút.

### Hướng dẫn chi tiết

Xem file [`RAILWAY_DEPLOYMENT.md`](./RAILWAY_DEPLOYMENT.md) để có hướng dẫn từng bước chi tiết.

### Tóm tắt bước quan trọng

1. **Push code lên GitHub**
2. **Tạo project trên Railway.app** (kết nối với GitHub repo)
3. **Thêm MySQL database** (Railway tự động cấu hình)
4. **Cấu hình biến môi trường:**
   - `MYSQL_URL` (Railway tự động tạo, hoặc dùng các biến riêng lẻ)
   - `ADMIN_USER`, `ADMIN_PASS`, `ADMIN_EMAIL`, `ADMIN_FULL_NAME`, `ADMIN_PHONE`
5. **Generate domain** và truy cập
6. **Login với admin account**

**Lưu ý:** 
- Railway hỗ trợ cả `MYSQL_URL` (format: `mysql://user:pass@host:port/dbname`) và các biến riêng lẻ (`DB_HOST`, `DB_PORT`, `DB_USER`, `DB_PASS`, `DB_NAME`)
- File `docker-entrypoint.sh` tự động import schema và tạo admin user khi deploy lần đầu
- Persistent storage cho `/var/www/html/uploads` được tự động mount bởi Railway

## 📝 Phát triển

### Cấu trúc thư mục

```
leaf-02/
├── admin/                    # Trang quản trị
│   ├── auth.php             # Xác thực admin
│   ├── dashboard.php        # Dashboard admin
│   ├── manage_posts.php     # Quản lý bài đăng
│   └── manage_users.php     # Quản lý người dùng
├── config/                   # Cấu hình
│   └── database.php         # Kết nối database (hỗ trợ MYSQL_URL và biến riêng)
├── uploads/                  # Upload files
│   └── posts/               # Ảnh bài đăng
├── create_post.php          # Tạo bài đăng mới
├── docker-compose.yml       # Docker Compose config
├── docker-entrypoint.sh     # Docker entrypoint script (auto setup DB)
├── Dockerfile               # Docker image definition
├── guide.php                # Trang hướng dẫn
├── index.php                # Trang chủ (danh sách bài đăng approved)
├── login.php                # Đăng nhập
├── logout.php               # Đăng xuất
├── my_orders.php            # Đơn hàng của tôi
├── my_posts.php             # Bài đăng của tôi
├── order.php                # Đặt hàng/quan tâm sản phẩm
├── profile.php              # Quản lý hồ sơ cá nhân
├── register.php             # Đăng ký tài khoản
├── schema.sql               # Database schema
├── seed_admin.php           # Script tạo admin user
├── share.php                # Chia sẻ bài đăng
├── view_orders.php          # Xem đơn hàng (cho người đăng bài)
├── view_post.php            # Xem chi tiết bài đăng
├── .env.example             # Mẫu file environment variables
├── QUICK_DEPLOY.md          # Hướng dẫn deploy nhanh
├── RAILWAY_DEPLOYMENT.md    # Hướng dẫn deploy chi tiết
└── README.md                # File này
```

### Database schema

Xem `schema.sql` để biết cấu trúc database đầy đủ. Các bảng chính:

#### `users` table
- `id`: Primary key
- `username`: Tên đăng nhập (unique)
- `password`: Mật khẩu đã hash
- `email`: Email
- `full_name`: Họ tên đầy đủ
- `phone`: Số điện thoại
- `facebook_link`: Link Facebook
- `homeroom_teacher`: Giáo viên chủ nhiệm
- `class`: Lớp
- `role`: Vai trò (`user` hoặc `admin`)
- `created_at`: Thời gian tạo

#### `posts` table
- `id`: Primary key
- `user_id`: ID người đăng (foreign key → users)
- `title`: Tên sản phẩm
- `description`: Mô tả
- `image`: Đường dẫn ảnh
- `category`: Danh mục (Sách, Dụng cụ học tập, Đồng phục, Thiết bị điện tử, Khác)
- `type`: Loại (Trao đổi, Bán, Tặng)
- `price`: Giá (nếu là bán)
- `contact_email`: Email liên hệ
- `status`: Trạng thái sản phẩm (Còn hàng, Hết hàng, Đã giao dịch)
- `post_status`: Trạng thái duyệt (`pending`, `approved`, `rejected`)
- `created_at`: Thời gian đăng

#### `orders` table
- `id`: Primary key
- `post_id`: ID bài đăng (foreign key → posts)
- `user_id`: ID người đặt hàng (foreign key → users)
- `status`: Trạng thái đơn (`pending`, `confirmed`, `cancelled`)
- `created_at`: Thời gian đặt

### Environment Variables

File `config/database.php` hỗ trợ 2 cách cấu hình:

**Option 1: MYSQL_URL (Railway format)**
```bash
MYSQL_URL=mysql://user:password@host:port/database
```

**Option 2: Biến riêng lẻ**
```bash
DB_HOST=localhost
DB_PORT=3306
DB_NAME=traodododung_db
DB_USER=root
DB_PASS=your_password
```

**Admin account (cho seed_admin.php)**
```bash
ADMIN_USER=admin
ADMIN_PASS=your_strong_password
ADMIN_EMAIL=admin@example.com
ADMIN_FULL_NAME=Administrator
ADMIN_PHONE=0123456789
```

## 🔒 Security

- Passwords được hash với `password_hash()` và verify với `password_verify()`
- SQL injection prevention với PDO prepared statements
- Upload validation cho images (kiểm tra extension và di chuyển vào thư mục uploads/posts)
- Role-based access control (kiểm tra `role` trong session)
- Admin routes được bảo vệ bởi `admin/auth.php`
- Session-based authentication

## 🐛 Known Issues & TODOs

- [ ] Thêm CSRF protection cho forms
- [ ] Escape output để prevent XSS
- [ ] Rate limiting cho API endpoints  
- [ ] Enhanced upload validation (kích thước tối đa, mime type checking)
- [ ] Image resizing/optimization để tiết kiệm storage
- [ ] Email notifications cho user khi bài đăng được duyệt
- [ ] Pagination cho danh sách bài đăng
- [ ] Search và filter functionality
- [ ] User blocking/reporting system

## 📜 License

[MIT License](LICENSE)

## 🤝 Contributing

1. Fork repo
2. Tạo branch (`git checkout -b feature/something`)
3. Commit changes (`git commit -am 'Add something'`)
4. Push branch (`git push origin feature/something`)
5. Tạo Pull Request

## 📞 Support

Nếu gặp vấn đề khi triển khai hoặc sử dụng:
- Tạo issue trên GitHub
- Kiểm tra logs trong Railway dashboard (Deploy Logs và App Logs)
- Xem lại hướng dẫn trong `QUICK_DEPLOY.md` và `RAILWAY_DEPLOYMENT.md`