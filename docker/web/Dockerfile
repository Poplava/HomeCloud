FROM php:7-apache

RUN apt-get update && apt-get install -y \
    curl \
    screen \
    vim \
    git \
    wget \
    sudo \
    zlib1g-dev \
    libicu-dev

# Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

RUN docker-php-ext-install -j$(nproc) zip intl bcmath

# php.ini
COPY php.ini /usr/local/etc/php/

# Apache
COPY sites-available /etc/apache2/sites-available
RUN a2ensite homecloud_non-ssl.conf
