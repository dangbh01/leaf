# Copilot Instructions for Leaf (leaf-02)

## Project Overview
- **Leaf** is a PHP 8.1 web app for students to exchange, buy, or give away study materials.
- Follows a simple MVC-like structure (no framework): logic is split between user-facing pages (root), admin tools (`admin/`), and configuration (`config/`).
- Data is stored in MySQL/MariaDB, accessed via PDO (see `config/database.php`).
- File uploads (images) are stored in `uploads/posts/`.

## Key Components
- **User features:** Registration, login, post creation (with images), order/interest registration, profile management, personal post/order management, sharing, and help page.
- **Admin features:** Post moderation, user management, statistics, and order review. Admin routes are protected by `admin/auth.php`.
- **Database schema:** See `schema.sql` for tables: `users`, `posts`, `orders`. Each table has clear, documented fields.
- **Environment config:** Supports both Railway-style `MYSQL_URL` and individual DB vars. Admin credentials are set via env or `.env` file.

## Developer Workflows
- **Local development:**
  - Recommended: `docker compose up -d` (see `docker-compose.yml`, `Dockerfile`, `docker-entrypoint.sh`).
  - Manual: Use PHP built-in server (`php -S localhost:8000`), set up DB, import `schema.sql`, and run `seed_admin.php` to create an admin.
- **Deployment:**
  - Railway.app is supported (see `QUICK_DEPLOY.md` and `RAILWAY_DEPLOYMENT.md`).
  - Persistent uploads are handled by mounting `/var/www/html/uploads`.
- **Admin user creation:** Run `php seed_admin.php` after setting admin vars in `.env`.

## Project Conventions & Patterns
- **Routing:** Each PHP file in root or `admin/` is a page/endpoint. No front controller.
- **Session-based auth:** User and admin roles are checked via session; see `admin/auth.php` for admin protection.
- **Security:**
  - Passwords hashed with `password_hash()`
  - SQL injection prevented via PDO prepared statements
  - Image uploads validated for extension and location
  - Output escaping and CSRF protection are TODOs (see README)
- **Uploads:** All images go to `uploads/posts/`. Ensure this directory is writable.
- **No JS SPA:** All logic is server-side PHP; Bootstrap 5 is used for UI.

## Examples & References
- **Database connection:** `config/database.php`
- **Admin protection:** `admin/auth.php`
- **Post creation:** `create_post.php`
- **User management:** `admin/manage_users.php`
- **Order flow:** `order.php`, `my_orders.php`, `view_orders.php`

## Tips for AI Agents
- Always check for session and role before allowing admin actions.
- Use prepared statements for all DB queries.
- When adding new features, follow the file-per-page pattern.
- Reference `README.md` for setup, deployment, and environment details.
- For new uploads, ensure `uploads/posts/` exists and is writable.

---
For more, see `README.md`, `QUICK_DEPLOY.md`, and `RAILWAY_DEPLOYMENT.md`.
