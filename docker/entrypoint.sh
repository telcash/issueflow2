#!/bin/sh
set -eu

port="${PORT:-80}"

# Render proporciona PORT en tiempo de ejecución; Docker local usa el puerto 80.
sed -ri "s/^Listen 80$/Listen ${port}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${port}>/" /etc/apache2/sites-available/000-default.conf

php bin/console doctrine:migrations:migrate --no-interaction
php bin/console app:ticket:seed --no-interaction

exec apache2-foreground
