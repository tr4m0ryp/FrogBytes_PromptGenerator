# FrogBytes - AI Prompt Helper with User Authentication

## 🤖 **Advanced AI Prompt Management & Enhancement Tool**

FrogBytes is a sophisticated web-based prompt management system designed to help users create, organize, and optimize prompts for AI interactions. It combines powerful prompt engineering capabilities with Google Gemini AI integration to automatically improve and refine your prompts for better AI responses.

### 🎯 **What Makes FrogBytes Special**

This isn't just another prompt storage tool - it's a comprehensive AI prompt engineering assistant that helps you:

- **Generate Clear, Professional Prompts** - Uses Google Gemini AI to automatically rewrite and improve your rough prompt ideas into clear, specific, and highly effective prompts
- **Combine Files with Context** - Drop multiple files (PDFs, text files, code files) and automatically combine them with your prompt for comprehensive AI analysis
- **Pre-Built Expert Prompts** - Includes a curated library of proven prompts for common tasks like code review, technical writing, system architecture, and more
- **Smart Prompt Organization** - Create custom categories and organize prompts with cloud synchronization across all your devices
- **Professional Prompt Templates** - Built-in templates for different professional roles (Senior Developer, System Architect, Technical Writer, etc.)

## ✨ **Core Features**

### 🧠 **AI-Powered Prompt Enhancement**
- **Google Gemini Integration** - Automatically transforms rough prompt ideas into professional, detailed prompts
- **Intelligent Rewriting** - Improves clarity, specificity, and effectiveness of your prompts
- **Context Preservation** - Maintains your original intent while making prompts more actionable
- **Professional Formatting** - Converts casual requests into structured, professional AI instructions

### 📁 **Smart File Processing**
- **Multi-Format Support** - Handle PDFs, text files, code files, and documents
- **Automatic Content Extraction** - PDF parsing and text extraction with proper formatting
- **File Combination** - Merge multiple files with your prompt for comprehensive context
- **Code Styling** - Automatically formats code snippets with proper syntax highlighting
- **Root File Detection** - Smart identification of main project files for better organization

### 🔐 **User Authentication & Cloud Sync**
- **Secure Registration & Login** - Argon2ID password hashing with email verification
- **Session Management** - "Remember me" functionality with secure token handling
- **Password Reset** - Email-based password recovery system
- **Cross-Device Synchronization** - Access your prompts from anywhere

### 📚 **Comprehensive Prompt Library**
- **Expert-Crafted Presets** - Pre-built prompts for common professional tasks:
  - **Code Reviewer** - Comprehensive code analysis and improvement suggestions
  - **Technical Writer** - Documentation generation with examples and API references
  - **System Architect** - Scalable system design recommendations
  - **Senior Python Backend Developer** - Framework selection and optimization guidance
  - **Senior React Frontend Developer** - Component architecture and performance optimization
  - **AI Safety Specialist** - Responsible AI implementation guidance
- **Custom Prompt Creation** - Build and organize your own prompt library
- **Category Organization** - Sort prompts by project, client, or use case
- **Import/Export** - Backup and share prompt collections

### 💾 **Advanced Storage & Sync**
- **Hybrid Storage System** - Local storage for offline use + cloud sync for accessibility
- **Automatic Synchronization** - Seamless sync across devices when logged in
- **Offline-First Design** - Full functionality without internet connection
- **Conflict Resolution** - Smart merging when the same prompt is edited on multiple devices
- **Backup & Recovery** - Automatic backups with version history

### 🎨 **Modern User Experience**
- **Responsive Design** - Optimized for desktop, tablet, and mobile devices
- **Dark/Light Theme** - Toggle between themes with system preference detection
- **Intuitive Interface** - Clean, professional design focused on productivity
- **Real-time Updates** - Instant feedback and live sync indicators
- **Progressive Web App** - Install on mobile devices for native app experience

## 🚀 **How It Works**

