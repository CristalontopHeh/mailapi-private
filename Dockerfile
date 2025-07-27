FROM php:8.2-apache

# Installe les dépendances pour IMAP
RUN apt-get update && \
    apt-get install -y libc-client-dev libkrb5-dev && \
    docker-php-ext-configure imap --with-kerberos --with-imap-ssl && \
    docker-php-ext-install imap

# Active mod_rewrite (utile pour index.php)
RUN a2enmod rewrite

# Copie le code source dans le dossier Apache
COPY . /var/www/html/

# Donne les droits nécessaires
RUN chown -R www-data:www-data /var/www/html

# Expose le port web
EXPOSE 80
