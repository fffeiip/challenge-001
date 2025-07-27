
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

## Bonus Features Implemented

This project goes beyond the core requirements by implementing several optional bonus features, significantly enhancing its functionality and developer experience.

### 1. Fully Dockerized Environment

A complete `docker-compose` environment is provided for a seamless, one-command setup. This eliminates the need for manual local server configuration.

*   **Services**: Includes pre-configured services for the Apache/PHP application (`app`), a MySQL database (`db`), and a `phpmyadmin` container for easy database management.
*   **Automated Setup**: Running `docker-compose up -d` automatically builds the PHP image, installs Composer dependencies, and runs the database setup scripts (`db:setup`).
*   **Health Checks**: The application container waits for the database to be healthy before starting, preventing startup race conditions and connection errors.

### 2. CSV Import/Export for Weapons

The weapons inventory can be managed in bulk via CSV, a powerful feature for data migration and external editing.

*   **Export**: From the weapons list, the currently filtered and sorted data can be exported to a CSV file.
*   **Import**: A user-friendly form allows uploading a CSV file to create new weapons or update existing ones (matched by `serial_number`).
*   **Template & Validation**: A downloadable CSV template is provided to ensure correct formatting. The import process includes robust server-side validation for file structure and data integrity, providing clear, line-by-line feedback on any errors.

### 3. Bulk PDF Export

Multiple weapon spec sheets can be exported at once, a significant time-saver for administrative tasks.

*   **Multi-Select**: Users can select multiple weapons from the list using checkboxes.
*   **ZIP Archive**: The selected weapon details are generated as individual, well-formatted PDFs and bundled into a single downloadable ZIP archive for convenience.

### 4. Store Autocomplete Search

To enhance usability, the weapon creation and editing forms feature a live autocomplete search for stores.

*   **Dynamic Search**: As a user types a store name, an AJAX request is sent to a dedicated JSON endpoint (`/weapons/store-autocomplete`).
*   **REST-like Endpoint**: The server responds with a JSON list of matching stores, which are then displayed to the user, providing a fast and intuitive way to link a weapon to a store.

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


## Running the Application

You can run the application using Docker (recommended) or a local PHP development server.

### Option 1: Using Docker (Recommended)

This project is fully containerized using Docker and Docker Compose, providing a consistent and isolated development environment. This is the simplest way to get started.

1.  **Prerequisites**:
    *   [Docker](https://www.docker.com/get-started)
    *   [Docker Compose](https://docs.docker.com/compose/install/)

2.  **Build and Run the Containers**:
    From the project root, run the following command to build the images and start the services in the background:
    ```bash
    docker-compose up -d --build
    ```
    This command will:
    *   Build the PHP-Apache image for the application.
    *   Start the `app`, `db` (MySQL), and `phpmyadmin` services.
    *   The `app` container will automatically run `composer install` and `composer run db:setup` to prepare the database and runs tests, so no manual database setup is needed.

3.  **Access the Application**:
    *   **Web Application**: Open your browser and navigate to **http://localhost:8000**.
    *   **phpMyAdmin**: Access the database via phpMyAdmin at **http://localhost:8001**. Use `db` for the server, `root` for the user, and `root` for the password.

4.  **Stopping the Application**:
    To stop the containers, run:
    ```bash
    docker-compose down
    ```

### Option 2: Using the PHP Built-in Web Server (for Local Development without Docker)

### 2. Install Dependencies

Run Composer to install the required PHP libraries.

```bash
composer install
```

### 3. Configure Environment Variables

This step will only be necessary if you have a different root user and password from the DEFAULT credetials for MYSQL. IF you have the default credential. please skip this step.

The application uses a `.env` file to manage database credentials and other environment-specific settings.

1.  Open the `.env` file and replace with the code below:
    ```dotenv
    DB_HOST=127.0.0.1
    DB_DATABASE=challenge_db
    DB_USERNAME=root
    DB_PASSWORD=
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
