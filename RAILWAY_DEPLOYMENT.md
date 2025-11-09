# 🚂 Hướng dẫn Deploy Leaf lên Railway.app

## 📋 Tổng quan
Railway.app là platform miễn phí hỗ trợ deploy ứng dụng Docker với MySQL built-in. Miễn phí **500 giờ/tháng** và domain HTTPS tự động.

**⏱️ Thời gian:** 10-15 phút  
**💰 Chi phí:** MIỄN PHÍ (500 giờ/tháng)
**🌐 Domain:** `https://leaf-production-xxxx.up.railway.app`

---

## ✅ Yêu cầu trước khi bắt đầu

1. ✅ **Tài khoản GitHub** (để đăng nhập Railway)
2. ✅ **Code đã push lên GitHub repository**
3. ✅ **Tài khoản Railway** (đăng ký miễn phí bằng GitHub tại https://railway.app)

---

## 🚀 BƯỚC 1: Chuẩn bị Code

### 1.1. Kiểm tra file cần thiết

Đảm bảo repo của bạn có các file sau:
```
✅ Dockerfile                  # Build PHP 8.1 + Apache + MySQL client
✅ docker-compose.yml          # Local development (Railway không dùng)
✅ docker-entrypoint.sh        # Auto setup DB schema & admin
✅ schema.sql                  # Database tables definition
✅ config/database.php         # Hỗ trợ MYSQL_URL và biến riêng
✅ seed_admin.php              # Script tạo admin từ env vars
✅ .env.example                # Mẫu environment variables
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
- Tạo sẵn các biến môi trường (bao gồm `MYSQL_URL`)

### 3.2. Lấy thông tin Database

1. Click vào **MySQL** service trong dashboard
2. Chuyển sang tab **"Variables"**
3. Bạn sẽ thấy các biến:
   - `MYSQL_URL` (format: `mysql://user:pass@host:port/dbname`) ⭐ **Khuyến nghị**
   - `MYSQL_HOST`
   - `MYSQL_PORT` (3306)
   - `MYSQL_DATABASE`
   - `MYSQL_USER`
   - `MYSQL_PASSWORD`
   - `MYSQL_ROOT_PASSWORD`

**📝 Ghi chú:** 
- Bạn không cần copy, Railway tự động share biến này giữa các service
- Code hỗ trợ cả 2 cách: `MYSQL_URL` hoặc biến riêng lẻ

**✅ Checkpoint:** MySQL service đã chạy (màu xanh)

---

## 🚀 BƯỚC 4: Cấu hình Environment Variables cho Web App

### 4.1. Mở settings của Web service

1. Click vào **service "leaf"** (không phải MySQL)
2. Chuyển sang tab **"Variables"**

### 4.2. Thêm biến môi trường

Click **"New Variable"** và thêm theo một trong hai cách:

#### 🌟 CÁCH 1: Sử dụng MYSQL_URL (KHUYẾN NGHỊ - Đơn giản hơn)

```
MYSQL_URL = ${{MySQL.MYSQL_URL}}
ADMIN_USER = admin
ADMIN_PASS = YourSecurePassword123!
ADMIN_EMAIL = admin@youremail.com
ADMIN_FULL_NAME = Administrator
ADMIN_PHONE = 0987654321
```

**✅ Ưu điểm:** 
- Chỉ cần 1 biến database thay vì 5
- `config/database.php` tự động parse format `mysql://user:pass@host:port/dbname`
- Railway format chuẩn

#### 🔗 CÁCH 2: Sử dụng biến riêng lẻ (Tương thích local dev)

```
DB_HOST = ${{MySQL.MYSQL_HOST}}
DB_PORT = ${{MySQL.MYSQL_PORT}}
DB_NAME = ${{MySQL.MYSQL_DATABASE}}
DB_USER = ${{MySQL.MYSQL_USER}}
DB_PASS = ${{MySQL.MYSQL_PASSWORD}}
ADMIN_USER = admin
ADMIN_PASS = YourSecurePassword123!
ADMIN_EMAIL = admin@youremail.com
ADMIN_FULL_NAME = Administrator
ADMIN_PHONE = 0987654321
```

**⚠️ LƯU Ý QUAN TRỌNG:** 
- Gõ **CHÍNH XÁC** `${{MySQL.MYSQL_URL}}` hoặc `${{MySQL.MYSQL_HOST}}` (Railway sẽ tự động thay thế bằng giá trị thật)
- Nếu MySQL service tên khác, thay `MySQL` bằng tên đó
- **Thay `YourSecurePassword123!` bằng mật khẩu mạnh của bạn!**

### 4.3. Kiểm tra lại

**Nếu dùng MYSQL_URL:** Cần **6 biến**
- ✅ MYSQL_URL
- ✅ ADMIN_USER
- ✅ ADMIN_PASS
- ✅ ADMIN_EMAIL
- ✅ ADMIN_FULL_NAME (optional nhưng nên có)
- ✅ ADMIN_PHONE (optional)

**Nếu dùng biến riêng:** Cần **10 biến**
- ✅ DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
- ✅ ADMIN_USER, ADMIN_PASS, ADMIN_EMAIL, ADMIN_FULL_NAME, ADMIN_PHONE

**✅ Checkpoint:** Tất cả biến đã được thêm vào

---

## 🚀 BƯỚC 5: Redeploy & Kiểm tra Log

### 5.1. Trigger Redeploy

1. Vẫn ở service **"leaf"**
2. Chuyển sang tab **"Deployments"**
3. Click vào deployment mới nhất (hoặc Railway sẽ tự động redeploy khi thêm biến)

### 5.2. Xem Log để kiểm tra

1. Click vào deployment đang chạy
2. Xem tab **"Deploy Logs"**

**Kiểm tra các log quan trọng:**

```
✅ "Waiting for MySQL to initialize..."
✅ "DB_HOST=xxxx" (hoặc parsing MYSQL_URL)
✅ "✅ Proceeding with database connection..."
✅ "Setting up database schema..."
✅ "Importing schema..." (lần đầu tiên) hoặc "✅ Database tables already exist"
✅ "✅ Schema imported successfully"
✅ "Creating admin user..."
✅ "✅ Admin user created successfully!"
```

**Có thể thấy warning (bình thường, không sao):**
```
⚠️ "AH00558: apache2: Could not reliably determine the server's fully qualified domain name"
```

**❌ Nếu thấy lỗi:**
- `Connection refused` → MySQL chưa sẵn sàng, đợi thêm 1-2 phút
- `Access denied` → Sai DB_USER hoặc DB_PASS hoặc MYSQL_URL
- `Database setup error` → Kiểm tra schema.sql, có thể retry sau
- `ADMIN_USER is required` → Thiếu biến môi trường admin

**✅ Checkpoint:** Log cho thấy schema đã import và admin đã tạo thành công

---

## 🚀 BƯỚC 6: Database Schema & Admin User

### 6.1. Tự động Setup (Khuyến nghị - Đã cấu hình sẵn)

Script `docker-entrypoint.sh` sẽ **TỰ ĐỘNG**:

1. **Import schema.sql** khi:
   - Database trống (chưa có bảng)
   - Service khởi động lần đầu

2. **Tạo admin user** từ environment variables khi:
   - Schema đã được import thành công
   - Admin chưa tồn tại trong database

**Logic trong docker-entrypoint.sh:**
```bash
# Parse MYSQL_URL nếu có, fallback sang biến riêng
# Đợi MySQL sẵn sàng (sleep 5s)
# Check tables exist
# Nếu trống → import schema.sql và run seed_admin.php
# Nếu đã có tables → skip
```

**✅ Chỉ cần đợi deploy xong là OK!** Kiểm tra Deploy Logs để confirm.

### 6.2. Troubleshooting: Import thủ công (nếu cần)

**Nếu auto import không chạy:**

#### Option A: Via Railway MySQL Data Tab
1. Click vào **MySQL service**
2. Tab **"Data"** → click **"Query"**
3. Copy nội dung file `schema.sql` từ repo
4. Paste vào query editor và click **"Run Query"**

#### Option B: Via Railway CLI
```bash
# Install Railway CLI
npm i -g @railway/cli

# Login
railway login

# Link project
railway link

# Connect to MySQL và import
railway connect MySQL
# Sau đó trong MySQL prompt:
source /path/to/schema.sql
```

### 6.3. Kiểm tra Admin User

**Xem log để confirm:**
```
✅ Admin user created successfully!
   Username: admin
   Password: YourSecurePassword123!
```

**Nếu không thấy trong log, check database:**
1. Vào MySQL service → Data tab
2. Chạy query:
```sql
SELECT id, username, email, role FROM users WHERE role='admin';
```

**Nếu admin chưa tồn tại, tạo thủ công:**
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
**⚠️ Hash trên là password `password`, nhớ đổi sau khi login!**

**✅ Checkpoint:** Admin user đã được tạo và có thể login

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

### ❌ Lỗi "Connection refused" hoặc "Can't connect to MySQL"

**Nguyên nhân:** MySQL chưa sẵn sàng hoặc biến môi trường sai

**Giải pháp:**
1. Kiểm tra MySQL service đã chạy (màu xanh) trong Railway dashboard
2. Kiểm tra biến `MYSQL_URL` hoặc `DB_HOST`, `DB_USER`, `DB_PASS` đúng format reference: `${{MySQL.MYSQL_URL}}`
3. Đợi thêm 1-2 phút để MySQL khởi động hoàn toàn (Railway internal networking cần thời gian)
4. Redeploy web service (Click vào deployment → Redeploy)

### ❌ Lỗi "Access denied for user"

**Nguyên nhân:** Biến database credentials sai

**Giải pháp:**
1. Vào MySQL service → tab Variables
2. Copy chính xác `MYSQL_URL` hoặc các biến riêng lẻ
3. Paste vào biến của web service với format reference: `${{MySQL.MYSQL_URL}}`
4. **Không paste raw value**, dùng reference để Railway tự động sync

### ❌ Schema không được import

**Nguyên nhân:** 
- Database đã có tables từ lần deploy trước
- Script docker-entrypoint.sh gặp lỗi

**Giải pháp:**
1. Xem Deploy Logs, tìm "Importing schema..." hoặc "Database tables already exist"
2. Nếu không thấy, check file `schema.sql` có trong repo
3. Import thủ công qua MySQL Data tab (copy paste nội dung schema.sql)
4. Hoặc xóa database và redeploy (⚠️ mất data)

### ❌ Admin user không được tạo

**Nguyên nhân:**
- Thiếu biến `ADMIN_USER`, `ADMIN_PASS`, `ADMIN_EMAIL`
- Admin đã tồn tại từ trước
- Script seed_admin.php bị lỗi

**Giải pháp:**
1. Kiểm tra Deploy Logs, tìm "Creating admin user..." và "✅ Admin user created successfully!"
2. Nếu thấy "Admin already exists", dùng mật khẩu trong `ADMIN_PASS` để login
3. Nếu thấy lỗi "ADMIN_USER is required", thêm biến môi trường và redeploy
4. Check database:
   ```sql
   SELECT username, email, role FROM users WHERE role='admin';
   ```
5. Nếu không có, insert thủ công hoặc fix biến env và redeploy

### ❌ Upload ảnh bị lỗi

**Nguyên nhân:** 
- Thư mục uploads không có quyền write
- Persistent volume chưa được mount

**Giải pháp:**
1. Kiểm tra Deploy Logs có đoạn:
   ```
   Ensure uploads directory exists and is writable
   ```
2. Railway tự động mount persistent volume cho `/var/www/html/uploads`
3. Thử upload lại sau khi deploy hoàn toàn
4. Check file permissions trong logs

### ❌ Ảnh upload không hiển thị

**Nguyên nhân:**
- Đường dẫn ảnh sai
- Ảnh bị mất sau redeploy (nếu không dùng persistent volume)

**Giải pháp:**
1. Railway tự động persist `/var/www/html/uploads`
2. Check đường dẫn trong database (posts.image column)
3. Verify file tồn tại: vào MySQL Data tab query `SELECT id, title, image FROM posts;`

### ❌ Database bị reset sau mỗi deploy

**Nguyên nhân:** Railway MySQL service có persistent volume riêng

**Giải pháp:**
- Railway MySQL service data **KHÔNG bị mất** khi redeploy web service
- Chỉ mất khi **XÓA MySQL SERVICE**
- Web service code thay đổi không ảnh hưởng database

### ⚠️ Website chạy nhưng không có dữ liệu/bài đăng

**Nguyên nhân:** 
- Chưa có user đăng ký
- Chưa có bài đăng nào được approved

**Giải pháp:**
1. Login với admin account
2. Đăng ký user thường và tạo bài đăng
3. Admin approve bài đăng tại `/admin/manage_posts.php`
4. Bài đăng sẽ hiển thị trên trang chủ

### ❌ Lỗi "Database schema error" trong logs

**Nguyên nhân:**
- MySQL chưa sẵn sàng 100% khi web service start
- PHP script chạy quá sớm

**Giải pháp:**
- Script có logic retry: "Will retry when Apache starts..."
- Đợi thêm vài phút để MySQL stable
- Redeploy nếu cần

### ❌ Port/Apache configuration errors

**Nguyên nhân:**
- Railway tự động set biến `PORT`
- docker-entrypoint.sh configure Apache listen port

**Giải pháp:**
- Không cần lo lắng về warning "Could not reliably determine server's fully qualified domain name"
- Railway tự động expose đúng port
- Check Deploy Logs có "Configuring Apache to listen on port..."

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
   - Login với password từ `ADMIN_PASS`
   - Vào Profile → đổi mật khẩu
   
2. **Sử dụng mật khẩu mạnh cho ADMIN_PASS**
   - Ít nhất 12 ký tự
   - Bao gồm chữ hoa, chữ thường, số, ký tự đặc biệt
   
3. **Không commit file chứa secrets**
   - Không push `.env` lên GitHub
   - Dùng `.gitignore` để exclude
   
4. **Giới hạn quyền MySQL user** 
   - Railway tạo user với quyền đầy đủ cho database riêng
   - An toàn vì isolated per project

### 📊 Monitoring

1. **Railway Dashboard → Metrics**
   - CPU usage
   - Memory usage
   - Network traffic
   - Disk usage (cho uploads)

2. **Logs**
   - **Deploy Logs:** Xem quá trình build và setup
   - **App Logs:** Runtime errors, PHP errors, Apache logs
   - Filter theo severity: Error, Warning, Info

3. **Database Monitoring**
   - MySQL service → Metrics
   - Connections, queries, storage

### 💰 Quản lý Resource (500 giờ/tháng miễn phí)

- **1 web service + 1 MySQL luôn chạy** = ~1440 giờ/tháng → **vượt quota**
- **Giải pháp:**
  - **Option 1:** Tắt service khi không dùng (Development mode)
  - **Option 2:** Upgrade plan ($5/tháng cho unlimited hours + 512MB RAM)
  - **Option 3:** Sleep/wake theo schedule (Railway Pro feature)
  
### 🚀 Performance Tips

1. **Enable PHP OPcache** (thêm vào Dockerfile nếu cần):
   ```dockerfile
   RUN docker-php-ext-install opcache
   ```

2. **Optimize uploads:**
   - Resize images trước khi save
   - Compress images (JPEG quality 80-85%)
   - Limit file size trong upload validation

3. **Database indexing:**
   - Schema đã có indexes trên foreign keys
   - Thêm index cho `posts.post_status` nếu cần:
     ```sql
     CREATE INDEX idx_post_status ON posts(post_status);
     ```

### 🌐 Custom Domain (Optional)

Nếu có domain riêng (ví dụ: `leaf.yourdomain.com`):

1. **Railway Dashboard:**
   - Service Settings → Networking → Custom Domains
   - Thêm domain của bạn

2. **DNS Configuration (ở nhà cung cấp domain):**
   ```
   CNAME: leaf.yourdomain.com → [railway-generated-domain].up.railway.app
   ```

3. **SSL Certificate:**
   - Railway tự động provision Let's Encrypt SSL
   - HTTPS enabled by default

### 🔄 CI/CD Best Practices

1. **Automatic deployment:**
   - Railway tự động deploy khi push lên GitHub
   - Configure deployment branch trong Settings

2. **Healthcheck:**
   - Railway tự động check nếu service respond
   - Có thể config custom healthcheck endpoint

3. **Rollback:**
   - Deployments tab → click vào deployment cũ → Redeploy
   - Instant rollback đến version trước

### 📦 Backup Strategy

1. **Database backup:**
   - Railway không auto backup trong free plan
   - Manual backup: MySQL Data tab → Export data
   - Hoặc dùng `mysqldump` qua Railway CLI

2. **Uploads backup:**
   - Downloads files từ persistent volume
   - Railway CLI: `railway run` rồi copy files

3. **Scheduled backups:**
   - Chạy cron job trên máy local
   - Script backup database định kỳ

---

## 📚 Tài liệu tham khảo

- **Railway Official Docs:** https://docs.railway.app
- **Railway MySQL Database:** https://docs.railway.app/databases/mysql
- **Railway CLI:** https://docs.railway.app/develop/cli
- **Railway Environment Variables:** https://docs.railway.app/develop/variables
- **Railway Networking:** https://docs.railway.app/deploy/networking

### Các file quan trọng trong project

- **`docker-entrypoint.sh`:** Auto setup script (schema import, admin creation, Apache config)
- **`config/database.php`:** Database connection với MYSQL_URL parsing support
- **`seed_admin.php`:** Admin user creation từ environment variables
- **`schema.sql`:** Database schema với 3 tables (users, posts, orders)
- **`.env.example`:** Template cho environment variables

---

## ❓ Hỗ trợ

Nếu gặp vấn đề:

1. **Kiểm tra Railway logs:**
   - Deploy Logs (setup và build process)
   - App Logs (runtime errors)
   - MySQL Logs (database issues)

2. **Check database:**
   - MySQL service → Data tab
   - Query tables để verify data

3. **Common issues:**
   - Connection errors → Check MySQL running và biến env đúng
   - Schema errors → Verify schema.sql imported
   - Admin login fails → Check ADMIN_PASS và query users table
   - Upload errors → Check uploads directory permissions trong logs

4. **GitHub Issues:** https://github.com/dangbh01/leaf/issues

5. **Railway Community:**
   - Railway Discord: https://discord.gg/railway
   - Railway Forum: https://help.railway.app

---

**🎉 Chúc bạn deploy thành công! 🚀**

Nếu có thắc mắc hoặc gặp lỗi không có trong troubleshooting, tạo issue trên GitHub hoặc hỏi trong Railway Discord.
