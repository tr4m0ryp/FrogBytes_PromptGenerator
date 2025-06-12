# FrogBytes Production Deployment Checklist

## Pre-Deployment
- [ ] **VPS Requirements Met**
  - [ ] PHP 7.4+ installed with required extensions (PDO, PDO_MySQL, mbstring, openssl)
  - [ ] MariaDB/MySQL server running
  - [ ] Web server configured (Apache/Nginx)
  - [ ] HTTPS certificate installed
  - [ ] Domain pointing to server

- [ ] **Files Uploaded**
  - [ ] All PHP files uploaded to web root
  - [ ] File permissions not yet set (will be handled by deploy script)
  - [ ] Database setup files included

## Deployment Process

### Step 1: Initial Setup
```bash
# Make deployment script executable
chmod +x deploy.sh

# Run deployment script
./deploy.sh
```

### Step 2: Configuration
```bash
# Run interactive setup wizard
php setup_wizard.php
```

The wizard will prompt for:
- Database host, name, username, password
- Site URL (with https://)
- SMTP settings (optional)

### Step 3: Validation
```bash
# Validate configuration
php validate_setup.php
```

### Step 4: Testing
- [ ] **Database Connection**: Visit `/test_db.php`
- [ ] **User Registration**: Visit `/signup.php` and create account
- [ ] **User Login**: Test login at `/login.php`
- [ ] **Prompt Functionality**: Test creating/saving prompts
- [ ] **Password Reset**: Test forgot password (if SMTP configured)

### Step 5: Security Cleanup
```bash
# Remove setup and test files
rm setup_wizard.php
rm validate_setup.php  
rm test_db.php
rm deploy.sh
```

## Post-Deployment Security

### File Permissions Verification
```bash
# Config files should be secure
ls -la config/
# Should show: drwx------ (700) for directory
# Should show: -rw------- (600) for database.php

# Web files should be readable
ls -la *.php
# Should show: -rw-r--r-- (644)
```

### Database Security
- [ ] **Regular Backups Scheduled**
  ```bash
  # Add to crontab
  0 2 * * * mysqldump -u username -p'password' frogbytes_db > /backup/frogbytes_$(date +\%Y\%m\%d).sql
  ```

- [ ] **Cleanup Jobs Scheduled**
  ```bash
  # Add to crontab - clean expired tokens daily
  0 3 * * * mysql -u username -p'password' frogbytes_db -e "DELETE FROM password_resets WHERE expires_at < NOW() OR used = TRUE; DELETE FROM user_sessions WHERE expires_at < NOW();"
  ```

### Web Server Security

#### Apache (.htaccess already created)
- [ ] **Security headers active**
- [ ] **Sensitive directories protected**
- [ ] **File extensions blocked**

#### Nginx (if using Nginx)
Add to server block:
```nginx
# Security headers
add_header X-Content-Type-Options nosniff;
add_header X-Frame-Options DENY;
add_header X-XSS-Protection "1; mode=block";
add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload";

# Protect sensitive directories
location ~ ^/(config|includes|database)/ {
    deny all;
    return 404;
}

# Block access to sensitive files
location ~ \.(sql|env|log)$ {
    deny all;
    return 404;
}
```

## Final Verification

### Functionality Checklist
- [ ] **Main Site Loads**: Visit homepage
- [ ] **User Registration Works**: Create test account
- [ ] **Email Verification**: Check if verification email sent (if configured)
- [ ] **User Login Works**: Login with test account
- [ ] **Prompt Creation**: Create and save a custom prompt
- [ ] **Prompt Sync**: Verify prompts persist after logout/login
- [ ] **Theme Toggle**: Test dark/light theme switching
- [ ] **Mobile Responsive**: Test on mobile device
- [ ] **Password Reset**: Test forgot password flow (if SMTP configured)

### Security Verification
- [ ] **HTTPS Working**: All pages load over HTTPS
- [ ] **Setup Files Removed**: No setup files accessible
- [ ] **Config Protected**: Cannot access `/config/database.php` via browser
- [ ] **Database Protected**: Cannot access `.sql` files via browser
- [ ] **Debug Mode Off**: No error messages visible to users
- [ ] **Session Security**: Sessions work correctly, logout works

### Performance & Monitoring
- [ ] **Database Performance**: Check query performance
- [ ] **Log Monitoring**: Set up log monitoring
- [ ] **Uptime Monitoring**: Set up uptime checks
- [ ] **SSL Certificate Monitoring**: Monitor certificate expiration

## Maintenance

### Regular Tasks
- **Daily**: Monitor logs for errors
- **Weekly**: Check database size and performance
- **Monthly**: Update PHP and server packages
- **Quarterly**: Review and rotate secrets/tokens

### Backup Strategy
- **Database**: Daily automated backups
- **Files**: Weekly full site backup
- **Offsite**: Copy backups to separate location

### Updates
- Keep PHP updated
- Monitor for security updates
- Test updates in staging environment first

## Troubleshooting

### Common Issues
1. **"Database connection failed"**
   - Check MariaDB status: `systemctl status mariadb`
   - Verify credentials in config/database.php
   - Check database exists and user has permissions

2. **"Permission denied" errors**
   - Re-run file permission setup from deploy.sh
   - Check web server user permissions

3. **Sessions not persisting**
   - Check PHP session configuration
   - Verify session directory is writable
   - Check session cleanup isn't running too frequently

4. **Emails not sending**
   - Verify SMTP credentials
   - Check firewall allows SMTP traffic
   - Test with simple mail script

### Getting Help
- Check server error logs: `/var/log/apache2/error.log` or `/var/log/nginx/error.log`
- Check PHP error logs: `/var/log/php_errors.log`
- Enable debug mode temporarily in config/database.php
- Verify all requirements with `php validate_setup.php`

---

## ✅ Deployment Complete

Once all items are checked off, your FrogBytes installation is ready for production use!

Remember to:
- Keep regular backups
- Monitor server health
- Update dependencies regularly
- Review security settings periodically
