# Admin Tool (Sporting Goods Store Management)

A lightweight PHP 7.2+ CRUD application for managing stores, weapons and users, with:

- MVC-style organization  
- SQLite backend (migrations & seeds)  
- Session-based authentication & authorization (super_admin vs store_user)  
- CSRF protection, input validation & flash messaging  
- Backend-driven pagination, sorting & filtering  
- PDF export via TCPDF with branded header/footer  
- Responsive, mobile-first frontend and a dashboard  
- PHPUnit tests (PHP 7.2-compatible)  

---

## 📋 Prerequisites

- **PHP ≥ 7.2** with extensions:
  - `pdo`
  - `pdo_sqlite`
  - `mbstring`
  - `tokenizer`
  - `xml`
  - `openssl`  
- **Composer** (for dependency management)  
- **Write permission** on the project `database/` directory  
- PHP’s built-in server  

---

## ⚙️ Installation & Setup

1. **Clone the repository**  
   ```bash
   git clone --branch bruno/weapons-store --single-branch https://github.com/kambereBr/challenge-001.git
   ```

    ```bash
    cd challenge-001
    ```

2. **Install PHP dependencies**
    ```bash
    composer install
    ```
3.  **Bootstrap the database**
    
    Run the migrations and seed the database (Make sure the `database/` directory is writable and `pdo_sqlite` is enabled in your PHP configuration):
    ```bash
    php scripts/init.php
    ```
    This will create the SQLite database file (`database/database.sqlite`) and populate it with initial data. Run this command whenever you want to reset the database.
4. **Start the built-in PHP server**
    ```bash
    php -S localhost:8000 -t public
    ```
5. **Access the application**  
   Open your browser and navigate to [http://localhost:8000](http://localhost:8000)
6. **Login with default credentials**
   - **Super Admin**: 
     - username: `admin`
     - password: `admin`
   - **Store User**: 
     - username: `test_user`
     - password: `test`
7. **Usage**
   - **Dashboard**: View store and user statistics.
   - **Stores / Weapons / Users**: full CRUD with soft‐deletes
   - **Filtering, sorting & pagination**: all driven by PHP & query strings
   - **PDF Export**: Generate PDF reports for weapons, and stores.
8. **Run tests**
   ```bash
   vendor/bin/phpunit
   ```
   - **DatabaseTest**: basic SQLite insert/read
   - **StoreModelTest**: Read operation on stores
   - **PDFServiceTest**: validates PDF output bytes & content

## 🛡️ Security
- **CSRF Protection**: Enabled for all forms
- **Input Validation**: Sanitizes and validates all user inputs
- **Session Management**: Secure session handling with flash messages

## 🗂️ Project Structure

- **CHALLENGE-001/**
    - `app/` — Application code (no framework)
        - `Controllers/` — Auth, Dashboard, Store, User, Weapon controllers
        - `Migrations/` — Database migrations & seed scripts
            - `seed/`
        - `Models/` — Models with soft-delete & query helpers
        - `Views/` — PHP templates (layout, auth, dashboard, stores, weapons, users)
    - `config/` — Configuration files (e.g. `database.php`)
    - `core/` — Core framework components (Controller, Model, DB, Services, Helpers)
    - `database/` — Generated SQLite file (`database.sqlite`)
    - `public/` — Web root
        - `assets/` — CSS, JS
        - `index.php` — Front controller / router
    - `scripts/` — Utility scripts
        - `init.php` — Creates/migrates/seeds the database
    - `tests/` — PHPUnit tests
    - `composer.json` — Dependency & autoload config
    - `phpunit.xml` — PHPUnit configuration
    - `README.md` — This documentation

## 👤 Authors
- **Bruno Kambere** - [kambereBr](https://github.com/kambereBr)
- **Challenge author** - [fffeiip](https://github.com/fffeiip)

## 📄 License

MIT License
---