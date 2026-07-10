@echo off
cls

echo --------------------------------------------------------------------------------
echo Generando cache de configuracion: php8 artisan config:cache
pause
php8 artisan config:cache

echo --------------------------------------------------------------------------------
echo Generando cache de rutas: php8 artisan route:cache
pause
php8 artisan route:cache

echo --------------------------------------------------------------------------------
echo Generando cache de vistas: php8 artisan view:cache
pause
php8 artisan view:cache

echo --------------------------------------------------------------------------------
echo Generando cache de eventos: php8 artisan event:cache
pause
php8 artisan event:cache

echo --------------------------------------------------------------------------------
echo Optimizando autoload y dumping de clases: composer8 dump-autoload --optimize
pause
composer8 dump-autoload --optimize

echo --------------------------------------------------------------------------------
echo ¡Optimizacion completada con exito!
pause
