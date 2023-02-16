# Set the base image for subsequent instructions
FROM php:8.0.11-apache

# Set the working directory
WORKDIR /var/www

# Copy the application code
COPY . .

# Install required PHP extensions
RUN docker-php-ext-install pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

RUN chown -R www-data:www-data /var/www

# Expose port 80 for web traffic
EXPOSE 80
