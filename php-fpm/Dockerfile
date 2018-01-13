FROM alpine:3.6

RUN apk add --update \
    php7-fpm \
    php7-apcu \
    php7-ctype \
    php7-curl \
    php7-dom \
    php7-gd \
    php7-iconv \
    php7-imagick \
    php7-json \
    php7-intl \
    php7-mcrypt \
    php7-mbstring \
    php7-opcache \
    php7-openssl \
    php7-pdo \
    php7-pdo_mysql \
    php7-mysqli \
    php7-xml \
    php7-zlib \
    php7-phar \
    php7-tokenizer \
    php7-session \
    make \
    curl


RUN rm -rf /var/cache/apk/* && rm -rf /tmp/*

RUN curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer
RUN curl -L --insecure https://phar.phpunit.de/phpunit-6.1.phar -o /usr/bin/phpunit && chmod +x /usr/bin/phpunit

ADD symfony.ini /etc/php7/php-fpm.d/
ADD symfony.ini /etc/php7/cli/conf.d/

ADD symfony.pool.conf /etc/php7/php-fpm.d/

CMD  mkdir -p /var/www/symfony3/var/ 
CMD  chmod 777 /var/www/symfony3/var/ -R

CMD ["php-fpm7", "-F"]

WORKDIR /var/www/symfony3

EXPOSE 9001
