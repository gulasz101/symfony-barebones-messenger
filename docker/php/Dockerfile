FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
    pkg-config \
    libssl-dev \
    libmcrypt-dev \
    sqlite3 \
    libsqlite3-dev \
    openssl \
    libcurl4-openssl-dev \
    git \
    zip \
    zlib1g-dev \
    libzip-dev \
    unzip \
    mailutils \
    mc

# Install PECL extensions
RUN \
    pecl install xdebug \
    docker-php-ext-enable xdebug

# install mysql support
RUN \
    docker-php-ext-install \
        mysqli \
        pdo \
        pdo_mysql

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN \
    echo 'alias sf="php bin/console"' >> ~/.bashrc