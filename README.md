# APPOLIOS

PHP MVC application. Application code lives only under **`EspritBookMVC/`** with folders: `Controller/`, `Model/`, `View/`, `config/`, `database/`.

## Entry URL (XAMPP)

```
http://localhost/appolios-project/EspritBookMVC/Controller/index.php
```

Routes use the `url` query parameter (e.g. `?url=home`).

## CLI maintenance (MVC)

Account maintenance logic lives in **`Model/AccountMaintenance.php`** and is invoked by **`Controller/MaintenanceController.php`** via the CLI front controller **`Controller/cli.php`**.

From the repository root:

```bash
php EspritBookMVC/Controller/cli.php help
php EspritBookMVC/Controller/cli.php fix-passwords
php EspritBookMVC/Controller/cli.php setup-teachers
php EspritBookMVC/Controller/cli.php fix-accounts
php EspritBookMVC/Controller/cli.php reset
php EspritBookMVC/Controller/cli.php debug-login
php EspritBookMVC/Controller/cli.php test-auth
```

## Uploaded files

Group cover images are stored under **`uploads/groupes/`** at the project root (outside the MVC tree). Configure the web server so this path is reachable (same host as the app).

## Layout

```
appolios-project/
├── EspritBookMVC/          # MVC: Controller, Model, View, config, database (+ Controller/cli.php)
├── uploads/groupes/        # runtime uploads (gitignored)
└── README.md
```
