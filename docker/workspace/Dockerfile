FROM php:7.4-fpm

RUN set -xe; \
    apt-get update -yqq && \
    apt-get install -yqq \
    apt-utils \
    npm \
    git \
    cron \
    vim \
    zip \
    unzip \
    htop \
    procps \
    zlib1g-dev \
    libpng-dev \
    libonig-dev \
    libzip-dev && \
    apt autoremove -yqq

RUN docker-php-ext-install \
    zip \
    pdo \
    pdo_mysql \
    mbstring \
    gd

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
php -r "unlink('composer-setup.php');"

RUN npm install npm@latest -g