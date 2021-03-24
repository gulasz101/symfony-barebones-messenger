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

# install amqp
RUN \
    apt-get install -y \
        librabbitmq-dev \
        libssh-dev \
    && pecl install amqp \
    && docker-php-ext-enable amqp

RUN \
    echo 'alias sf="php bin/console"' >> ~/.bashrc

RUN \
    apt-get install -y \
        supervisor

COPY ./etc/supervisor/supervisord.conf /etc/supervisor
#COPY ./etc/supervisor/conf.d/* /etc/supervisor/conf.d/
COPY ./supervisor.sh /opt/supervisor.sh

ENTRYPOINT ["bash", "/opt/supervisor.sh"]