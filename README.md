# APPOLIOS - E-Learning Platform

A modern, professional e-learning platform built with PHP MVC architecture and MySQL database.

## Features

- **User Authentication**: Secure login/register system with PHP sessions
- **Role-Based Access**: Admin and Student dashboards
- **Course Management**: Add, edit, delete courses (Admin)
- **Course Enrollment**: Students can enroll in courses
- **Progress Tracking**: Track student learning progress
- **Responsive Design**: Mobile, tablet, and desktop friendly
- **Modern UI**: Clean design with animations

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for URL routing)

## Installation

### 1. Copy Files
Copy the `APPOLIOS` folder to your web server directory (e.g., `htdocs` or `www`).

### 2. Create Database
Import the database schema:
```sql
-- Open phpMyAdmin or MySQL CLI
-- Import the file: database/appolios_db.sql
```

Or run:
```bash
mysql -u root -p < database/appolios_db.sql
```

### 3. Configure Database
Edit `config/config.php` and update the database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'appolios_db');
define('DB_USER', 'root');      // Your MySQL username
define('DB_PASS', '');           // Your MySQL password
```

### 4. Configure Web Server

#### For Apache (.htaccess included)
Make sure `mod_rewrite` is enabled:
```bash
sudo a2enmod rewrite
sudo service apache2 restart
```

#### For Nginx
Add this to your server config:
```nginx
location /projetWeb/APPOLIOS {
    try_files $uri $uri/ /projetWeb/APPOLIOS/public/index.php?url=$uri&$args;
}
```

### 5. Set Base URL
Update `APP_URL` in `config/config.php`:
```php
define('APP_URL', 'http://localhost/projetWeb/APPOLIOS');
```

### 6. Access the Application
Open your browser and navigate to:
```
http://localhost/projetWeb/APPOLIOS
```

## Default Accounts

### Admin Account
- **Email**: admin@appolios.com
- **Password**: admin123

### Student Account
- **Email**: student@appolios.com
- **Password**: student123

## Troubleshooting Login

If default login shows "Invalid email or password", your existing DB likely has old password hashes.

Run once from project root:

```bash
php fix_passwords.php
```

Then login with:
- admin@appolios.com / admin123
- student@appolios.com / student123
- teacher@appolios.com / teacher123

## Project Structure

```
APPOLIOS/
├── app/
│   ├── controllers/
│   │   ├── HomeController.php
│   │   ├── AuthController.php
│   │   ├── AdminController.php
│   │   └── StudentController.php
│   ├── models/
│   │   ├── User.php
│   │   ├── Course.php
│   │   └── Enrollment.php
│   ├── views/
│   │   ├── partials/
│   │   │   ├── header.php
│   │   │   └── footer.php
│   │   ├── home/
│   │   ├── auth/
│   │   ├── admin/
│   │   ├── student/
│   │   └── errors/
│   └── core/
│       ├── Controller.php
│       ├── Model.php
│       └── Router.php
├── config/
│   ├── config.php
│   └── database.php
├── public/
│   ├── index.php
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── images/
├── routes/
│   └── web.php
├── database/
│   └── appolios_db.sql
├── .htaccess
└── README.md
```

## Color Palette

- **Dark Blue (Primary)**: `#0A1F44`
- **Light Blue (Secondary)**: `#4DA8DA`
- **White (Background)**: `#FFFFFF`
- **Yellow (CTA)**: `#FFD447`

## Security Features

- Password hashing with bcrypt
- PDO prepared statements (SQL injection prevention)
- XSS protection with htmlspecialchars
- Session regeneration on login
- CSRF protection (recommended to add)
- Input validation and sanitization

## License

This project is for educational purposes.

## Author

Created for the APPOLIOS E-Learning Platform.