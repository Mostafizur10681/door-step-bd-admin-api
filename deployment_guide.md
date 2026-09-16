# cPanel Deployment Guide for E-commerce Platform

This guide provides step-by-step instructions for deploying your Vue.js frontend, Next.js dashboard, and Laravel backend on a standard cPanel hosting environment.

---

## 1. Vue.js Frontend (SPA) Deployment

The compiled frontend is packaged in `E:\e-commerce\frontend\frontend-dist.zip`.

### Steps:
1. **Upload:** Upload the `frontend-dist.zip` file to your cPanel's **File Manager**. Place it in the directory corresponding to your primary domain (usually `public_html`).
2. **Extract:** Extract the contents of `frontend-dist.zip` directly into the `public_html` directory.
3. **SPA Routing Configuration (.htaccess):**
   To ensure that Vue Router works correctly when users reload the page (and doesn't return a 404 error), you must configure a rewrite rule.
   * In cPanel File Manager, ensure "Show Hidden Files" is checked in Settings (top right).
   * Create or edit a `.htaccess` file in `public_html` and add the following rules:
     ```apache
     <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteBase /
       RewriteRule ^index\.html$ - [L]
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteRule . /index.html [L]
     </IfModule>
     ```

---

## 2. Next.js Dashboard Deployment

The production build of the dashboard is packaged in `E:\e-commence-dashboard\dashboard-prod.zip`. This ZIP contains `.next`, `public`, `package.json`, `package-lock.json`, `.env`, and a custom startup `server.js` file.

### Steps:
1. **Setup Node.js App in cPanel:**
   * Log into cPanel and search for **Setup Node.js App**.
   * Click **Create Application**.
   * Fill out the configuration form:
     * **Node.js version:** Select `18.x` or `20.x` (or newer).
     * **Application mode:** Select `Production`.
     * **Application root:** Enter a folder name outside public_html, e.g., `dashboard-app`.
     * **Application URL:** Select or input the domain or subdomain you want to host it on (e.g., `admin.yourdomain.com`).
     * **Application startup file:** Enter `server.js`.
   * Click **Create** (this generates the folder structure and configuration).
2. **Upload and Extract:**
   * Go to **File Manager** and locate the application root directory you just created (e.g., `/home/username/dashboard-app`).
   * Upload `dashboard-prod.zip` to this directory and extract it. Overwrite any default template files if prompted.
3. **Configure Environment Variables:**
   * Edit the `.env` file in the `dashboard-app` folder.
   * Update `NEXT_PUBLIC_API_URL` to point to your live Laravel backend URL:
     ```env
     NEXT_PUBLIC_API_URL=https://api.yourdomain.com
     ```
4. **Install Dependencies:**
   * Go back to the **Setup Node.js App** dashboard.
   * Click on the edit icon next to your application.
   * Scroll down and click the **Run npm install** button. This will install only the production dependencies in cPanel.
5. **Start/Restart Application:**
   * Click the **Restart** button at the top of the Setup Node.js App page to boot your Next.js application.

---

## 3. Laravel Backend Deployment

The backend files are packaged in `E:\xampp\htdocs\e-commerce-backend\backend-prod.zip`. Vendor libraries and development cache are excluded to keep the package lightweight.

### Steps:
1. **Database Setup:**
   * In cPanel, open **MySQL Database Wizard**.
   * Create a new database (e.g., `yourdomain_db`).
   * Create a new database user (e.g., `yourdomain_user`) and a secure password.
   * Add the user to the database and check the box for **ALL PRIVILEGES**.
2. **Upload and Extract:**
   * Create a directory in your cPanel File Manager outside `public_html` (e.g., `/home/username/e-commerce-backend`).
   * Upload `backend-prod.zip` to this folder and extract it.
3. **Domain Mapping:**
   * In cPanel, go to **Domains** or **Subdomains**.
   * Create a subdomain for your API (e.g., `api.yourdomain.com`).
   * Set its **Document Root** to point to the `public/` directory of your backend folder: `/home/username/e-commerce-backend/public`.
4. **Configure Environment Variables:**
   * In cPanel File Manager, edit `/home/username/e-commerce-backend/.env`.
   * Configure the production variables:
     ```env
     APP_ENV=production
     APP_DEBUG=false
     APP_URL=https://api.yourdomain.com

     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=yourdomain_db
     DB_USERNAME=yourdomain_user
     DB_PASSWORD=your_secure_password
     ```
5. **Install Vendor Libraries (Composer):**
   * If you have **SSH Access**:
     * SSH into your server, navigate to the backend folder (`cd /home/username/e-commerce-backend`).
     * Run: `composer install --no-dev --optimize-autoloader`
   * If you **Do Not** have SSH Access:
     * Compress your local `vendor` folder (located in `E:\xampp\htdocs\e-commerce-backend\vendor`).
     * Upload and extract it directly into `/home/username/e-commerce-backend/`.
6. **Run Database Migrations:**
   * If SSH is available, run: `php artisan migrate --force`
   * If SSH is disabled, you can run migrations by setting up a temporary route in `routes/web.php` or using a cPanel **Cron Job**:
     * Add a Cron Job to run once: `cd /home/username/e-commerce-backend && php artisan migrate --force`
7. **Storage Symlink:**
   * To ensure uploaded files are visible, run `php artisan storage:link` via SSH or as a one-time Cron Job:
     * `cd /home/username/e-commerce-backend && php artisan storage:link`
