# # Use the official PHP 7.2 image with Apache
# FROM php:7.2-apache

# # Change sources to Debian archive since Buster is EOL
# RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list && \
#     sed -i 's/security.debian.org/archive.debian.org/g' /etc/apt/sources.list && \
#     sed -i '/buster-updates/d' /etc/apt/sources.list

# # Install system dependencies required for PHP extensions
# RUN apt-get update && apt-get install -y \
#     libzip-dev \
#     zip \
#     && rm -rf /var/lib/apt/lists/*

# # Install required PHP extensions for your application
# RUN docker-php-ext-install pdo pdo_mysql mysqli zip

# # Enable Apache's rewrite module for potential clean URLs in the future
# RUN a2enmod rewrite

# # Overwrite the default Apache virtual host file with our own
# COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# # Install Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Set the working directory
# WORKDIR /var/www/html

# # Change ownership of the web directory to the Apache user
# # The actual code will be mounted via docker-compose, but this sets permissions correctly.
# RUN chown -R www-data:www-data /var/www/html


# FROM php:7.2-apache

# # Fix outdated Debian Stretch mirrors
# RUN sed -i 's|http://deb.debian.org|http://archive.debian.org|g' /etc/apt/sources.list && \
#     sed -i 's|security.debian.org|archive.debian.org|g' /etc/apt/sources.list && \
#     echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99no-check-valid-until

# # Use non-interactive mode for apt
# ENV DEBIAN_FRONTEND=noninteractive

# # Install system dependencies
# RUN apt-get update && apt-get install -y \
#     curl \
#     unzip \
#     zip \
#     git \
#     libzip-dev \
#     libpng-dev \
#     libjpeg-dev \
#     libonig-dev \
#     libfreetype6-dev \
#     && docker-php-ext-configure zip --with-libzip \
#     && docker-php-ext-install pdo pdo_mysql zip \
#     && apt-get clean && rm -rf /var/lib/apt/lists/*

# # Copy app files
# COPY . /var/www/html

# # Set working directory
# WORKDIR /var/www/html

# # Install Composer
# RUN curl -sS https://getcomposer.org/installer | php \
#     && mv composer.phar /usr/local/bin/composer

# # Install PHP dependencies
# RUN composer install

# # Change ownership of files to www-data
# RUN chown -R www-data:www-data /var/www/html

# # Set user and group IDs for Apache process
# USER www-data


FROM php:7.2-apache

# Fix outdated Debian mirrors
RUN sed -i 's|deb.debian.org|archive.debian.org|g' /etc/apt/sources.list && \
    sed -i 's|security.debian.org|archive.debian.org|g' /etc/apt/sources.list && \
    echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99no-check-valid-until

ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    zip \
    git \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libfreetype6-dev \
    && docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Copy composer files and install dependencies to leverage Docker cache
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-dev --prefer-dist --no-autoloader

# Copy your project files
COPY . .

# Dump autoloader
RUN composer dump-autoload --no-dev --optimize

# Copy the entrypoint script and make it executable
COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set permissions
RUN chown -R www-data:www-data /var/www

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
