FROM php:8.2-fpm

# Installation des dépendances système en une seule couche
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    wget \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    zip \
    intl \
    opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installation de Composer avec une version spécifique
COPY --from=composer:2.6 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

# Configuration optimisée de PHP-FPM
RUN echo "pm = dynamic" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.max_children = 50" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.start_servers = 5" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.min_spare_servers = 5" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.max_spare_servers = 35" >> /usr/local/etc/php-fpm.d/www.conf

EXPOSE 9000
CMD ["php-fpm"]