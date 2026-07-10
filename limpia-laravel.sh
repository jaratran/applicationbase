#!/bin/bash

clear

echo
echo "--------------------------------------------------------------------------------"
echo "Iniciando limpieza de caches de Laravel..."
echo

echo "--------------------------------------------------------------------------------"
echo "Eliminando carpeta bootstrap/cache para evitar datos cacheados obsoletos..."
rm -rf bootstrap/cache

echo "--------------------------------------------------------------------------------"
echo "Recreando carpeta bootstrap/cache..."
mkdir -p bootstrap/cache
cat <<EOL > bootstrap/cache/.gitignore
*
!.gitignore
EOL

echo "--------------------------------------------------------------------------------"
echo "Limpiando cache de configuracion: php artisan config:clear"
php artisan config:clear

echo "--------------------------------------------------------------------------------"
echo "Limpiando cache de rutas: php artisan route:clear"
php artisan route:clear

echo "--------------------------------------------------------------------------------"
echo "Limpiando vistas Blade compiladas: php artisan view:clear"
php artisan view:clear

echo "--------------------------------------------------------------------------------"
echo "Limpiando cache de eventos: php artisan event:clear"
php artisan event:clear

echo "--------------------------------------------------------------------------------"
echo "Limpiando cache general de la aplicacion: php artisan cache:clear"
php artisan cache:clear

echo "--------------------------------------------------------------------------------"
echo "Limpiando archivos compilados: php artisan clear-compiled"
php artisan clear-compiled

echo "--------------------------------------------------------------------------------"
echo "Asegurandose de eliminar todo: php artisan optimize:clear"
php artisan optimize:clear

echo
echo "--------------------------------------------------------------------------------"
echo "Todos los comandos de limpieza han sido ejecutados."
echo

echo "--------------------------------------------------------------------------------"
echo "Generando cache de configuracion: php artisan config:cache"
php artisan config:cache