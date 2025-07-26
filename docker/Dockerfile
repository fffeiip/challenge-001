# Use the official PHP 7.2 image with Apache
FROM php:7.2-apache

# Change sources to Debian archive since Buster is EOL
RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list && \
    sed -i 's/security.debian.org/archive.debian.org/g' /etc/apt/sources.list && \
    sed -i '/buster-updates/d' /etc/apt/sources.list

# Install system dependencies required for PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && rm -rf /var/lib/apt/lists/*

# Install required PHP extensions for your application
RUN docker-php-ext-install pdo pdo_mysql mysqli zip

# Enable Apache's rewrite module for potential clean URLs in the future
RUN a2enmod rewrite

# Overwrite the default Apache virtual host file with our own
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www/html

# Change ownership of the web directory to the Apache user
# The actual code will be mounted via docker-compose, but this sets permissions correctly.
RUN chown -R www-data:www-data /var/www/html
