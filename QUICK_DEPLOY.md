# ⚡ Quick Deploy Guide - Railway.app

## 🎯 Deploy trong 10 phút

### Bước 1: Push code lên GitHub
```bash
git add .
git commit -m "Ready for Railway deployment"
git push origin main
```

### Bước 2: Deploy trên Railway

1. **Tạo project:**
   - Vào https://railway.app (login bằng GitHub)
   - New Project → Deploy from GitHub repo
   - Chọn repo `dangbh01/leaf`

2. **Thêm MySQL:**
   - Click "New" → Database → Add MySQL
   - Đợi MySQL chạy (màu xanh)
   - Railway tự động tạo biến `MYSQL_URL`

3. **Cấu hình biến môi trường:**
   
   Vào service "leaf" → tab Variables → thêm:
   
   **Option 1: Sử dụng MYSQL_URL (Khuyến nghị - Railway tự động)**
   ```
   MYSQL_URL = ${{MySQL.MYSQL_URL}}
   ADMIN_USER = admin
   ADMIN_PASS = MatKhauManh123!
   ADMIN_EMAIL = admin@example.com
   ADMIN_FULL_NAME = Administrator
   ADMIN_PHONE = 0987654321
   ```
   
   **Option 2: Sử dụng biến riêng lẻ**
   ```
   DB_HOST = ${{MySQL.MYSQL_HOST}}
   DB_PORT = ${{MySQL.MYSQL_PORT}}
   DB_NAME = ${{MySQL.MYSQL_DATABASE}}
   DB_USER = ${{MySQL.MYSQL_USER}}
   DB_PASS = ${{MySQL.MYSQL_PASSWORD}}
   ADMIN_USER = admin
   ADMIN_PASS = MatKhauManh123!
   ADMIN_EMAIL = admin@example.com
   ADMIN_FULL_NAME = Administrator
   ADMIN_PHONE = 0987654321
   ```
   
   **⚠️ QUAN TRỌNG:** 
   - Đổi `MatKhauManh123!` thành mật khẩu mạnh của bạn!
   - File `docker-entrypoint.sh` tự động xử lý cả 2 format

4. **Generate Domain:**
   - Vào Settings → Networking → Generate Domain
   - Copy domain: `https://leaf-production-xxxx.up.railway.app`

5. **Kiểm tra Deploy:**
   - Vào tab Deployments → xem Deploy Logs
   - Tìm dòng "✅ Schema imported successfully"
   - Tìm dòng "Creating admin user..."

6. **Truy cập & Login:**
   - Mở domain vừa tạo
   - Login với username: `admin`, password: (mật khẩu bạn đặt)
   - Done! 🎉

---

## 🔧 Cập nhật code sau này

```bash
git add .
git commit -m "Update something"
git push origin main
```

Railway tự động deploy lại sau 2-3 phút.

---

## 📚 Các tính năng tự động

**✅ Docker entrypoint script tự động:**
- Kiểm tra và import `schema.sql` nếu database trống
- Tạo admin user từ biến môi trường (nếu chưa tồn tại)
- Tạo thư mục `uploads/posts` với đúng permissions
- Hỗ trợ cả `MYSQL_URL` và biến riêng lẻ

**✅ Database config (`config/database.php`):**
- Parse `MYSQL_URL` format: `mysql://user:pass@host:port/dbname`
- Fallback sang biến riêng lẻ nếu không có `MYSQL_URL`
- Tự động set charset utf8mb4

---

## 📚 Hướng dẫn chi tiết

Xem file: [`RAILWAY_DEPLOYMENT.md`](./RAILWAY_DEPLOYMENT.md)

---

## ❓ Troubleshooting nhanh

**❌ Lỗi "Connection refused" hoặc "Can't connect to MySQL":**
- Kiểm tra MySQL service đã chạy (màu xanh) trong Railway dashboard
- Kiểm tra biến `MYSQL_URL` hoặc `DB_HOST` đúng format reference: `${{MySQL.MYSQL_URL}}`
- Đợi thêm 1-2 phút để MySQL khởi động hoàn toàn
- Redeploy web service (Click vào deployment → Redeploy)

**❌ Không login được admin:**
- Xem Deploy Logs → tìm "Creating admin user..."
- Nếu thấy "Admin already exists", admin đã được tạo, dùng mật khẩu trong `ADMIN_PASS`
- Nếu thấy lỗi "ADMIN_USER is required", kiểm tra lại biến môi trường
- Nếu vẫn không được, vào MySQL Data tab chạy:
  ```sql
  SELECT username, email FROM users WHERE role='admin';
  ```
  Để xem admin có tồn tại không

**❌ Schema không được import:**
- Xem Deploy Logs, tìm "Importing schema..." hoặc "Database tables already exist"
- Nếu không thấy, kiểm tra file `schema.sql` có trong repo không
- Redeploy lại hoặc import thủ công qua MySQL Data tab

**❌ Upload ảnh lỗi hoặc ảnh không hiển thị:**
- Kiểm tra Deploy Logs có dòng tạo thư mục uploads
- Railway tự động mount persistent volume cho `/var/www/html/uploads`
- Thử upload lại sau khi deploy xong hoàn toàn
- Kiểm tra permissions trong logs

**❌ Lỗi "Database schema error" trong logs:**
- Database có thể chưa sẵn sàng khi web service khởi động
- Script sẽ retry khi Apache starts
- Đợi thêm vài phút hoặc redeploy

**❌ Port/Apache errors:**
- Railway tự động set biến `PORT`, docker-entrypoint.sh sẽ configure Apache
- Không cần lo lắng về cảnh báo "Could not reliably determine server's fully qualified domain name"

---

## 💡 Tips

- **Check logs thường xuyên:** Deploy Logs cho setup, App Logs cho runtime errors
- **MySQL Data tab:** Dùng để query database trực tiếp, rất hữu ích để debug
- **Redeploy khi cần:** Nếu gặp vấn đề, thử redeploy thường fix được
- **Environment variables:** Thay đổi biến môi trường sẽ tự động trigger redeploy
- **MYSQL_URL format:** Railway format là `mysql://user:pass@host:port/dbname`, code tự động parse

---

**🎉 Chúc deploy thành công!**
