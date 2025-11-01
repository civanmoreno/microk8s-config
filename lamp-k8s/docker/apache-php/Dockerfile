FROM php:8.2-apache

# Instalar extensiones de PHP necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar módulos de Apache
RUN a2enmod rewrite

# Configurar ServerName para evitar advertencias
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar la aplicación
COPY ./app /var/www/html/

# Dar permisos apropiados
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
