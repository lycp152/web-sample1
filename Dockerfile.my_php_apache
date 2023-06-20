FROM php:8.1.2-apache
RUN apt-get update
RUN docker-php-ext-install pdo_mysql

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"