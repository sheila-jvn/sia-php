FROM php:8.2-cli

# Install PDO MySQL extension
RUN docker-php-ext-install pdo_mysql

# Set working directory
WORKDIR /var/www/html

# Default command (can be overridden in docker-compose)
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
