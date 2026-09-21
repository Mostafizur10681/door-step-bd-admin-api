# 🚀 CI/CD VPS Deployment Guide for Door Step BD Admin & API

This repository includes a ready-to-use **GitHub Actions CI/CD workflow** that automatically deploys your Laravel Admin Panel & API to your VPS on every `git push` to `main` (or manual trigger).

---

## 🛠️ Step 1: Configure GitHub Repository Secrets

Go to your GitHub repository:
👉 **Settings** → **Secrets and variables** → **Actions** → **New repository secret**

Add the following **Repository Secrets**:

| Secret Name | Description | Example / Value |
| :--- | :--- | :--- |
| `SSH_HOST` | VPS IP address or hostname | `123.45.67.89` or `admin.doorstepbd.org` |
| `SSH_USER` | VPS SSH username | `root` or `ubuntu` |
| `SSH_PRIVATE_KEY` | VPS Private SSH Key | Content of your private SSH key (e.g. `~/.ssh/id_rsa` or `~/.ssh/id_ed25519`) |
| `SSH_PORT` | SSH Port *(Optional, default 22)* | `22` |
| `DEPLOY_PATH` | Server project directory | `/var/www/admin.doorstepbd.org` |

---

## ⚙️ Step 2: One-Time VPS Server Setup (Ubuntu 22.04 / 24.04)

Log in to your VPS via SSH and run the following commands:

### 1. Update Packages & Install System Tools
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y git curl wget unzip zip software-properties-common nginx supervisor
```

### 2. Install PHP 8.2 & Essential Extensions
```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd php8.2-intl \
    php8.2-sqlite3 php8.2-redis
```

### 3. Install Composer & Node.js
```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version

# Install Node.js (v20 LTS) & NPM
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
node -v && npm -v
```

### 4. Create Web Directory & Set Permissions
```bash
# Create target directory
sudo mkdir -p /var/www/admin.doorstepbd.org

# Set ownership to current user and www-data
sudo chown -R $USER:www-data /var/www/admin.doorstepbd.org
sudo chmod -R 775 /var/www/admin.doorstepbd.org

# Clone the repository for the first time
git clone https://github.com/Mostafizur10681/door-step-bd-admin-api.git /var/www/admin.doorstepbd.org

# Navigate into directory
cd /var/www/admin.doorstepbd.org

# Create production .env file
cp .env.example .env
nano .env
# (Configure APP_ENV=production, APP_DEBUG=false, APP_URL=https://admin.doorstepbd.org, DB credentials, etc.)
```

### 5. Configure Nginx Web Server
Copy the template from `deploy/nginx/admin.conf` to Nginx:
```bash
sudo cp /var/www/admin.doorstepbd.org/deploy/nginx/admin.conf /etc/nginx/sites-available/admin.doorstepbd.org.conf
```
Enable the site and restart Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/admin.doorstepbd.org.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 6. Install Free SSL Certificate (Let's Encrypt)
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d admin.doorstepbd.org
```

### 7. (Optional) Setup Queue Worker Supervisor
```bash
sudo cp /var/www/admin.doorstepbd.org/deploy/supervisor/queue-worker.conf /etc/supervisor/conf.d/
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start doorstep-bd-worker:*
```

---

## ⚡ Step 3: Trigger Automated Deployment

Whenever you push to the `main` branch or click **Run workflow** manually under the **Actions** tab in GitHub:

1. Connects securely via SSH to your VPS.
2. Puts application into maintenance mode (`php artisan down`).
3. Pulls latest changes (`git fetch & reset`).
4. Installs optimized Composer packages (`composer install --no-dev`).
5. Installs npm packages and builds Vite assets (`npm run build`).
6. Runs database migrations (`php artisan migrate --force`).
7. Ensures storage link is created (`php artisan storage:link`).
8. Caches configs, routes, views, and events (`config:cache`, `route:cache`, `view:cache`, `event:cache`).
9. Restarts background queue workers & reloads PHP-FPM.
10. Brings application out of maintenance mode (`php artisan up`).
