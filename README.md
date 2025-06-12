# FrogBytes - AI Prompt Helper with User Authentication

A sophisticated prompt management system for AI interactions with full user authentication and cloud synchronization.

## Features

### 🔐 User Authentication
- **Secure Registration & Login** - Argon2ID password hashing
- **Session Management** - "Remember me" functionality  
- **Password Reset** - Email-based password recovery
- **Account Verification** - Email verification for new accounts

### 💾 Prompt Management
- **Local & Cloud Storage** - Prompts sync across devices
- **Custom Categories** - Organize prompts by category
- **Import/Export** - Backup and transfer prompts
- **Smart Sync** - Automatic sync when logged in
- **Offline Mode** - Works without internet connection

### 🎨 Modern Interface
- **Responsive Design** - Works on desktop and mobile
- **Dark/Light Theme** - Toggle between themes
- **Intuitive UI** - Easy-to-use interface
- **Real-time Updates** - Instant feedback

## Quick Start

1. **Upload files** to your web server
2. **Run setup**: `php setup_wizard.php`
3. **Visit your site** and create an account
4. **Start managing prompts!**

## File Structure

```
├── index.php              # Main application
├── login.php             # User login page
├── signup.php            # User registration
├── forgot_password.php   # Password reset request
├── reset_password.php    # Password reset form
├── logout.php            # Logout handler
├── api/
│   └── prompts.php       # REST API for prompts
├── includes/
│   ├── Database.php      # Database connection
│   ├── User.php          # User management
│   ├── UserPrompts.php   # Prompt CRUD operations
│   └── EmailUtility.php  # Email functionality
├── config/
│   └── database.php      # Configuration file
├── database/
│   └── setup.sql         # Database schema
├── prompt-sync.js        # Client-server sync
└── SETUP_GUIDE.md        # Detailed setup instructions
```

## Technology Stack

- **Backend**: PHP 7.4+, MariaDB/MySQL
- **Frontend**: Vanilla JavaScript, CSS3
- **Security**: Argon2ID hashing, CSRF protection, SQL injection prevention
- **Database**: MariaDB with proper indexing and foreign keys

## Security Features

- ✅ Password hashing with Argon2ID
- ✅ SQL injection prevention with prepared statements
- ✅ CSRF token protection
- ✅ Session management with secure tokens
- ✅ Input validation and sanitization
- ✅ File permission security
- ✅ HTTP security headers

## API Endpoints

### User Authentication
- `POST /login.php` - User login
- `POST /signup.php` - User registration
- `POST /forgot_password.php` - Password reset request
- `POST /reset_password.php` - Password reset

### Prompt Management
- `GET /api/prompts.php` - Get user prompts
- `POST /api/prompts.php` - Create new prompt
- `PUT /api/prompts.php` - Update prompt
- `DELETE /api/prompts.php` - Delete prompt

## Database Schema

### Users Table
- User accounts with email and hashed passwords
- Email verification and account status
- Creation and update timestamps

### User Prompts Table
- Custom prompts linked to user accounts
- Category organization and public/private settings
- Full-text search capabilities

### Password Resets Table
- Secure password reset tokens
- Expiration and usage tracking

### User Sessions Table
- Session management with tokens
- IP and user agent tracking
- Automatic cleanup of expired sessions

## Configuration

All configuration is handled in `config/database.php`:

```php
// Database settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'frogbytes_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

// Site settings
define('SITE_URL', 'https://yourdomain.com');
define('DEBUG_MODE', false);

// Email settings (optional)
define('SMTP_HOST', 'smtp.yourdomain.com');
// ... other SMTP settings
```

## Production Deployment

1. **Run deployment script**: `./deploy.sh`
2. **Configure database**: Update `config/database.php`
3. **Set up HTTPS**: Configure SSL certificate
4. **Enable security headers**: Configure web server
5. **Set up backups**: Regular database backups
6. **Monitor logs**: Set up log monitoring

## Support

For issues or questions:
1. Check `SETUP_GUIDE.md` for detailed instructions
2. Run `php validate_setup.php` to check configuration
3. Check web server error logs
4. Verify database connection and permissions

## License

This project is open source and available under the MIT License.
