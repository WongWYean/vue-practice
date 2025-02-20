# 🟢 PHP + Apache
FROM php:8.2-apache

WORKDIR /var/www/html

# Install Xdebug properly
RUN apt-get update && apt-get install -y unzip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

# Enable Apache modules (Rewrite for Vue routing, Headers for CORS)
# RUN a2enmod rewrite headers

# Copy PHP backend files
COPY server /var/www/html/server/

# Copy .htaccess files for API routing
# COPY server/.htaccess /var/www/html/server/.htaccess
COPY server/.htaccess /var/www/html/.htaccess

# Set the ServerName to suppress the warning about Apache's domain name
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html

# Enable Apache modules (Rewrite for Vue routing, Headers for CORS)
RUN a2enmod rewrite headers

# Expose Apache port
EXPOSE 80
CMD ["apache2-foreground"]