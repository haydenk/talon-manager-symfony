FROM php:7.2-fpm-alpine

COPY . /var/www/html
ARG USER_ID

RUN set -e; mkdir -p /usr/share/man/man1; \
    apk upgrade --update; \
    apk add \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        curl-dev \
        icu-dev \
        libmcrypt-dev \
        libxslt-dev \
        unzip \
        curl \
        shadow \
        g++ \
        make \
        autoconf; \
    pecl install xdebug-2.6.0; \
    docker-php-source extract; \
    docker-php-ext-install -j$(nproc) iconv; \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/; \
    docker-php-ext-install -j$(nproc) gd opcache bcmath curl intl json mbstring pdo pdo_mysql xsl zip; \
    docker-php-ext-enable xdebug; \
    docker-php-source delete; \
    sed -e 's/expose_php = On/expose_php = Off/g' /usr/local/etc/php/php.ini-development > /usr/local/etc/php/php.ini; \
    sed -i -e 's/memory_limit = .*/memory_limit = -1/g' /usr/local/etc/php/php.ini; \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer; \
    usermod --uid ${USER_ID} www-data; \
    groupmod --gid ${USER_ID} www-data; \
    chown -R www-data:www-data /var/www /var/www/*

USER www-data
WORKDIR /var/www/html
VOLUME ["/var/www/html"]
