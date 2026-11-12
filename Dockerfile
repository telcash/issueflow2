# Imagen oficial de PHP 8.4 con Apache incluido
FROM php:8.4-apache

# Instalo los paquetes necesarios para mi app
RUN apt-get update \
    && apt-get install -y \
        git \
        unzip \
        libicu-dev \
        libpq-dev \
        libzip-dev \
    && docker-php-ext-install \
        intl \
        pdo_mysql \
        pdo_pgsql \
        zip \
    && rm -rf /var/lib/apt/lists/*

# Actuva el módulo mod_rewrite de Apache
RUN a2enmod rewrite

# Copio composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Ejecuta los scripts de Composer y Symfony en el entorno de producción.
ENV APP_ENV=prod \
    APP_DEBUG=0

# Copio el contenido del contexto de construcción hacia el directorio de trabajo
COPY . .

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

COPY docker/apache/000-default.conf \
    /etc/apache2/sites-available/000-default.conf

COPY docker/entrypoint.sh /usr/local/bin/issueflow-entrypoint
RUN chmod +x /usr/local/bin/issueflow-entrypoint

RUN mkdir -p var \
    && chown -R www-data:www-data var \
    && chmod -R 775 var

CMD ["/usr/local/bin/issueflow-entrypoint"]