### 1. **Prompt Creation & Enhancement**
1. **Draft Your Idea** - Start with a rough prompt or idea
2. **AI Enhancement** - Click "Generate & Copy" to let Gemini AI improve your prompt
3. **Review & Refine** - The enhanced prompt appears in your text area for further editing
4. **Save & Organize** - Store in your personal library with custom categories

### 2. **File Integration**
1. **Drop Files** - Drag and drop PDFs, code files, or documents
2. **Automatic Processing** - Files are parsed and formatted automatically  
3. **Context Combination** - Your prompt + file contents = comprehensive AI input
4. **One-Click Copy** - Everything formatted and ready for your AI assistant

### 3. **Professional Templates**
1. **Select Expert Role** - Choose from preset professional prompts
2. **Customize Context** - Add your specific requirements or files
3. **Generate Results** - Get professional-quality AI responses
4. **Save Variations** - Store customized versions for future use

## 🎯 **Perfect For**

- **Software Developers** - Code reviews, documentation, architecture planning
- **Technical Writers** - API documentation, user guides, technical specifications  
- **Project Managers** - Requirements analysis, stakeholder communication
- **Data Scientists** - Data analysis prompts, model evaluation frameworks
- **Content Creators** - Structured content creation, SEO optimization
- **Consultants** - Client-specific prompt libraries, proposal generation
- **Researchers** - Literature analysis, survey design, report generation
- **Anyone** working with AI tools who wants better, more consistent results

## ⚡ **Quick Start Guide**

### **Method 1: Quick Setup (Recommended)**
1. **Upload files** to your web server (Apache/Nginx with PHP 7.4+)
2. **Run the setup wizard**: Visit `https://yourdomain.com/setup_wizard.php`
3. **Configure database** - The wizard will guide you through database setup
4. **Create your account** - Visit your site and register
5. **Add your Gemini API key** - Configure in the prompt manager for AI enhancement
6. **Start creating enhanced prompts!**

### **Method 2: Manual Setup**
1. **Database Setup** - Import `database/setup.sql` into your MariaDB/MySQL
2. **Configuration** - Edit `config/database.php` with your database credentials
3. **Permissions** - Ensure proper file permissions for security
4. **Testing** - Run `php validate_setup.php` to verify configuration

