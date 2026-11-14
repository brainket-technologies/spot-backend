# Spot 2 Delivery - Backend Deployment & Infrastructure Guide

This document contains complete architectural, server, database, and CI/CD deployment documentation for the **Spot 2 Delivery (Laravel Backend)** hosted on **GoDaddy Web Hosting (cPanel)**.

---

## 1. Server & Environment Information

| Parameter | Value |
|---|---|
| **Domain** | [https://spot2delivery.com](https://spot2delivery.com) |
| **API Endpoint** | [https://spot2delivery.com/api/v1/config](https://spot2delivery.com/api/v1/config) |
| **Hosting Provider** | GoDaddy Web Hosting (cPanel Jupiter Theme) |
| **Server Hostname / IP** | `spot2delivery.com` / `68.178.148.191` |
| **cPanel / SSH Username** | `fl65s7bvagbn` |
| **SSH Port** | `22` |
| **Production Root** | `/home/fl65s7bvagbn/public_html` |
| **PHP Version** | `PHP 8.3` (CLI: `/usr/local/bin/php`) |
| **Framework** | Laravel (StackFood Multi-Restaurant System) |
| **GitHub Repository** | `https://github.com/brainket-technologies/spot-backend.git` |
| **Production Branch** | `main` (Auto-deploys to GoDaddy) |
| **Development Branch** | `dev_firoz` (Active coding & testing) |

---

## 2. Database Configuration

| Setting | Value |
|---|---|
| **Connection** | `mysql` |
| **Host** | `127.0.0.1` |
| **Port** | `3306` |
| **Database Name** | `spotfood` |
| **Database Username** | `spotfood` |
| **Database Password** | `SpotFood@2026!` |

> **Note:** The server's production `.env` is located at `/home/fl65s7bvagbn/public_html/.env`. It contains live keys and credentials and is protected from being overwritten during automated deployments.

---

## 3. Git Branching & Deployment Workflow

```
[ dev_firoz ]  ---> Coding & Local Testing
      │
      ▼ (Merge / PR)
  [ main ]     ---> Auto-Deploy triggers via GitHub Actions ---> GoDaddy Live Server
```

### Day-to-Day Development Flow:
1. **Work on `dev_firoz` branch:**
   ```bash
   git checkout dev_firoz
   # Make your changes, code & test
   git add .
   git commit -m "Your commit message"
   git push origin dev_firoz
   ```
2. **Deploy to Live (GoDaddy):**
   Jab testing complete ho jaye aur live karna ho:
   ```bash
   git checkout main
   git merge dev_firoz
   git push origin main
   git checkout dev_firoz
   ```
   *Jaise hi `main` par push hoga, GitHub Actions automatically live server par deploy kar dega!*

---

## 4. Automated CI/CD Deployment (GitHub Actions)

Every push to the `main` branch triggers an automated deployment via GitHub Actions:
- **Workflow File:** `.github/workflows/deploy.yml`
- **Trigger:** `git push origin main`

### Required GitHub Repository Secrets
Set these in **Repository Settings > Secrets and variables > Actions**:

| Secret Name | Value |
|---|---|
| `SSH_HOST` | `spot2delivery.com` |
| `SSH_USERNAME` | `fl65s7bvagbn` |
| `SSH_PRIVATE_KEY` | *(OpenSSH private key for `fl65s7bvagbn`)* |

### Deployment Pipeline Execution Steps:
1. Connects to the GoDaddy server via SSH.
2. Backs up current `.env` file (`cp .env .env.deploy_backup`).
3. Discards tracked local changes (`git checkout .`).
4. Pulls latest commit from `origin main` (`git pull origin main`).
5. Restores `.env` (`cp .env.deploy_backup .env`).
6. Executes any pending database migrations (`/usr/local/bin/php artisan migrate --force`).
7. Clears and optimizes caches (`/usr/local/bin/php artisan optimize:clear`).

---

## 5. Scheduled Tasks & Cron Jobs

The following cron jobs are configured and active on the server (`crontab -l`):

```bash
# 1. Laravel Task Scheduler (Runs every minute)
* * * * * /usr/local/bin/php /home/fl65s7bvagbn/public_html/artisan schedule:run >> /dev/null 2>&1

# 2. Delivery Man Disbursement (Every day at 10:00 AM)
00 10 * * * /usr/local/bin/php /home/fl65s7bvagbn/public_html/artisan dm:disbursement >> /dev/null 2>&1

# 3. Restaurant Disbursement (Every day at 11:00 AM)
00 11 * * * /usr/local/bin/php /home/fl65s7bvagbn/public_html/artisan restaurant:disbursement >> /dev/null 2>&1
```

---

## 6. Manual Server Access & Maintenance

### Connecting via SSH:
```bash
ssh -i /path/to/private_key fl65s7bvagbn@spot2delivery.com
```

### Useful Maintenance Commands on Server:
```bash
cd /home/fl65s7bvagbn/public_html

# Check git status
git status

# Manual git pull
git pull origin main

# Clear application cache
/usr/local/bin/php artisan optimize:clear

# Create storage symlink
/usr/local/bin/php artisan storage:link

# Verify database connection
/usr/local/bin/php artisan tinker --execute="echo DB::connection()->getDatabaseName();"

# Check storage permissions
chmod -R 775 storage bootstrap/cache
```

### Database Backup & Restore:
```bash
# Export / Backup Database
mysqldump -u spotfood -p'SpotFood@2026!' spotfood > /home/fl65s7bvagbn/backup_$(date +%Y%m%d_%H%M%S).sql

# Import / Restore Database
mysql -u spotfood -p'SpotFood@2026!' spotfood < /path/to/backup.sql
```

---

## 7. Verification Checklist

After any major update or migration, verify:
- [x] Website loads: [https://spot2delivery.com](https://spot2delivery.com)
- [x] API config loads: [https://spot2delivery.com/api/v1/config](https://spot2delivery.com/api/v1/config)
- [x] Admin Login works: [https://spot2delivery.com/login/admin](https://spot2delivery.com/login/admin)
- [x] Storage images load without 403/404 errors (`storage/` symlink active).
- [x] Cron jobs are active and running.
