# FrogBytes Database Setup Guide

## Quick Start (Recommended)

### Automated Setup
1. **Upload all files** to your web server
2. **Run the deployment script**:
   ```bash
   chmod +x deploy.sh
   ./deploy.sh
   ```
3. **Run the setup wizard**:
   ```bash
   php setup_wizard.php
   ```
4. **Validate your setup**:
   ```bash
   php validate_setup.php
   ```
5. **Test in browser** and then **remove setup files**:
   ```bash
   rm setup_wizard.php validate_setup.php test_db.php deploy.sh
   ```

## Manual Setup (Advanced)

### Prerequisites
- MariaDB/MySQL server running on your VPS
- PHP 7.4 or higher with PDO extension
- Web server (Apache/Nginx) with PHP support

### Step 1: Database Configuration

1. **Update database credentials** in `config/database.php`:
   ```php
   define('DB_HOST', 'your_vps_ip_or_localhost');
   define('DB_NAME', 'frogbytes_db');
   define('DB_USER', 'your_mariadb_username');
   define('DB_PASS', 'your_mariadb_password');
   ```

2. **Update site settings**:
   ```php
   define('SITE_URL', 'https://yourdomain.com');
   define('SMTP_FROM_EMAIL', 'noreply@yourdomain.com');
   ```

### Step 2: Database Setup

1. **Connect to your MariaDB server**:
   ```bash
   mysql -u root -p
   ```

2. **Run the setup script**:
   ```sql
   source /path/to/your/website/database/setup.sql
   ```
   
   Or copy and paste the contents of `database/setup.sql` into your MariaDB client.

## Step 3: File Permissions

Set proper permissions for your PHP files:
```bash
# Make sure web server can read PHP files
chmod 644 *.php
chmod 644 includes/*.php
chmod 644 config/*.php

# Secure the config directory
chmod 700 config/
```

## Step 4: Web Server Configuration

### Apache (.htaccess)
Create an `.htaccess` file in your web root:
```apache
RewriteEngine On

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"

# Hide PHP extensions in URLs (optional)
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^\.]+)$ $1.php [NC,L]

# Protect sensitive directories
<FilesMatch "^(config|includes|database)/">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### Nginx
Add to your server block:
```nginx
location ~ ^/(config|includes|database)/ {
    deny all;
    return 404;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

## Step 5: Testing

1. **Test database connection**:
   Visit `yourdomain.com/test_db.php` (create this file temporarily):
   ```php
   <?php
   require_once 'includes/Database.php';
   try {
       $db = Database::getInstance();
       echo "Database connection successful!";
   } catch (Exception $e) {
       echo "Database connection failed: " . $e->getMessage();
   }
   ?>
   ```

2. **Test user registration**:
   - Visit `yourdomain.com/signup.php`
   - Create a test account
   - Check if user appears in the database

3. **Test login**:
   - Visit `yourdomain.com/login.php`
   - Login with test account

## Step 6: Security Considerations

1. **Set DEBUG_MODE to false** in production:
   ```php
   define('DEBUG_MODE', false);
   ```

2. **Use HTTPS** for your website (required for secure sessions)

3. **Regularly update** your PHP version and dependencies

4. **Set up automated backups** for your database

5. **Monitor logs** for suspicious activity

## Email Configuration (Optional)

For password reset functionality to work, configure SMTP settings in `config/database.php`:

```php
define('SMTP_HOST', 'smtp.your-provider.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@domain.com');
define('SMTP_PASSWORD', 'your-email-password');
```

## Troubleshooting

### Common Issues:

1. **"Database connection failed"**:
   - Check MariaDB is running: `systemctl status mariadb`
   - Verify credentials in `config/database.php`
   - Check firewall settings

2. **"Permission denied"**:
   - Check file permissions
   - Ensure web server user can read files

3. **Sessions not working**:
   - Check PHP session configuration
   - Ensure session directory is writable

4. **Emails not sending**:
   - Check SMTP credentials
   - Verify your server can send emails
   - Check spam folders

## Database Maintenance

### Regular Cleanup (set up as cron jobs):

1. **Clean expired password reset tokens**:
   ```sql
   DELETE FROM password_resets WHERE expires_at < NOW() OR used = TRUE;
   ```

2. **Clean expired sessions**:
   ```sql
   DELETE FROM user_sessions WHERE expires_at < NOW();
   ```

### Backup Command:
```bash
mysqldump -u username -p frogbytes_db > backup_$(date +%Y%m%d_%H%M%S).sql
```

## Production Checklist

- [ ] Database credentials updated
- [ ] Site URL configured correctly
- [ ] DEBUG_MODE set to false
- [ ] File permissions set correctly
- [ ] HTTPS enabled
- [ ] Email configuration tested
- [ ] Backup system in place
- [ ] Log monitoring configured
- [ ] Security headers implemented
