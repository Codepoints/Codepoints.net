FROM php:8.3-apache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY dev/httpd.conf /etc/apache2/sites-available/httpd.conf
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

RUN <<EOT
cat <<EOF > /usr/local/etc/php/conf.d/99-codepoints.ini
xdebug.client_host=host.docker.internal
xdebug.mode=debug
xdebug.start_with_request=yes
xdebug.discover_client_host=0
xdebug.remote_handler = "dbgp"
xdebug.client_port=9003
EOF
EOT

# do not write access log to stdout
RUN rm /var/log/apache2/access.log /var/log/apache2/other_vhosts_access.log

WORKDIR /var/www/codepoints.net
