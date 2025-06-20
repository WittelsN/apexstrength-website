FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install mysqli PHP extension
RUN docker-php-ext-install mysqli

# Copy project files into the web root
COPY . /var/www/html/

# Set correct ownership and permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
