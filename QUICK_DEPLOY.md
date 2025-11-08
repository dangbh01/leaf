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

3. **Cấu hình biến môi trường:**
   
   Vào service "leaf" → tab Variables → thêm:
   
   ```
   DB_HOST = ${{MySQL.MYSQL_HOST}}
   DB_PORT = ${{MySQL.MYSQL_PORT}}
   DB_NAME = ${{MySQL.MYSQL_DATABASE}}
   DB_USER = ${{MySQL.MYSQL_USER}}
   DB_PASS = ${{MySQL.MYSQL_PASSWORD}}
   ADMIN_USER = admin
   ADMIN_PASS = MatKhauManh123!
   ADMIN_EMAIL = admin@gmail.com
   ADMIN_FULL_NAME = Administrator
   ADMIN_PHONE = 0987654321
   ```
   
   **⚠️ QUAN TRỌNG:** Đổi `MatKhauManh123!` thành mật khẩu của bạn!

4. **Generate Domain:**
   - Vào Settings → Networking → Generate Domain
   - Copy domain: `https://leaf-production-xxxx.up.railway.app`

5. **Kiểm tra:**
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

## 📚 Hướng dẫn chi tiết

Xem file: [`RAILWAY_DEPLOYMENT.md`](./RAILWAY_DEPLOYMENT.md)

---

## ❓ Troubleshooting nhanh

**❌ Lỗi "Connection refused":**
- Kiểm tra MySQL service đã chạy (màu xanh)
- Kiểm tra biến `DB_HOST` đúng format `${{MySQL.MYSQL_HOST}}`
- Redeploy web service

**❌ Không login được admin:**
- Kiểm tra đã set `ADMIN_USER`, `ADMIN_PASS`, `ADMIN_EMAIL`
- Xem Deploy Logs → tìm dòng "Creating admin user..."
- Nếu báo lỗi, vào MySQL Data tab chạy:
  ```sql
  DELETE FROM users WHERE username='admin';
  ```
  Rồi redeploy

**❌ Upload ảnh lỗi:**
- Railway tự động mount persistent disk cho `/var/www/html/uploads`
- Kiểm tra Deploy Logs có dòng tạo thư mục uploads

---

**🎉 Chúc deploy thành công!**