```
FrogBytes/
├── 🏠 **Core Application**
│   ├── index.php                    # Main app interface with AI integration
│   ├── script.js                    # File handling, Gemini API, UI logic
│   ├── preset-prompts.js           # Expert-crafted prompt library
│   └── manifest.json               # PWA configuration
│
├── 🔐 **Authentication System**  
│   ├── login.php                   # User login interface
│   ├── signup.php                  # Account registration
│   ├── forgot_password.php         # Password reset request
│   ├── reset_password.php          # Password reset form
│   ├── logout.php                  # Session termination
│   └── prompt-sync.js              # Client-server synchronization
│
├── 🔧 **Backend Services**
│   ├── includes/
│   │   ├── Database.php            # Database connection management
│   │   ├── User.php                # Authentication & session handling
│   │   ├── UserPrompts.php         # Prompt CRUD operations
│   │   └── EmailUtility.php        # Password reset emails
│   ├── api/
│   │   └── prompts.php             # RESTful API for prompt management
│   └── config/
│       └── database.php            # Application configuration
│
├── 🗄️ **Database & Setup**
│   ├── database/
│   │   └── setup.sql               # Complete database schema
│   ├── setup_wizard.php            # Interactive installation
│   ├── validate_setup.php          # Configuration validation
│   └── deploy.sh                   # Production deployment script
│
├── 📚 **Documentation**
│   ├── README.md                   # This comprehensive guide
│   ├── SETUP_GUIDE.md              # Detailed installation instructions
│   ├── IMPLEMENTATION_SUMMARY.md   # Technical implementation details
│   └── DEPLOYMENT_CHECKLIST.md     # Production deployment guide
│
└── 🎨 **Assets & Libraries**
    ├── ajax/libs/font-awesome/      # Icon library
    ├── npm/pdfjs-dist/             # PDF processing library
    └── s/inter/                    # Professional typography
```
│   └── setup.sql         # Database schema
├── prompt-sync.js        # Client-server sync
└── SETUP_GUIDE.md        # Detailed setup instructions
```

## 🔧 **Technology Stack & Architecture**

### **Frontend Technologies**
- **Modern JavaScript** - ES6+, async/await, modern DOM APIs
- **Responsive CSS3** - Custom CSS with CSS Grid and Flexbox
- **Progressive Web App** - Manifest file, service worker ready
- **PDF.js Integration** - Client-side PDF parsing and text extraction
- **Local Storage API** - Offline-first data persistence

### **Backend Architecture**
- **PHP 7.4+** - Modern PHP with type declarations and error handling
- **MariaDB/MySQL** - Optimized database schema with proper indexing
- **RESTful API** - Clean API endpoints for all CRUD operations
- **Object-Oriented Design** - Modular classes for User, Prompts, Database management

### **Security Features**
- **Argon2ID Password Hashing** - State-of-the-art password security
- **SQL Injection Prevention** - Prepared statements throughout
- **CSRF Protection** - Token-based request validation  
- **Session Security** - Secure session management with automatic cleanup
- **Input Validation** - Comprehensive sanitization and validation
- **File Security** - Proper permissions and access controls

### **AI Integration**
- **Google Gemini Pro API** - Advanced prompt enhancement and rewriting
- **Configurable Parameters** - Temperature, token limits, response tuning
- **Error Handling** - Graceful fallbacks and user feedback
- **Local API Key Storage** - Keys stored in browser, never on server

## 📁 **Project Structure**

## 🎯 **Use Cases & Examples**

### **For Software Developers**
```
Scenario: Code Review
1. Drop your code files into FrogBytes
2. Select "Code Reviewer" preset prompt
3. Click "Combine & Copy" 
4. Paste into ChatGPT/Claude for comprehensive code analysis

Result: Professional code review covering security, performance, 
        best practices, and specific improvement suggestions
```

### **For Technical Writers**
```
Scenario: API Documentation
1. Upload your API spec file or code
2. Select "Technical Writer" preset
3. Add context: "Create REST API documentation"
4. Generate enhanced prompt with Gemini AI
5. Get structured documentation with examples

Result: Complete API docs with usage examples, error codes,
        and developer-friendly explanations
```

### **For Project Managers**
```
Scenario: Requirements Analysis  
1. Upload project requirements documents
2. Create custom prompt: "Analyze requirements for gaps"
3. Use Gemini enhancement for clarity
4. Save as "Requirements Analyzer" for future projects

Result: Systematic requirements analysis template
        reusable across multiple projects
```

### **For Consultants**
```
Scenario: Client-Specific Prompts
1. Create client folder in prompt categories
2. Build library of client-specific prompts
3. Combine with project files for context
4. Sync across devices for client meetings

Result: Professional, consistent AI interactions
        tailored to each client's needs
