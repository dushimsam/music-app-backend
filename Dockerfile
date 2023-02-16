# Set the base image for subsequent instructions
FROM php:7.3

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    openssl \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# Install required PHP extensions
RUN docker-php-ext-install pdo mbstring pdo_mysql


# Set the working directory
WORKDIR /app/backend

# Copy the application code
COPY . .

RUN composer install

# Expose port 80 for web traffic
EXPOSE 8000

CMD php artisan serve --host:0.0.0.0
