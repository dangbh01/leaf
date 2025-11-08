# 🚂 Hướng dẫn Deploy Leaf lên Railway.app

## 📋 Tổng quan
Railway.app là platform miễn phí hỗ trợ deploy ứng dụng Docker với MySQL built-in. Miễn phí **500 giờ/tháng** và domain HTTPS tự động.

**⏱️ Thời gian:** 10-15 phút  
**💰 Chi phí:** MIỄN PHÍ  
**🌐 Domain:** `https://leaf-production-xxxx.up.railway.app`

---

## ✅ Yêu cầu trước khi bắt đầu

1. ✅ **Tài khoản GitHub** (để đăng nhập Railway)
2. ✅ **Code đã push lên GitHub repository**
3. ✅ **Tài khoản Railway** (đăng ký miễn phí bằng GitHub)

---

## 🚀 BƯỚC 1: Chuẩn bị Code

### 1.1. Kiểm tra file cần thiết

Đảm bảo repo của bạn có các file sau:
```
✅ Dockerfile
✅ docker-compose.yml (không bắt buộc, Railway chỉ dùng Dockerfile)
✅ docker-entrypoint.sh
✅ schema.sql
✅ config/database.php
✅ seed_admin.php
```

### 1.2. Push code lên GitHub (nếu chưa có)

```bash
# Kiểm tra git status
git status

# Add tất cả file
git add .

# Commit
git commit -m "Prepare for Railway deployment"

# Push lên GitHub
git push origin main
```

**✅ Checkpoint:** Code đã có trên GitHub repo `dangbh01/leaf`

---

## 🚀 BƯỚC 2: Tạo Project trên Railway

### 2.1. Đăng ký/Đăng nhập Railway

1. Truy cập: **https://railway.app**
2. Click **"Login"** hoặc **"Start a New Project"**
3. Chọn **"Login with GitHub"**
4. Authorize Railway truy cập GitHub của bạn

### 2.2. Tạo Project mới

1. Sau khi đăng nhập, click **"New Project"**
2. Chọn **"Deploy from GitHub repo"**
3. Nếu lần đầu, click **"Configure GitHub App"** để Railway có quyền truy cập repo
4. Chọn repository: **`dangbh01/leaf`**
5. Click **"Deploy Now"**

**🎉 Railway sẽ tự động:**
- Phát hiện `Dockerfile`
- Build Docker image
- Deploy lên server

**⏳ Đợi 2-3 phút** để Railway build lần đầu.

**✅ Checkpoint:** Bạn thấy project "leaf" trong Railway dashboard

---

## 🚀 BƯỚC 3: Thêm MySQL Database

### 3.1. Tạo MySQL Database

1. Trong Railway project, click **"New"** (góc phải)
2. Chọn **"Database"**
3. Chọn **"Add MySQL"**

**🎉 Railway sẽ tự động:**
- Tạo MySQL container
- Tạo database với thông tin kết nối
- Tạo sẵn các biến môi trường

### 3.2. Lấy thông tin Database

1. Click vào **MySQL** service trong dashboard
2. Chuyển sang tab **"Variables"**
3. Bạn sẽ thấy các biến:
   - `MYSQL_HOST`
   - `MYSQL_PORT` (3306)
   - `MYSQL_DATABASE`
   - `MYSQL_USER`
   - `MYSQL_PASSWORD`
   - `MYSQL_ROOT_PASSWORD`

**📝 Ghi chú:** Bạn không cần copy, Railway tự động share biến này giữa các service.

**✅ Checkpoint:** MySQL service đã chạy (màu xanh)

---

## 🚀 BƯỚC 4: Cấu hình Environment Variables cho Web App

### 4.1. Mở settings của Web service

1. Click vào **service "leaf"** (không phải MySQL)
2. Chuyển sang tab **"Variables"**

### 4.2. Thêm biến môi trường

Click **"New Variable"** và thêm **CHÍNH XÁC** như sau:

#### 🔗 Database Connection (dùng reference từ MySQL)

```
DB_HOST = ${{MySQL.MYSQL_HOST}}
DB_PORT = ${{MySQL.MYSQL_PORT}}
DB_NAME = ${{MySQL.MYSQL_DATABASE}}
DB_USER = ${{MySQL.MYSQL_USER}}
DB_PASS = ${{MySQL.MYSQL_PASSWORD}}
```

**⚠️ LƯU Ý:** 
- Gõ **CHÍNH XÁC** `${{MySQL.MYSQL_HOST}}` (không phải value thật)
- Railway sẽ tự động thay thế bằng giá trị thật
- Nếu MySQL service tên khác, thay `MySQL` bằng tên đó

#### 👑 Admin Account