```

## 🔍 **Advanced Features**

### **Smart File Processing**
- **PDF Text Extraction** - Automatically extracts text from PDF documents
- **Code Syntax Detection** - Recognizes programming languages and formats accordingly  
- **Multi-File Combining** - Merges multiple files with proper separation and labeling
- **Root File Priority** - First file uploaded becomes the primary context

### **Prompt Enhancement Engine**
- **Professional Rewriting** - Transforms casual requests into structured prompts
- **Context Preservation** - Maintains original intent while improving clarity
- **Specificity Boost** - Adds detailed requirements and expected output formats
- **Best Practice Integration** - Incorporates proven prompt engineering techniques

### **Collaboration Features**
- **Public Prompts** - Share useful prompts with the community (optional)
- **Export/Import** - Share prompt collections with team members
- **Category Organization** - Organize by client, project, or use case
- **Version History** - Track prompt evolution and improvements

## 🛠️ **Customization & Extension**

### **Adding Custom Preset Prompts**
Edit `preset-prompts.js` to add your own expert prompts:

```javascript
{
  id: 'your-custom-prompt',
  title: 'Your Custom Expert',
  content: `You are a [expert role] with expertise in [domain].
  
  Your responsibilities:
  • [Key responsibility 1]
  • [Key responsibility 2]
  • [Key responsibility 3]
  
  When I ask questions, respond as an experienced [role] would:
  be [trait 1], [trait 2], and [trait 3].
  
  My request:`
}
```

### **Custom CSS Themes**
Modify the CSS variables in `index.php` to create custom themes:

```css
:root {
  --bg-primary: #your-color;
  --text-primary: #your-color;
  --accent-primary: #your-color;
  /* ... other variables */
}
```

### **API Extensions**
Extend `api/prompts.php` to add custom functionality:
- Advanced search and filtering
- Prompt analytics and usage tracking
- Integration with other AI services
- Custom export formats

## 🔌 **API Reference**

### **Authentication Endpoints**
```
POST /login.php              # User authentication
POST /signup.php             # Account creation  
POST /forgot_password.php    # Password reset request
POST /reset_password.php     # Password reset confirmation
GET  /logout.php             # Session termination
```

### **Prompt Management API**
```
GET    /api/prompts.php      # Retrieve all user prompts
POST   /api/prompts.php      # Create new prompt
PUT    /api/prompts.php      # Update existing prompt  
DELETE /api/prompts.php?id=X # Delete specific prompt
POST   /api/prompts.php      # Bulk import from localStorage
```

### **Request/Response Examples**

**Create Prompt:**
```json
POST /api/prompts.php
{
  "title": "Code Reviewer",
  "content": "Review this code for bugs and improvements...",
  "category": "development",
  "is_public": false
}
```

**Response:**
```json
{
  "success": true,
  "message": "Prompt created successfully",
  "prompt": {
    "id": 123,
    "title": "Code Reviewer",
    "content": "Review this code for bugs...",
    "category": "development",
    "created_at": "2024-01-15 10:30:00"
  }
}
```

## 🗃️ **Database Schema**

### **Users Table** - Account Management
```sql
- id (Primary Key)           # Unique user identifier
- username                   # User's chosen username  
- email                      # Email for login and recovery
- password                   # Argon2ID hashed password
- is_verified               # Email verification status
- verification_token        # Email verification token
- created_at, updated_at    # Timestamp tracking
```

### **User Prompts Table** - Prompt Storage
```sql
- id (Primary Key)          # Unique prompt identifier
- user_id (Foreign Key)     # Links to users table
- title                     # Prompt display name
- content                   # Full prompt text
- category                  # Organization category
- is_public                 # Sharing permission
- created_at, updated_at    # Version tracking
```

### **Password Resets Table** - Recovery System
```sql
- id (Primary Key)          # Reset request ID
- user_id (Foreign Key)     # User requesting reset
- token                     # Secure reset token
- expires_at               # Token expiration
- used_at                  # Usage tracking
```

### **User Sessions Table** - Session Management  
```sql
- id (Primary Key)          # Session identifier
- user_id (Foreign Key)     # Session owner
- session_token            # Secure session token
- ip_address               # Security tracking
- user_agent              # Device identification
- expires_at              # Auto-cleanup
```

## ⚙️ **Configuration Guide**

### **Basic Configuration**
All configuration is centralized in `config/database.php`:

```php
<?php
// Database Configuration
define('DB_HOST', 'localhost');           # Database server
define('DB_NAME', 'frogbytes_prompts');   # Database name
define('DB_USER', 'your_db_username');    # Database user
define('DB_PASS', 'your_secure_password'); # Database password

// Application Settings
define('SITE_URL', 'https://yourdomain.com');    # Your website URL
define('SITE_NAME', 'FrogBytes Prompt Helper');   # Application name
define('DEBUG_MODE', false);                      # Set to true for development

