FROM php:8.4
RUN apt-get update && apt-get -yq \
    install \
    git \
    unzip \
    zip \
    libzip-dev \
    zlib1g-dev \
    libicu-dev
RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN echo 'memory_limit = 512M' >> /usr/local/etc/php/php.ini
WORKDIR /var/www/bundle
