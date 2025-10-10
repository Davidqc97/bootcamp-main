FROM php:8.2-apache

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Configurar PHP
RUN echo "upload_max_filesize = 10M" >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini
RUN echo "post_max_size = 10M" >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini
RUN echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini

# Configurar Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]