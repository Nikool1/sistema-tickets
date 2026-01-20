 FROM php:8.2-apache

#habilitar extensiones necesarias
RUN docker-php-ext-install mysqli

#activar mod_rewrite
RUN a2enmod rewrite

#copiar proyecto al contenedor
COPY . /var/www/html/

#permisos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
