FROM php:8.3-apache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY dev/httpd.conf /etc/apache2/sites-available/httpd.conf
COPY ops/apache2/local-confs/codepoints.net.conf /etc/apache2/local-confs/
COPY dev/*.pem /etc/certs/

RUN apt-get update && \
    apt-get install libzip-dev -y && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-enable pdo_mysql && \
    docker-php-ext-install zip && \
    docker-php-ext-enable zip && \
    a2enmod rewrite && \
    a2enmod ssl && \
    a2enmod socache_shmcb && \
    a2enmod headers && \
    a2enmod expires && \
    a2enmod proxy && \
    a2enmod proxy_http && \
    a2enmod proxy_wstunnel && \
    a2ensite httpd && \
    a2dissite 000-default

# do not write access log to stdout
RUN rm /var/log/apache2/access.log /var/log/apache2/other_vhosts_access.log
