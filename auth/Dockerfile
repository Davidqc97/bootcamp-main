FROM php:8.2-apache

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Configurar PHP para desarrollo
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php.ini
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php.ini
RUN echo "log_errors = On" >> /usr/local/etc/php/conf.d/docker-php.ini
RUN echo "error_log = /var/log/php-error.log" >> /usr/local/etc/php/conf.d/docker-php.ini

# Configurar Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]