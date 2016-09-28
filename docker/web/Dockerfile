FROM php:7-apache

RUN apt-get update && apt-get install -y \
    git

# Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

COPY sites-available /etc/apache2/sites-available
RUN a2ensite homecloud_non-ssl.conf