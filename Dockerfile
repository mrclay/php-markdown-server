FROM php:8.4-apache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y git && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite && a2enmod expires

WORKDIR /var/www

RUN rm -rf html
RUN ln -s public html

EXPOSE 80
