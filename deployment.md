# Deployment Guide for Spot 2 Delivery (Laravel Backend)

This document details the deployment process and environment configuration for the **Spot 2 Delivery** backend application, hosted on **GoDaddy Web Hosting Economy (cPanel)**.

---

## 1. Environment & Architecture
- **Framework:** PHP Laravel
- **Hosting Provider:** GoDaddy (Web Hosting Economy)
- **Control Panel:** cPanel
- **Domain:** [spot2delivery.com](https://spot2delivery.com)
- **Database:** MySQL

---

## 2. Directory Structure
- **Production Root:** `/home/<cpanel_user>/public_html/spot2delivery.com`
- **Database Backups:** `.mysql_backup/` contains the SQL backup (`spotfood.sql.gz`).

---

## 3. Step-by-Step Deployment Procedure

### Step 1: Upload Files to cPanel
1. Compress the project folder `spot2delivery.com` (excluding the large vendor directory to save time, or use `spot2delivery.com.zip` if uploading the full package).
2. Log in to your GoDaddy cPanel account.
3. Open the **File Manager** and navigate to the directory:
   `/public_html/spot2delivery.com`
4. Upload the zip file and extract it.

### Step 2: Database Setup & Import
1. In cPanel, navigate to **MySQL® Databases**.
2. Create a new database named `spotfood`.
3. Create a new database user named `spotfood` with a strong password (e.g., `Rishabh@7380`).
4. Associate the user with the database and grant **All Privileges**.
5. Open **phpMyAdmin** from cPanel, select the `spotfood` database, and import the database backup file:
   - Decompress `.mysql_backup/spotfood.sql.gz` and import the `.sql` file.

### Step 3: Configure Environment Variables
Create or update the `.env` file in the production root directory with the following values:
```env
APP_NAME=stackfood1741612647
APP_ENV=live
APP_KEY=base64:LJw2MMwgKylfdfirSeS5M9543xuWBIfZ27tc9j/cdtk=
APP_DEBUG=false
APP_INSTALL=true
APP_MODE=live
APP_URL=https://spot2delivery.com/

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=spotfood
DB_USERNAME=spotfood
DB_PASSWORD=Rishabh@7380
```

### Step 4: Configure Cron Jobs (Task Scheduler)
Set up the following cron jobs in cPanel **Cron Jobs** section to ensure disbursements and regular tasks execute correctly:
```bash
# Disbursement Scheduler
00 10 * * * /usr/bin/php /home/<cpanel_user>/public_html/spot2delivery.com/artisan dm:disbursement

# Restaurant Disbursement Scheduler
00 11 * * * /usr/bin/php /home/<cpanel_user>/public_html/spot2delivery.com/artisan restaurant:disbursement
```

---

## 4. Verification and Testing
After deployment, verify the following:
1. Visit `https://spot2delivery.com/` to ensure the application loads.
2. Check the Laravel logs under `storage/logs/laravel.log` for any runtime issues or permission errors.
3. Ensure directory permissions for `storage` and `bootstrap/cache` are set to writeable (`775` or `755`).
