# 🚀 CI/CD VPS Deployment Guide for Door Step BD Admin & API

This repository includes a ready-to-use **GitHub Actions CI/CD workflow** that automatically tests and deploys your Laravel Admin Panel & API to your VPS on every `git push` to `main`.

---

## 🛠️ Step 1: Configure GitHub Repository Secrets

Go to your GitHub repository:
👉 **Settings** → **Secrets and variables** → **Actions** → **New repository secret**

Add the following **Repository Secrets**:

| Secret Name | Description | Example / Value |
| :--- | :--- | :--- |
| `SSH_HOST` | VPS IP address or hostname | `123.45.67.89` or `admin.doorstepbd.com` |
| `SSH_USER` | VPS SSH username | `root` or `ubuntu` |
| `SSH_PRIVATE_KEY` | VPS Private SSH Key *(Recommended)* | Content of `~/.ssh/id_rsa` or `~/.ssh/id_ed25519` |
| `SSH_PASSWORD` | VPS SSH Password *(If not using SSH Key)* | `your_vps_root_password` |
| `SSH_PORT` | SSH Port *(Optional, default 22)* | `22` |
| `DEPLOY_PATH` | Path where project will live *(Optional)* | `/var/www/admin.doorstepbd.com` |

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
# Create directory
sudo mkdir -p /var/www/admin.doorstepbd.com

# Give current user and www-data ownership
sudo chown -R $USER:www-data /var/www/admin.doorstepbd.com
sudo chmod -R 775 /var/www/admin.doorstepbd.com

# Clone the repository for the first time
git clone https://github.com/Mostafizur10681/door-step-bd-admin-api.git /var/www/admin.doorstepbd.com

# Create/Upload your production .env manually
nano /var/www/admin.doorstepbd.com/.env
# (Paste your production environment variables, save with CTRL+O, exit with CTRL+X)
```

### 5. Configure Nginx Web Server
Copy the template from `deploy/nginx/admin.conf` to Nginx:
```bash
sudo cp /var/www/admin.doorstepbd.com/deploy/nginx/admin.conf /etc/nginx/sites-available/admin.doorstepbd.com.conf
```
Enable the site and restart Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/admin.doorstepbd.com.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 6. Install Free SSL Certificate (Let's Encrypt)
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d admin.doorstepbd.com
```

### 7. (Optional) Setup Queue Worker Supervisor
```bash
sudo cp /var/www/admin.doorstepbd.com/deploy/supervisor/queue-worker.conf /etc/supervisor/conf.d/
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start doorstep-bd-worker:*
```

---

## ⚡ Step 3: Trigger Automated Deployment

Whenever you push to the `main` branch or click **Run workflow** manually under the **Actions** tab in GitHub:

1. Connects securely via SSH to your VPS.
2. Pulls the latest commits.
3. Automatically syncs `.env` configuration.
4. Installs and optimizes Composer packages.
5. Builds Vite / Tailwind assets (`npm run build`).
6. Runs database migrations (`php artisan migrate --force`).
7. Creates storage symlink (`php artisan storage:link`).
8. Caches configs, routes, and views for lightning-fast response times.
9. Resets queue workers and reloads PHP-FPM.