```
ADMIN_USER = admin
ADMIN_PASS = YourSecurePassword123!
ADMIN_EMAIL = admin@youremail.com
ADMIN_FULL_NAME = Administrator
ADMIN_PHONE = 0987654321
```

**⚠️ QUAN TRỌNG:** Thay `YourSecurePassword123!` bằng mật khẩu mạnh của bạn!

### 4.3. Kiểm tra lại

Đảm bảo bạn có **ít nhất 9 biến**:
- ✅ DB_HOST
- ✅ DB_PORT
- ✅ DB_NAME
- ✅ DB_USER
- ✅ DB_PASS
- ✅ ADMIN_USER
- ✅ ADMIN_PASS
- ✅ ADMIN_EMAIL
- ✅ ADMIN_FULL_NAME (optional nhưng nên có)
- ✅ ADMIN_PHONE (optional)

**✅ Checkpoint:** Tất cả biến đã được thêm vào

---

## 🚀 BƯỚC 5: Redeploy & Kiểm tra Log

### 5.1. Trigger Redeploy

1. Vẫn ở tab **"Deployments"** của service leaf
2. Click vào deployment mới nhất
3. Click **"Redeploy"** (hoặc Railway sẽ tự động redeploy khi thêm biến)

### 5.2. Xem Log để kiểm tra

1. Click vào deployment đang chạy
2. Xem tab **"Deploy Logs"**

**Kiểm tra các log sau:**

```
✅ "Waiting for MySQL..." 
✅ "Checking database schema..."
✅ "Importing schema..." (lần đầu tiên)
✅ "AH00558: apache2: Could not reliably determine..." (OK, không sao)
```

**❌ Nếu thấy lỗi:**
- `Connection refused` → MySQL chưa sẵn sàng, đợi thêm
- `Access denied` → Sai DB_USER hoặc DB_PASS

**✅ Checkpoint:** Log cho thấy schema đã import thành công

---

## 🚀 BƯỚC 6: Import Database Schema & Tạo Admin

### 6.1. Option A: Tự động (đã cấu hình sẵn)

Script `docker-entrypoint.sh` sẽ **tự động import** `schema.sql` khi:
- Database trống (chưa có bảng)
- Service khởi động lần đầu

**Chỉ cần đợi deploy xong là OK!**

### 6.2. Option B: Thủ công (nếu cần)

**Nếu muốn import thủ công:**

1. Click vào **MySQL service**
2. Tab **"Data"** → click **"Query"**
3. Copy nội dung file `schema.sql` và paste vào
4. Click **"Run Query"**

### 6.3. Tạo Admin User

**Cách 1: Tự động khi deploy (KHUYẾN NGHỊ)**

Thêm vào cuối file `docker-entrypoint.sh`:

```bash
# Seed admin user if not exists
echo "Creating admin user..."
php /var/www/html/seed_admin.php || echo "Admin already exists or error occurred"
```

Sau đó commit & push:
```bash
git add docker-entrypoint.sh
git commit -m "Auto seed admin on deploy"
git push
```

Railway sẽ tự động redeploy.

**Cách 2: Thủ công qua Railway CLI**

```bash
# Install Railway CLI
npm i -g @railway/cli

# Login
railway login

# Link project
railway link

# Run seed
railway run php seed_admin.php
```

**Cách 3: Thủ công qua MySQL Query**

Vào MySQL Data tab, chạy:

```sql
INSERT INTO users (username, password, email, full_name, phone, role)
VALUES (
  'admin',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  -- password: password
  'admin@example.com',
  'Administrator',
  '0987654321',
  'admin'
);
```

**⚠️ Lưu ý:** Hash trên là password `password`, nhớ đổi sau khi login!

**✅ Checkpoint:** Admin user đã được tạo

---

## 🚀 BƯỚC 7: Thêm Domain & Truy cập Website

### 7.1. Generate Domain

1. Click vào **service "leaf"** (web service)
2. Tab **"Settings"**
3. Scroll xuống **"Networking"** → **"Public Networking"**
4. Click **"Generate Domain"**

**🎉 Railway sẽ tạo domain:**
```
https://leaf-production-xxxx.up.railway.app
```

### 7.2. Truy cập & Test

1. Copy domain vừa tạo
2. Mở trình duyệt
3. Truy cập domain

**✅ Bạn sẽ thấy:**
- Trang chủ Leaf với navbar xanh
- Menu: Đăng ký, Đăng nhập, Hướng dẫn, Chia sẻ

### 7.3. Login Admin

1. Click **"Đăng nhập"**
2. Nhập:
   - Username: `admin` (hoặc giá trị `ADMIN_USER`)
   - Password: (giá trị `ADMIN_PASS`)
