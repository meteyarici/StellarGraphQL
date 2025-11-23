FROM php:8.4-fpm

# Sistem bağımlılıkları
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    curl \
    zip \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip

# Node.js eklemek istiyorsan (Debian resmi repo)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
