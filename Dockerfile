
FROM php:8.1-apache

RUN apt update
RUN apt install zip -y
RUN apt install unzip -y

RUN apt install -y libicu-dev

RUN apt install locate -y
RUN apt install vim -y 

RUN usermod -u 1000 www-data
RUN usermod -G staff www-data

RUN mkdir -p /var/www/html/ecf-garage-back
COPY ./ ./ecf-garage-back

WORKDIR /var/www/html/ecf-garage-back

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install intl
RUN docker-php-ext-install pdo_mysql

#RUN export COMPOSER_PROCESS_TIMEOUT=60
RUN composer install --no-dev --optimize-autoloader 

RUN chown -R www-data:www-data /var/www/*
RUN chmod 777 var/* -R

WORKDIR /var/www/html/ecf-garage-back

RUN mkdir config/jwt

COPY ./private.pem config/jwt/
COPY ./public.pem config/jwt/

RUN mkdir -p /etc/apache2/ssl

COPY ./studi-public.crt /etc/apache2/ssl/server.crt
COPY ./studi-private.key /etc/apache2/ssl/server.key
COPY ./httpd-vhosts-9443.conf /etc/apache2/sites-available/

RUN echo "Include /etc/apache2/sites-available/httpd-vhosts-9443.conf" | tee -a /etc/apache2/sites-available/000-default.conf > /dev/null
RUN echo "Listen 9443" | tee -a /etc/apache2/ports.conf > /dev/null

RUN apt-get install -y ca-certificates
COPY ./studi-cacert.crt /usr/local/share/ca-certificates
RUN update-ca-certificates

RUN a2enmod rewrite
RUN a2enmod ssl 

RUN chmod -R 755 scripts
#ENTRYPOINT scripts/docker.sh

EXPOSE 9443 

#RUN service apache2 start