// Email Configuration (for password resets)
define('SMTP_HOST', 'smtp.yourdomain.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'noreply@yourdomain.com');
define('SMTP_PASSWORD', 'your_email_password');
define('SMTP_FROM_EMAIL', 'noreply@yourdomain.com');
define('SMTP_FROM_NAME', 'FrogBytes System');

// Security Settings
define('SESSION_LIFETIME', 86400);        # 24 hours in seconds
define('REMEMBER_ME_LIFETIME', 2592000);  # 30 days in seconds
define('PASSWORD_RESET_EXPIRY', 3600);    # 1 hour in seconds
?>
```

### **Google Gemini API Setup**
1. Visit [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Create a new API key for Gemini Pro
3. In FrogBytes, go to "Manage Your Custom Prompts" section
4. Enter your API key in the "Gemini API Configuration" field
5. Click "Save API Key" - it's stored locally in your browser for security

### **Environment Variables (Optional)**
For enhanced security, you can use environment variables:
```bash
# Database
export DB_HOST="localhost"
export DB_NAME="frogbytes_prompts"
export DB_USER="your_username"
export DB_PASS="your_password"

# Application
export SITE_URL="https://yourdomain.com"
export DEBUG_MODE="false"
```

## 🚀 **Production Deployment**

### **Automated Deployment**
```bash
# Run the deployment script
./deploy.sh

# Or manually execute each step:
chmod +x deploy.sh
bash deploy.sh
```

### **Manual Deployment Checklist**
1. **Server Requirements**
   - PHP 7.4+ with extensions: PDO, PDO_MySQL, mbstring, openssl
   - MariaDB 10.3+ or MySQL 5.7+
   - Apache/Nginx with mod_rewrite enabled
   - SSL certificate configured

2. **Database Setup**
   ```sql
   CREATE DATABASE frogbytes_prompts CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
   Import the schema: `mysql -u username -p frogbytes_prompts < database/setup.sql`

3. **File Permissions**
   ```bash
   # Set secure permissions
   chmod 644 *.php *.js *.css *.html
   chmod 755 directories/
   chmod 600 config/database.php
   chmod 700 includes/
   ```

4. **Security Headers** (Apache `.htaccess`)
   ```apache
   # Security Headers
   Header always set X-Content-Type-Options nosniff
   Header always set X-Frame-Options DENY
   Header always set X-XSS-Protection "1; mode=block"
   Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
   Header always set Content-Security-Policy "default-src 'self'"
   
   # Hide sensitive files
   <Files ~ "^\.">
       Require all denied
   </Files>
   
   <Files ~ "\.(sql|log|md)$">
       Require all denied
   </Files>
   ```

5. **Validation & Testing**
   ```bash
   # Validate configuration
   php validate_setup.php
   
   # Test database connection
   php test_db.php
   ```

### **Post-Deployment Security**
- [ ] Remove or secure setup files: `setup_wizard.php`, `validate_setup.php`, `test_db.php`
- [ ] Set up regular database backups
- [ ] Configure log rotation for error logs
- [ ] Enable firewall rules for database access
- [ ] Set up monitoring and alerting
- [ ] Configure automated security updates

### **Performance Optimization**
- [ ] Enable PHP OPCache
- [ ] Configure MySQL query cache
- [ ] Set up Redis for session storage (optional)
- [ ] Enable gzip compression
- [ ] Configure CDN for static assets (optional)

## 🐛 **Troubleshooting & Support**

### **Common Issues & Solutions**

**Issue: "Database connection failed"**
```bash
# Check database credentials in config/database.php
# Verify database server is running
# Test connection with:
php test_db.php
```

**Issue: "Gemini API not working"**
```
1. Verify API key is correctly entered
2. Check Google AI Studio for API key status
3. Ensure you have Gemini Pro API access
4. Check browser console for error messages
```

**Issue: "Prompts not syncing"**  
```
1. Check if user is logged in
2. Verify network connection
3. Check browser console for sync errors
4. Try manual sync with browser refresh
```

**Issue: "File upload not working"**
```
1. Check file size limits in PHP settings
2. Verify file permissions on server
3. Ensure PDF.js library is loading correctly
4. Check browser console for JavaScript errors
```

### **Debug Mode**
Enable debug mode in `config/database.php`:
```php
define('DEBUG_MODE', true);
```

This provides detailed error messages and logging for troubleshooting.

### **Log Files**
Check these locations for error logs:
- Web server error log (Apache/Nginx)
- PHP error log (`php_errors.log`)
- Browser console (F12 Developer Tools)
- Database error logs

### **Validation Tools**
```bash
# Validate complete setup
php validate_setup.php

# Test database connectivity  
php test_db.php

# Check file permissions
ls -la config/
ls -la includes/
```

## 📞 **Getting Help**

### **Documentation Resources**
- `README.md` - This comprehensive guide
- `SETUP_GUIDE.md` - Detailed installation instructions
- `IMPLEMENTATION_SUMMARY.md` - Technical architecture details
- `DEPLOYMENT_CHECKLIST.md` - Production deployment guide

### **Self-Help Steps**
1. Check the setup guide for detailed installation steps
2. Run validation tools to identify configuration issues
3. Check error logs for specific error messages
4. Verify all dependencies and requirements are met
5. Test with a fresh browser session to rule out cache issues

### **System Requirements**
- **Server**: Apache 2.4+ or Nginx 1.16+
- **PHP**: Version 7.4+ with extensions: PDO, PDO_MySQL, mbstring, openssl
- **Database**: MariaDB 10.3+ or MySQL 5.7+
- **Browser**: Modern browser with JavaScript enabled
- **Storage**: Minimum 50MB for application files

## 🏆 **Why Choose FrogBytes?**

### **Compared to Simple Prompt Storage**
- ✅ **AI-Powered Enhancement** - Automatically improves your prompts
- ✅ **File Integration** - Combine documents with prompts seamlessly  
- ✅ **Expert Templates** - Professional-grade preset prompts
- ✅ **Cloud Synchronization** - Access from anywhere
- ✅ **Offline Capability** - Works without internet

### **Compared to Basic AI Tools**
- ✅ **Structured Workflow** - Organized approach to prompt engineering
- ✅ **Reusable Libraries** - Build your prompt knowledge base
- ✅ **Professional Focus** - Designed for business and development use
- ✅ **Privacy Focused** - Your prompts stay under your control
- ✅ **Team Collaboration** - Share and organize prompts across teams

### **Return on Investment**
- **Time Savings** - Reduce prompt creation time by 70%
- **Quality Improvement** - Get better AI responses with enhanced prompts
- **Consistency** - Standardize AI interactions across your organization
- **Knowledge Retention** - Build institutional knowledge in prompt libraries
- **Productivity Boost** - Streamlined workflow for AI-assisted tasks

## 📄 **License & Legal**

This project is open source and available under the **MIT License**.

### **MIT License**
```
Copyright (c) 2024 FrogBytes

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
```

### **Third-Party Dependencies**
- **PDF.js** - Mozilla Public License 2.0
- **Font Awesome** - Font Awesome Free License  
- **Inter Font** - SIL Open Font License
- **Google Gemini API** - Subject to Google's terms of service

### **Privacy & Data Handling**
- **User Data** - Stored securely with encrypted passwords
- **Prompts** - Stored in your database, under your control
- **API Keys** - Stored locally in browser, never transmitted to our servers
- **No Tracking** - No analytics or tracking scripts included
- **GDPR Compliant** - Tools provided for data export and deletion

---

## 🚀 **Get Started Today!**

Transform your AI interactions with professional prompt engineering. Deploy FrogBytes in minutes and start creating better, more effective prompts immediately.

**Ready to enhance your AI workflow?** Upload the files to your server and run the setup wizard to get started!

---

*Built with ❤️ for developers, writers, and AI enthusiasts who want better results from their AI tools.*
