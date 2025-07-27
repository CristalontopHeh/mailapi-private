FROM php:8.2-apache

# Active mod_rewrite (utile pour index.php)
RUN a2enmod rewrite

# Copie le contenu du dossier dans /var/www/html
COPY . /var/www/html/

# Donne les droits nécessaires
RUN chown -R www-data:www-data /var/www/html

# Expose le port
EXPOSE 80
