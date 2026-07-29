FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    zip \
    libzip-dev \
    unzip \
    git \
    curl \
    mysql-client

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
