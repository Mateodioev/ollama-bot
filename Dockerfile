FROM php:8.4-cli

# Install required extensions and dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libsqlite3-dev \
    libonig-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo_mysql \
    pdo_sqlite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Command to run the bot
CMD ["php", "index.php"]