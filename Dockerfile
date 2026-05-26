FROM php:8.3-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
ENV PORT=8080

RUN apt-get update \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/apache/ports.conf /etc/apache2/ports.conf

WORKDIR /var/www/html
COPY . .

RUN mkdir -p data \
    && chown -R www-data:www-data data

CMD ["apache2-foreground"]
