# Event Management System (EMS)

A comprehensive web-based Event Management System built with PHP, MySQL, and Bootstrap. This system allows users to create, manage, and participate in events with role-based access control.

**GitHub Repository:** [Event-Mangement-System-WebTech](https://github.com/sajib91/Event-Mangement-System-WebTech.git)


### User Features
- **User Authentication**
  - User registration with email validation
  - Secure login with password hashing
  - Account recovery/password reset
  - Role-based access control

- **Event Management**
  - Create and request events
  - View ongoing, pending, approved, and archived events
  - Event calendar view
  - Event details with images
  - Event search functionality
  - Like/dislike events
  - Comment on events

- **User Profile**
  - View and manage user profile
  - Upload profile picture
  - View personal event history
  - Track event requests

### Admin Features
- **Dashboard**
  - View system statistics
  - User and event analytics
  - Engagement metrics

- **User Management**
  - View all users
  - Manage user roles and permissions
  - Suspend/activate users
  - Assign permissions for event requests and reviews

- **Event Management**
  - Approve/deny event requests
  - Manage event status
  - View event analytics
  - Delete events if needed

- **Comment Management**
  - Review and moderate comments
  - Delete inappropriate comments

- **Database Management**
  - Backup database
  - Restore from backup

### Additional Features
- **Responsive Design** - Works on desktop, tablet, and mobile
- **Real-time Updates** - Background worker processes
- **Input Sanitization** - Protection against XSS attacks
- **Session Management** - Secure session handling
- **Pagination** - Efficient data display
- **File Upload** - Support for profile pictures and event images

## 🖼️ Screenshots

The application features a modern, clean UI with:
- Blue and green gradient design
- Custom-styled forms and buttons
- Bootstrap-based responsive layout
- FontAwesome icons
- Professional typography using Poppins font

## 🛠️ Technology Stack

### Backend
- **Language:** PHP 7.3+
- **Database:** MySQL/MariaDB
- **ORM:** PDO (PHP Data Objects)

### Frontend
- **Framework:** Bootstrap 5
- **CSS:** Custom CSS + Bootstrap utilities
- **Icons:** FontAwesome 6 + Bootstrap Icons
- **JavaScript:** jQuery, Popper.js, Bootstrap JS
- **Calendar:** ML Calendar

### Additional Libraries
- **PHPMailer:** Email functionality
- **SendGrid:** Email delivery service
- **Dotenv:** Environment variable management
- **Composer:** Dependency management

## 📦 Requirements

### Server Requirements
- PHP 7.3 or higher
- MySQL 5.7 or MariaDB 10.0+
- Apache/Nginx web server
- Composer

### System Requirements
- 2GB RAM minimum (4GB recommended)
- 500MB storage space
- Modern web browser

### PHP Extensions Required
- `mysqli` or `mysql`
- `pdo`
- `json`
- `curl`
- `openssl`
- `mbstring`
- `fileinfo`

## 📥 Installation

### Step 1: Clone or Download the Project
```bash
# Navigate to your web root
cd /Applications/XAMPP/xamppfiles/htdocs

# Clone the repository (if using git)
git clone https://github.com/sajib91/Event-Mangement-System-WebTech.git EMS
cd EMS
```

### Step 2: Install Dependencies
```bash
# Install Composer dependencies
composer install
```

### Step 3: Configure Database
```bash
# Copy the database connection settings
cp PARTS/db_connection_settings.php.example PARTS/db_connection_settings.php

# Edit the file with your database credentials
nano PARTS/db_connection_settings.php
```

### Step 4: Setup Database
```bash
# Run the database setup script
php database_setup.php

# Or import the SQL file manually through phpMyAdmin
```

### Step 5: Configure Environment
Create a `.env` file in the project root:
```
DB_HOST=localhost
DB_NAME=event_management_system
DB_USER=root
DB_PASS=password
SENDGRID_API_KEY=your_sendgrid_key
```

### Step 6: Create Directories
```bash
# Ensure necessary directories exist and are writable
mkdir -p UPLOADS/img/EVENTS
chmod -R 755 UPLOADS/
chmod -R 755 ASSETS/event-image/
```

### Step 7: Access the Application
Open your browser and navigate to:
```
http://localhost/EMS/
```

## 📁 Project Structure

```
EMS/
├── index.php                          # Main entry point
├── database_setup.php                 # Database initialization
├── composer.json                      # Composer dependencies
├── README.md                          # This file
├── PATH_CONFIGURATION_GUIDE.md        # Path configuration documentation
├── PATH_CONFIGURATION_QUICK_REFERENCE.md
│
├── PARTS/                             # Core application files
│   ├── path-config.php                # Centralized path configuration
│   ├── config.php                     # Main configuration file
│   ├── db_connection_settings.php     # Database connection
│   ├── header.php                     # Site header/navigation
│   ├── footer.php                     # Site footer
│   ├── CSS.php                        # CSS loader
│   ├── JS.php                         # JavaScript loader
│   ├── sanitize_input.php             # Input sanitization
│   ├── background_worker.php          # Background processes
│   └── ...                            # Other utilities
│
├── ASSETS/                            # Static assets
│   ├── CSS/                           # Stylesheets
│   │   ├── bootstrap.min.css          # Bootstrap framework
│   │   ├── FA-all.min.css             # FontAwesome icons
│   │   ├── custom_design.css          # Custom styles
│   │   └── ...
│   ├── JS/                            # JavaScript files
│   │   ├── bootstrap.min.js
│   │   ├── jquery.slim.min.js
│   │   ├── FA-all.js
│   │   ├── custom_script.js
│   │   └── ...
│   ├── IMG/                           # Images and icons
│   ├── FONTS/                         # Font files
│   ├── event-image/                   # Event images
│   └── webfonts/                      # Web fonts
│
├── EMS/                               # User-facing pages
│   ├── login.php                      # Login page
│   ├── register.php                   # Registration page
│   ├── profile.php                    # User profile
│   ├── calendar.php                   # Event calendar
│   ├── search_event.php               # Event search
│   ├── events_pending.php             # Pending events
│   ├── events_approved.php            # Approved events
│   ├── events_ongoing.php             # Ongoing events
│   ├── events_archived.php            # Archived events
│   ├── event_details.php              # Event details page
│   ├── recover_account.php            # Account recovery
│   ├── suspended.php                  # Suspended account page
│   └── ...
│
├── ADMIN/                             # Admin panel pages
│   ├── administrator.php              # Admin dashboard
│   ├── manage_users.php               # User management
│   ├── manage_events.php              # Event management
│   ├── manage_comments.php            # Comment moderation
│   ├── manage_database.php            # Database backup/restore
│   └── ...
│
├── USER/                              # User utilities
│   ├── request_event.php              # Event request form
│   ├── view_my_requests.php           # View user requests
│   ├── verify_database.php            # Database verification
│   ├── test_diagnostic.php            # Diagnostic tools
│   └── ...
│
├── UPLOADS/                           # User uploads
│   └── img/
│       └── EVENTS/                    # Event images
│
├── SVG/                               # SVG assets
│
├── vendor/                            # Composer dependencies
│   └── ...
│
└── .gitignore                         # Git ignore rules
```

## 🗄️ Database Setup

### Automatic Setup
The system includes an automatic database setup:
```bash
php database_setup.php
```

### Manual Setup
If automatic setup fails, import the SQL manually:

1. Open phpMyAdmin
2. Create a new database: `event_management_system`
3. Import the SQL file (if provided)
4. Or use the database creation script in `database_setup.php`

### Database Tables

The system creates the following tables:
- `users` - User accounts
- `events` - Event listings
- `comments` - Event comments
- `comment_votes` - Comment likes/dislikes
- `user_permissions` - Role-based permissions
- `event_requests` - Event request log

## ⚙️ Configuration

### Database Configuration
Edit `PARTS/db_connection_settings.php`:
```php
<?php
$host = 'localhost';
$dbname = 'event_management_system';
$username = 'root';
$password = '';
?>
```

### Path Configuration
All paths are centralized in `PARTS/path-config.php`. This file defines:
- Project root directory
- Asset directories
- Configuration file paths

For detailed documentation, see:
- [PATH_CONFIGURATION_GUIDE.md](PATH_CONFIGURATION_GUIDE.md)
- [PATH_CONFIGURATION_QUICK_REFERENCE.md](PATH_CONFIGURATION_QUICK_REFERENCE.md)

### Email Configuration
Update SendGrid API key in environment variables or `PARTS/config.php` for email functionality.

## 💡 Usage

### For Regular Users

1. **Register an Account**
   - Go to `/EMS/register.php`
   - Fill in your details
   - Create password
   - Account created successfully

2. **Login**
   - Navigate to `/EMS/login.php`
   - Enter username and password
   - Redirected to dashboard

3. **View Events**
   - Browse ongoing, pending, approved events
   - Use search to find specific events
   - View event details and comments

4. **Request an Event**
   - Go to `/USER/request_event.php`
   - Fill event details
   - Submit request
   - Admin reviews and approves

5. **Manage Profile**
   - Access `/EMS/profile.php`
   - Update profile information
   - Upload profile picture

### For Admin Users

1. **Access Admin Panel**
   - Login as admin user
   - Go to `/ADMIN/administrator.php`

2. **Manage Users**
   - View all users
   - Edit user roles
   - Suspend/activate accounts
   - Assign permissions

3. **Manage Events**
   - View all event requests
   - Approve or deny requests
   - Change event status
   - Delete events if necessary

4. **Moderate Comments**
   - Review comments
   - Delete inappropriate content

5. **Database Management**
   - Backup database
   - Restore from backup

## 👥 User Roles

### Admin
- Full system access
- Manage users and events
- Moderate comments
- Database management
- Can review event requests
- Can delete users and events

### Regular User
- Create event requests
- View events
- Comment on events
- Like/dislike events
- Manage own profile
- View own event history

### Guest
- View public events (if enabled)
- View event details
- Cannot create events or comments
- Cannot like/dislike

## 🔌 API Endpoints

The system uses standard HTTP requests. Key endpoints:

### Authentication
- `POST /EMS/login.php` - User login
- `POST /EMS/register.php` - User registration
- `POST /EMS/recover_account.php` - Password recovery

### Events
- `GET /index.php` - View all events
- `GET /EMS/events_pending.php` - View pending events
- `GET /EMS/events_approved.php` - View approved events
- `GET /EMS/events_ongoing.php` - View ongoing events
- `POST /USER/request_event.php` - Create event request

### Admin
- `GET /ADMIN/administrator.php` - Admin dashboard
- `POST /ADMIN/manage_users.php` - Update users
- `POST /ADMIN/manage_events.php` - Update events

## 📍 Path Configuration

This project uses a **centralized relative path configuration** for improved maintainability:

### Key Constants
All paths are defined as constants in `PARTS/path-config.php`:
- `PROJECT_ROOT` - Main project directory
- `CONFIG_PATH` - Configuration file
- `ASSETS_DIR` - Static assets
- `PARTS_DIR` - Core components

### Auto Path Detection
The system automatically detects:
- Root level pages
- Subdirectory pages
- Correct asset paths for each location

See [PATH_CONFIGURATION_GUIDE.md](PATH_CONFIGURATION_GUIDE.md) for detailed information.

## 🐛 Troubleshooting

### Common Issues

#### CSS/Images Not Loading
1. Check file permissions
2. Verify ASSETS directory exists
3. Clear browser cache
4. Check browser console for errors

#### Database Connection Error
1. Verify database credentials in `db_connection_settings.php`
2. Ensure MySQL is running
3. Check database exists and is accessible
4. Verify permissions

#### Login Issues
1. Check session configuration
2. Verify cookies are enabled
3. Clear browser cookies and try again
4. Check user account is not suspended

#### File Upload Issues
1. Check UPLOADS directory permissions
2. Verify disk space available
3. Check file size limits in PHP
4. Ensure file type is allowed

#### Email Not Sending
1. Verify SendGrid API key
2. Check internet connection
3. Verify email configuration
4. Check spam folder

### Debug Mode
Enable debug logging by uncommenting in `PARTS/config.php`:
```php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
```

### Log Files
Check logs in:
- `UPLOADS/logs/` (if created)
- Browser console (F12)
- Server error logs

## 👨‍💼 Contributors

- **Md. Khayrul Islam Sajib** [GitHub](https://github.com/sajib91)
- **Montakim Talukdar Dukhu** [GitHub](https://github.com/Montakim-IIT-DU)

## 📜 License

This project is provided as-is for educational and development purposes. 

## 📧 Contact

For support and inquiries:
- **Email:** bsse1552@iit.du.ac.bd
- **Email:** bsse1512@iit.du.ac.bd
- **GitHub:** [sajib91](https://github.com/sajib91), [Montakim-IIT-DU](https://github.com/Montakim-IIT-DU)

## 🔗 Useful Links

- [Bootstrap Documentation](https://getbootstrap.com/docs)
- [FontAwesome Icons](https://fontawesome.com/icons)
- [PHP Documentation](https://www.php.net/manual)
- [MySQL Documentation](https://dev.mysql.com/doc)
- [jQuery Documentation](https://jquery.com)

## 📝 Version History

- **v1.0** (March 2026) - Initial release
  - Core event management functionality
  - User authentication and roles
  - Admin panel
  - Path configuration system
  - Database backup/restore

## 🚀 Future Enhancements

Planned features for future releases:
- [ ] Advanced event filtering
- [ ] User notifications
- [ ] Event registration limits
- [ ] Payment integration
- [ ] API endpoints for mobile app
- [ ] Real-time notifications
- [ ] Advanced analytics
- [ ] Event categories/tags
- [ ] Recurring events
- [ ] Event templates

## ⚠️ Important Notes

1. **Security:** Always use HTTPS in production
2. **Backups:** Regularly backup your database
3. **Updates:** Keep dependencies updated
4. **Permissions:** Ensure proper file permissions
5. **Environment:** Never commit `.env` file to version control

## 🆘 Support

For issues, bugs, or feature requests:
1. Check the troubleshooting section above
2. Review PATH_CONFIGURATION_GUIDE.md for path-related issues
3. Check browser console for JavaScript errors
4. Review server error logs
5. Contact the developers

---

**Last Updated:** March 6, 2026  

