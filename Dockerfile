FROM php:8.2-fpm

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    wget \
    && docker-php-ext-install \
    pdo_mysql \
    zip \
    intl

# Installation de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installation du Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt-get install -y symfony-cli

# Configuration PHP
COPY php.ini /usr/local/etc/php/conf.d/app.ini

WORKDIR /var/www/html

# Configuration du PHP-FPM
RUN echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "php_admin_value[error_log] = /var/log/fpm-php.www.log" >> /usr/local/etc/php-fpm.d/www.conf

# Exposer le port FPM
EXPOSE 9000

CMD ["php-fpm"]