FROM php:7-apache

RUN apt-get update && apt-get install -y \
    curl \
    screen \
    vim \
    git \
    wget \
    sudo \
    zlib1g-dev \
    libicu-dev \
    openssh-server \
    supervisor

# Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

RUN docker-php-ext-install -j$(nproc) zip intl bcmath exif sockets

# Apache
COPY sites-available /etc/apache2/sites-available
RUN a2ensite homecloud_non-ssl.conf

# php.ini
COPY php.ini /usr/local/etc/php/

# Set up sshd
COPY ssh/sshd_config /etc/ssh/sshd_config
COPY ssh/authorized_keys /etc/ssh/authorized_keys/root
RUN chmod 0644 /etc/ssh/authorized_keys/*
RUN chsh -s /bin/bash root
RUN mkdir -p /var/run/sshd

# Set up supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN usermod -a -G root www-data && usermod -u 1000 www-data

CMD ["supervisord", "-n"]