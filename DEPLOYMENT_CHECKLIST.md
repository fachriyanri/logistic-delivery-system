# PuninarLogistic Deployment Checklist

## Pre-Deployment Setup

### 1. Environment Requirements
- [ ] PHP 8.0 or higher
- [ ] MySQL 5.7 or higher  
- [ ] Web server (Apache/Nginx) with mod_rewrite enabled
- [ ] Composer installed

### 2. Required PHP Extensions
- [ ] mysqli
- [ ] mbstring
- [ ] xml
- [ ] gd or imagick
- [ ] intl
- [ ] fileinfo
- [ ] json

## Deployment Steps

### 1. Code Transfer
- [ ] Copy all project files to web server
- [ ] Ensure `.git` folder is excluded from public access
- [ ] Set web root to `public/` directory

### 2. Environment Configuration
- [ ] Copy `.env.example` to `.env` (if using)
- [ ] Update database configuration in `.env`:
  ```ini
  database.default.hostname = localhost
  database.default.database = puninar_logistic
  database.default.username = your_username
  database.default.password = your_password
  ```
- [ ] **IMPORTANT**: Remove or comment out hardcoded paths:
  ```ini
  # session.savePath = E:/laragon/www/PuninarLogistic/writable/session
  ```
- [ ] Generate encryption key if needed

### 3. Directory Setup
- [ ] Run the setup script: `php setup_directories.php`
- [ ] Verify writable directory permissions (755)
- [ ] Ensure web server can write to `writable/` directory

### 4. Database Setup
- [ ] Create database: `puninar_logistic`
- [ ] Import database from: `db/puninar_logistic.sql`
- [ ] Run migrations if any: `php spark migrate`
- [ ] Seed database if needed: `php spark db:seed`

### 5. Web Server Configuration

#### Apache (.htaccess)
Ensure `.htaccess` files are present:
- `public/.htaccess` (URL rewriting)
- `writable/.htaccess` (protection)

#### Nginx
Add this to your server block:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ ^/(writable|vendor) {
    deny all;
}
```

### 6. File Permissions
Set proper permissions:
```bash
# For Linux/Unix systems
chmod -R 755 /path/to/puninarlogistic
chmod -R 777 /path/to/puninarlogistic/writable
chown -R www-data:www-data /path/to/puninarlogistic

# For Windows (using cacls)
cacls writable /g everyone:f /t
```

## Testing

### 1. Basic Functionality
- [ ] Access homepage: `http://your-domain/`
- [ ] Test login with default credentials
- [ ] Check if sessions work (login persistence)
- [ ] Verify file uploads work

### 2. Database Connectivity
- [ ] Test CRUD operations on any module
- [ ] Check if migrations table exists
- [ ] Verify foreign key constraints

### 3. Error Handling
- [ ] Check `writable/logs/` for error logs
- [ ] Test 404 pages
- [ ] Verify debug mode is OFF in production

## Troubleshooting Common Issues

### "mkdir: No such file or directory" Error

**Causes:**
1. Hardcoded paths in configuration
2. Missing writable directories  
3. Insufficient permissions
4. Path separator issues (Windows vs Linux)

**Solutions:**
1. Run `php setup_directories.php`
2. Check `.env` for hardcoded paths
3. Verify directory permissions
4. Ensure parent directories exist

### Database Connection Issues

**Symptoms:**
- "Unable to connect to your database server"
- SQLSTATE[HY000] [2002] Connection refused

**Solutions:**
1. Verify database credentials in `.env`
2. Check if MySQL service is running
3. Test connection: `mysql -u username -p`
4. Verify hostname (localhost vs 127.0.0.1)

### Session/Cache Issues

**Symptoms:**
- Login doesn't persist
- "Session: Configured save path is not writable"

**Solutions:**
1. Check `writable/session/` exists and is writable
2. Remove hardcoded session paths from `.env`
3. Clear session files: `rm writable/session/ci_*`

### File Upload Issues

**Symptoms:**
- Upload fails silently
- "Move uploaded file failed"

**Solutions:**
1. Check `writable/uploads/` permissions
2. Verify `upload_max_filesize` in php.ini
3. Check `post_max_size` configuration

### URL Rewriting Issues

**Symptoms:**
- 404 errors on all pages except index
- URLs show `index.php`

**Solutions:**
1. Enable mod_rewrite in Apache
2. Check `.htaccess` files are present
3. Verify `AllowOverride All` in Apache config

## Environment-Specific Notes

### Development (Laragon/XAMPP)
- Default permissions usually sufficient
- Database typically uses root with no password
- Error reporting should be enabled

### Production
- Stricter file permissions required
- Database should use dedicated user
- Error reporting should be disabled
- HTTPS should be enforced

### Linux/Unix
- Use `chmod` and `chown` for permissions
- Web server user typically `www-data` or `apache`
- Path separators use forward slash `/`

### Windows
- Use `cacls` or `icacls` for permissions
- Web server user varies by software
- Path separators use backslash `\` but PHP accepts both

## Security Checklist

- [ ] Remove or secure database import files
- [ ] Ensure `writable/` is not web accessible
- [ ] Disable directory browsing
- [ ] Set secure session configuration
- [ ] Enable HTTPS in production
- [ ] Remove debug information in production
- [ ] Set strong database passwords
- [ ] Regular security updates

## Performance Optimization

- [ ] Enable caching in production
- [ ] Optimize database indexes
- [ ] Compress static assets
- [ ] Enable gzip compression
- [ ] Set proper cache headers

---

## Quick Setup Commands

For new deployments, run these commands in order:

```bash
# 1. Navigate to project directory
cd /path/to/puninarlogistic

# 2. Set up directories and permissions
php setup_directories.php

# 3. Install dependencies (if composer.json exists)
composer install --no-dev --optimize-autoloader

# 4. Set up database
mysql -u root -p -e "CREATE DATABASE puninar_logistic;"
mysql -u root -p puninar_logistic < db/puninar_logistic.sql

# 5. Test the application
curl -I http://your-domain/
```

Remember to adapt paths and commands to your specific environment!