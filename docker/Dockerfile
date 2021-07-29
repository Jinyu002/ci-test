#选择基础镜像
FROM php:7.2-apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

#语言设置
ENV LANG C.UTF-8

#安装php扩展
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libmagickwand-dev \
        libmagickcore-dev \
        libpng-dev \
        libxml2 \
        libxml2-dev \
        libmemcached-dev \
        zlib1g-dev \
        librabbitmq-dev \
        wget \
        vim \
        git \
        less \
        libssl-dev\
    && wget https://github.com/libevent/libevent/releases/download/release-2.1.8-stable/libevent-2.1.8-stable.tar.gz -O libevent.tar.gz \
    && mkdir -p libevent \
    && tar -xf libevent.tar.gz -C libevent --strip-components=1 \
    && rm libevent.tar.gz \
    && ( \
        cd libevent \
        && ./configure \
        && make -j$(nproc) \
        && make install \
        && ldconfig \
    ) \
    && rm -r libevent \
    && wget https://pecl.php.net/get/imagick-3.4.4.tgz \
    && tar zxvf imagick-3.4.4.tgz \
    && rm imagick-3.4.4.tgz \
    && ( \
        cd imagick-3.4.4 \
        && phpize \
        && ./configure \
        && make \
        && make install \
    ) \
    && rm -r imagick-3.4.4 \
    && docker-php-ext-enable imagick \
    && docker-php-ext-install -j$(nproc) bcmath exif gettext pcntl zip sockets mysqli pdo_mysql soap xmlrpc opcache

RUN docker-php-ext-configure gd --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && wget http://pecl.php.net/get/memcached-3.0.4.tgz \
    && pecl install memcached-3.0.4.tgz \
    && wget http://pecl.php.net/get/redis-4.1.1.tgz \
    && pecl install redis-4.1.1.tgz \
    && wget http://pecl.php.net/get/event-2.4.1.tgz \
    && pecl install event-2.4.1.tgz \
    && wget http://pecl.php.net/get/amqp-1.9.3.tgz \
    && pecl install amqp-1.9.3.tgz \
    && docker-php-ext-enable memcached redis event amqp \
    && curl -sS https://getcomposer.org/install | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer self-update --clean-backups

#node环境安装
RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.33.11/install.sh | bash \
    && export NVM_DIR="$HOME/.nvm" \
    && [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" \
    && [ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion" \
    && nvm install 9.11 \
    && npm install -g pm2 node-schedule

#开启rewrite
RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
COPY apache.conf /etc/apache2/sites-enabled/000-default.conf

# FPM配置文件
# COPY conf/httpd.conf /user/local/etc/www.conf

EXPOSE 80

WORKDIR /opt/ci123/www/html/api_shop/webroot
~                                             