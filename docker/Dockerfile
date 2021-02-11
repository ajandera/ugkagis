FROM php:7.0-apache
RUN docker-php-ext-install pdo_mysql
COPY ./php.ini /usr/local/etc/php

ADD ./apache.conf /etc/apache2/sites-available/
RUN ln -s /etc/apache2/sites-available/apache.conf /etc/apache2/sites-enabled/
RUN a2enmod rewrite
