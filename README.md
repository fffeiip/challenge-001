 # PHP Stores & Weapons CRUD Application

This project is a pure PHP 7.2 web application for managing stores and their weapon inventories, built as a technical challenge. It provides full CRUD (Create, Read, Update, Delete) functionality for both stores and weapons, featuring server-side sorting, filtering, pagination, and PDF exports without relying on any major frameworks.

---

## Features

*   **Stores Management**: Full CRUD operations for store records.
*   **Weapons Management**: Full CRUD operations for weapon records.
*   **Relational Data**: Each weapon is linked to a store.
*   **Interactive Tables**: All data tables support server-side:
    *   Sorting by any column.
    *   Filtering by any column.
    *   Pagination to handle large datasets.
*   **PDF Export**: Generate a PDF "spec sheet" for any weapon record.
*   **Navigation**: Easily navigate from a weapon to its store, and from a store to its list of weapons.

## Technical Stack

*   **Backend**: PHP 7.2 (No Frameworks)
*   **Database**: MySQL / MariaDB
*   **Frontend**: Vanilla HTML, CSS, and JavaScript
*   **Dependencies**:
    *   [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv) for environment variable management.
    *   [tecnickcom/tcpdf](https://github.com/tecnickcom/TCPDF) for PDF generation.

---

## Prerequisites

Before you begin, ensure you have the following installed on your local machine:

*   **PHP 7.2.x**
*   **MySQL or MariaDB Server** (e.g., via XAMPP, WAMP, MAMP)
*   **[Composer](https://getcomposer.org/)** for managing PHP dependencies.

## Setup Instructions

Follow these steps to get your development environment set up.

### 1. Clone the Repository

```bash
git clone <your-repository-url>
cd challenge-001
```

### 2. Install Dependencies

Run Composer to install the required PHP libraries.

```bash
composer install
```

### 3. Configure Environment Variables

This step will only be necessary if you have a different root user and password from the DEFAULT credetials for MYSQL. IF you have the default credential. please skip this step.

The application uses a `.env` file to manage database credentials and other environment-specific settings.

1.  Copy the example environment file:
    ```bash
    copy .env.example .env
    ```
2.  Open the `.env` file and update the database credentials to match your local setup:
    ```dotenv
    DB_HOST=127.0.0.1
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_user
    DB_PASSWORD=your_database_password
    ```

### 4. Database Setup

1.  **Start your MySQL/MariaDB server** (e.g., from the XAMPP Control Panel).
2.  **Run the setup script**. This project includes a Composer script that automates the entire database  setup process. This single command will:
    * **Provide full setup, creating the databse,migrating and seeeding, providing a development ready setup**
     Run the following command from the project root:

    ```bash
    composer run db:setup
    ```
    
    If everything is configured correctly in your `.env` file, your database will be ready to use. You can also run these steps individually if you choose but not recommended (e.g., `composer db:create`, `composer db:migrate`, `composer db:seed`).

    *   **Reset Database tables**: Executes this command in the terminal  which runs the `db/reset_db.php` script to reset the table and drop all available data and tables idempontently
    ```bash
    composer run db:reset
    ``` 
---

## Running the Application

You can run the application using either PHP's built-in web server or a full-featured server like Apache.

### Option 1: Using the PHP Built-in Web Server (Recommended for Development)

1.  Navigate to the project's root directory in your terminal.
2.  Run the following command:
    ```bash
    php -S localhost:8000 -t public
    ```
3.  Open your web browser and go to **http://localhost:8000**.


## Running Tests

This project uses PHPUnit for automated testing. The tests are configured in `phpunit.xml` and can be run via Composer scripts.

### Running the Test Suite

To run all the unit and integration tests, execute the following command from the project root:

```bash
composer run test
```

### Generating Test Coverage

To run the tests and generate an HTML code coverage report, use this command:

```bash
composer run test-coverage
```

The coverage report will be generated in the `public/coverage/` directory. You can open the `index.html` file in that directory with your browser to view the report.

**Note**: For test coverage to work correctly, you may need to have the Xdebug PHP extension installed and enabled.



