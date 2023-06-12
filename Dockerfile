# Start with a base image that includes PHP and other necessary extensions
FROM php:8.0-fpm

# Install PostgreSQL dependencies
RUN apt-get update \
    && apt-get install -y libpq-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql

# Enable the pdo_pgsql extension
RUN docker-php-ext-enable pdo_pgsql
