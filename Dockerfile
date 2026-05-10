# Multi-stage build for Laravel + Vue.js application

# Stage 1: Build Node assets
FROM node:22-alpine AS node_builder

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm install --no-audit --no-fund

# Copy source files
COPY . .

# Build assets
RUN npm run build

# Stage 2: PHP Application
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    default-mysql-client \
    && docker-php-ext-install -j$(nproc) \
    gd \
    zip \
    pdo \
    pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set Apache document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Configure Apache for Laravel
RUN echo '<Directory /var/www/html/public>' >> /etc/apache2/apache2.conf && \
    echo '    Options Indexes FollowSymLinks' >> /etc/apache2/apache2.conf && \
    echo '    AllowOverride All' >> /etc/apache2/apache2.conf && \
    echo '    Require all granted' >> /etc/apache2/apache2.conf && \
    echo '</Directory>' >> /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Copy built assets from Node stage
COPY --from=node_builder /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Create required directories and set permissions
RUN mkdir -p storage/logs && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 775 storage bootstrap/cache

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Start Apache
CMD ["apache2-foreground"]
