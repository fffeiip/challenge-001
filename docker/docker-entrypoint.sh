#!/bin/sh
set -e

# Wait for the database to be ready.
# The wait-for-db.php script will attempt to connect to the database
# using credentials from the .env file.
echo "Waiting for database connection..."
php /var/www/docker/wait-for-db.php

# Once the DB is ready, run the setup script.
echo "Database is ready. Running setup..."
composer run db:setup
echo "Database setup complete."

# # Run tests if RUN_TESTS is set to "true"
# if [ "$RUN_TESTS" = "true" ]; then
#     echo "Running tests..."
#     composer run test
#     echo "Tests complete."
# fi

# Now, execute the main command (CMD) for the container.
exec "$@"
