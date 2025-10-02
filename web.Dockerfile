FROM php:8.2-apache 
 
# Ajusta DocumentRoot si quieres (opcional, ya es /var/www/html) 
ENV APACHE_DOCUMENT_ROOT=/var/www/html 
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \ 
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf 
 
# Instalar extensiones necesarias para conectar con MySQL 
RUN docker-php-ext-install pdo pdo_mysql 
 
# Habilitar módulos útiles de Apache 
RUN a2enmod rewrite headers 
  