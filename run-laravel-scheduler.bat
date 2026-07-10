@echo off
title LaPortada - Laravel Scheduler (Telegram y Programa Diario)
cd /d D:\Apps-Apache-htdocs\laportada

echo --------------------------------------------------------------------------------------------------------------
echo Iniciando ejecucion continua del scheduler Laravel (telegram:procesar-mensajes y programa-diario:emitir-auto)
echo Aplicacion: LaPortada
echo Path: %CD%
echo --------------------------------------------------------------------------------------------------------------

REM Ejecuta el scheduler usando PHP 8.2 renombrado como "php8"
php8 artisan schedule:work

echo --------------------------------------------------------------------------------------------------------------
echo Scheduler finalizado o detenido manualmente.
pause
