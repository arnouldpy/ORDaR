FROM php:5.6.30-apache
RUN apt-get update
RUN apt-get install  php5-curl libssl-dev libssh2-1-dev  -y
RUN pecl install mongo
RUN pecl install ssh2
RUN a2enmod rewrite 
RUN echo 'extension=mongo.so' >> /usr/local/etc/php/php.ini
RUN echo 'extension=ssh2.so' >> /usr/local/etc/php/php.ini
RUN mkdir -p /data/applis/ORDaR/Uploads/
COPY . /var/www/html/ORDaR/
RUN chown -R www-data:www-data /data/applis/ORDaR/Uploads/
COPY ./Docker/Apache_PHP/config.ini /var/www/html/ORDaR/Frontend/config.ini
COPY ./Docker/Apache_PHP/000-default.conf /etc/apache2/sites-enabled/000-default.conf
