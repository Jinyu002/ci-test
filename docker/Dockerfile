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

#开启rewrite
RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
COPY apache.conf /etc/apache2/sites-enabled/000-default.conf

# FPM配置文件
# COPY conf/httpd.conf /user/local/etc/www.conf

EXPOSE 80

WORKDIR /opt/ci123/www/html/api_shop/webroot
                                    