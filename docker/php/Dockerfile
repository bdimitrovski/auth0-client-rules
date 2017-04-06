FROM php:5-fpm
MAINTAINER Bojan Dimitrovski <b.g.dimitrovski@gmail.com>

# install requirements
RUN apt-get update \
    && apt-get install -y libcurl4-openssl-dev libssl-dev libsasl2-dev zlib1g-dev libicu-dev g++ libmcrypt-dev \
    curl git unzip --no-install-recommends \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install -j$(nproc) mysql intl mcrypt \
    && docker-php-ext-install pdo pdo_mysql

# install and config xdebug
RUN yes | pecl install xdebug \
    && docker-php-ext-enable xdebug.so \
    && echo "xdebug.default_enable = 1" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_connect_back = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_handler: dbgp" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_port = 9100" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.max_nesting_level = 250" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "#!/bin/bash" > /usr/local/bin/xdebug \
    && echo 'XDEBUG_CONFIG="idekey=xdebug" PHP_IDE_CONFIG="serverName=project.dev" php -dxdebug.remote_host=$HOST_IP "$@"' >> /usr/local/bin/xdebug \
    && chmod a+x /usr/local/bin/xdebug

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# define host ip as env
RUN echo "export HOST_IP=`/sbin/ip route|awk '/default/ { print $3 }'`" >> ~/.bashrc

WORKDIR /srv/www/app

EXPOSE 9000
