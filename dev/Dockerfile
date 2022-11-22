FROM php:8.1-apache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY dev/httpd.conf /etc/apache2/sites-available/httpd.conf
COPY dev/*.pem /etc/certs/

RUN docker-php-ext-install pdo_mysql && \
    docker-php-ext-enable pdo_mysql && \
    a2enmod rewrite && \
    a2enmod ssl && \
    a2enmod socache_shmcb && \
    a2enmod headers && \
    a2enmod expires && \
    a2enmod proxy && \
    a2enmod proxy_http && \
    a2enmod proxy_wstunnel && \
    a2ensite httpd
