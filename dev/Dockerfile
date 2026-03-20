FROM php:8.5-apache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY dev/httpd.conf /etc/apache2/sites-available/httpd.conf
COPY dev/*.pem /etc/certs/

RUN apt-get update
RUN apt-get install libzip-dev libicu-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev -y
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN docker-php-ext-install intl
RUN docker-php-ext-enable intl
RUN docker-php-ext-install gd
RUN docker-php-ext-enable gd
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-enable pdo_mysql
RUN docker-php-ext-install zip
RUN docker-php-ext-enable zip
RUN a2enmod rewrite
RUN a2enmod ssl
RUN a2enmod socache_shmcb
RUN a2enmod headers
RUN a2enmod expires
RUN a2enmod proxy
RUN a2enmod proxy_http
RUN a2enmod proxy_wstunnel
RUN a2ensite httpd
RUN a2dissite 000-default

# do not write access log to stdout
RUN rm /var/log/apache2/access.log /var/log/apache2/other_vhosts_access.log

WORKDIR /var/www/codepoints.net