3. Login thành công → thấy menu **"Quản trị"**

**✅ Checkpoint:** Website đã chạy và login admin thành công!

---

## 🎉 HOÀN TẤT!

### ✅ Checklist cuối cùng:

- ✅ Website accessible qua domain Railway
- ✅ Database MySQL đã có bảng users, posts, orders
- ✅ Login admin thành công
- ✅ Có thể tạo bài đăng, upload ảnh
- ✅ HTTPS tự động (Railway cung cấp SSL miễn phí)

---

## 🔧 Troubleshooting

### ❌ Lỗi "Connection refused"

**Nguyên nhân:** MySQL chưa sẵn sàng hoặc biến môi trường sai

**Giải pháp:**
1. Kiểm tra MySQL service đã chạy (màu xanh)
2. Kiểm tra biến `DB_HOST`, `DB_USER`, `DB_PASS` đúng format `${{MySQL.XXX}}`
3. Redeploy web service

### ❌ Lỗi "Access denied for user"

**Nguyên nhân:** Biến DB_USER hoặc DB_PASS sai

**Giải pháp:**
1. Vào MySQL service → tab Variables
2. Copy chính xác `MYSQL_USER` và `MYSQL_PASSWORD`
3. Paste vào DB_USER và DB_PASS của web service
4. Hoặc dùng reference: `${{MySQL.MYSQL_USER}}`

### ❌ Upload ảnh bị lỗi

**Nguyên nhân:** Thư mục uploads không có quyền write

**Giải pháp:**
Kiểm tra `docker-entrypoint.sh` có đoạn:
```bash
mkdir -p /var/www/html/uploads/posts
chown -R www-data:www-data /var/www/html/uploads
chmod -R 755 /var/www/html/uploads
```

### ❌ Database bị reset sau mỗi deploy

**Nguyên nhân:** Railway free plan không persist data (tùy thuộc vào cách cấu hình)

**Giải pháp:**
Railway MySQL service có **persistent volume** tự động. Chỉ khi bạn **xóa MySQL service** thì mất data.

### ⚠️ Website chạy nhưng không có dữ liệu

**Nguyên nhân:** Schema chưa được import

**Giải pháp:**
```bash
# Sử dụng Railway CLI
railway connect MySQL

# Sau đó import
mysql -h [host] -u [user] -p[password] [database] < schema.sql
```

---

## 🚀 Cập nhật Code (CI/CD tự động)

Mỗi khi bạn push code mới lên GitHub:

```bash
git add .
git commit -m "Update feature XYZ"
git push origin main
```

**Railway sẽ tự động:**
1. Phát hiện commit mới
2. Build lại Docker image
3. Deploy version mới
4. Zero-downtime deployment

**⏱️ Thời gian:** 2-3 phút/lần deploy

---

## 💡 Tips & Best Practices

### 🔒 Bảo mật

1. **Đổi password admin ngay sau khi deploy**
2. **Không commit file `.env` lên GitHub**
3. **Sử dụng mật khẩu mạnh cho ADMIN_PASS**
4. **Giới hạn quyền MySQL user** (chỉ cho quyền cần thiết)

### 📊 Monitoring

1. **Railway Dashboard** → tab "Metrics" để xem:
   - CPU usage
   - Memory usage
   - Network traffic

2. **Logs** → tab "Deploy Logs" để debug

### 💰 Quản lý Resource (500 giờ/tháng miễn phí)

- 1 project luôn chạy = ~720 giờ/tháng → vượt quota
- **Giải pháp:** 
  - Tắt service khi không dùng
  - Hoặc upgrade plan ($5/tháng)
  - Hoặc deploy nhiều project khác nhau

### 🌐 Custom Domain (Optional)

Nếu có domain riêng (ví dụ: `leaf.yourdomain.com`):

1. Tab Settings → Custom Domains
2. Thêm domain
3. Config CNAME record ở nhà cung cấp domain:
   ```
   CNAME: leaf.yourdomain.com → [railway-domain]
   ```

---

## 📚 Tài liệu tham khảo

- Railway Docs: https://docs.railway.app
- Railway MySQL: https://docs.railway.app/databases/mysql
- Railway CLI: https://docs.railway.app/develop/cli

---

## ❓ Hỗ trợ

Nếu gặp vấn đề:

1. **Kiểm tra Railway logs** (Deploy Logs + App Logs)
2. **Xem MySQL logs** (MySQL service → Logs)
3. **GitHub Issues:** https://github.com/dangbh01/leaf/issues
4. **Railway Discord:** https://discord.gg/railway

---

**🎉 Chúc bạn deploy thành công! 🚀**
