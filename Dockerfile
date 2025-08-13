# ===== Etapa 1: Composer con PHP 8.2 =====
# Usamos esta versión solo para construir dependencias, con el "As vendor" permite copiar el resultado a la imagen final sin arrastrar herramientas de build
FROM php:8.2-cli AS vendor

# Instala Composer y herramientas que suelen necesitarse al instalar dependencias
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
# Instalamos extensiones PHP necesarias para Composer y otras utilidades, hay cosas que sobran, más adelante se pueden optimizar
RUN apt-get update && apt-get install -y --no-install-recommends \
      git unzip libzip-dev \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*
# Forzamos a que los comandos se ejecuten dentro de la /app
WORKDIR /app
# Variable de entorno para composer, permite que se ejecute como root sin problemas de permisos
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copiamos sólo lo necesario para resolver dependencias, de esta manera Docker cachea y no vuelva a instalar si no hay cambios
COPY composer.json composer.lock* ./

# Instala dependencias sin dev y optimiza autoloader. Excluimos dependencias de desarrollo, descarga de archivos empaquetados, limpia salida y evita prompts y genera autoloader optimizado.
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --optimize-autoloader

# ===== Etapa 2: runtime PHP + Apache =====
FROM php:8.2-apache

# Paquetes del sistema + extensiones PHP. Instala las extensiones mínimas necesarias para PHP y Apache, además de habilitar módulos de Apache.
RUN apt-get update && apt-get install -y --no-install-recommends \
      libzip-dev unzip \
    && docker-php-ext-install zip pdo pdo_mysql \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Docroot configurable si la app tuviera carpeta public, se lo añadiríamos al DOCROOT al final "/public"
ARG DOCROOT=/var/www/html
# Exponemos el puerto 80 para que Apache sirva la aplicación
ENV APACHE_DOCUMENT_ROOT=${DOCROOT}

# Ajusta VirtualHost y AllowOverride para que .htaccess funcione en el nuevo docroot
# Apache por defecto sirve desde /var/www/html, 1º cambiamos a #{APACHE_DOCUMENT_ROOT} y luego ajustamos la configuración de Apache para que use este nuevo docroot
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri 's!<Directory /var/www/>!<Directory ${APACHE_DOCUMENT_ROOT}/>!g' /etc/apache2/apache2.conf

 # Pasamos a trabajar en el DOCKROOT
WORKDIR /var/www/html

# Copiamos el código de la app a la imagen final
# Usamos --chown para que los archivos sean propiedad del usuario www-data, que es el usuario por defecto de Apache en esta imagen y evitemos problemas de permisos
COPY --chown=www-data:www-data . .

# Copiamos vendor construido en la etapa de Composer, traémos vendor desde la 1º etapa, ahorrandonos Composer, git ni nada más
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor

# (Opcional) php.ini propio
# COPY .docker/php.ini /usr/local/etc/php/conf.d/app.ini

# Apache queda en primer plano por defecto, no hace falta CMD ni ENTRYPOINT ya que la imagen base ya lo tiene configurado para iniciar Apache en primer plano.

#  Se construye usando: docker build -t dosxdos_app-php --build-arg DOCROOT=/var/www/html .

# Como unirlo a la databse: 
#    docker run --rm -p 8080:80 `
# -e DB_HOST=host.docker.internal `
#  -e DB_DATABASE=mi_bd `
#  -e DB_USERNAME=mi_user `
#  -e DB_PASSWORD=mi_pass `
#  dosxdos_app-php
# --restart unless-stopped -d dosxdos_app-php

# PS C:\xampp\htdocs\dosxdos_app> docker run --name dosxdos_app `           
#>>   -p 8080:80 `
#>>   -e DB_HOST=localhost `
#>>   -e DB_PORT=3306 `
#>>   -e DB_DATABASE=dosxdos `
#>>   -e DB_USERNAME=dosxdos `
#>>   -e DB_PASSWORD=Abfe04** `          
#>>   --restart unless-stopped -d dosxdos_app-php

# https://dosxdos.app.iidos.com