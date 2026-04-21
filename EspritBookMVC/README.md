# APPOLIOS (EspritBookMVC)

PHP MVC application. **All code lives under this folder** (`Controller/`, `Model/`, `View/`, `config/`, `database/`).

## Entry URL (XAMPP example)

```
http://localhost/appolios-project/EspritBookMVC/Controller/index.php
```

Use the `url` query parameter for routes, e.g. `?url=home` or `?url=login`.

## Configuration

- `config/config.php` — app constants, DB settings, `APP_URL` / `APP_ENTRY`
- `config/database.php` — `getConnection()` (PDO)

## Database

Import or sync schema from `database/schema.sql` (phpMyAdmin or MySQL CLI).

## CLI utilities (optional)

From the repository root:

```bash
php EspritBookMVC/scripts/fix_passwords.php
```

Other scripts: `debug_login.php`, `fix_accounts.php`, `reset.php`, `setup_teachers.php`, `test_auth.php`.

## Default logins (after `fix_passwords.php`)

- admin@appolios.com / admin123  
- student@appolios.com / student123  
- teacher@appolios.com / teacher123  

## Structure

```
EspritBookMVC/
├── Controller/
├── Model/
├── View/
├── config/
├── database/
├── uploads/
└── scripts/          # maintenance CLI scripts
```
