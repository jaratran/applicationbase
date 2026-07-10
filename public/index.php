<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';


// ----------------------------------------
// PARCHE DE CONTEXTO PARA PROXY INVERSO HTTPS
//
// Laravel usa $_SERVER['HTTPS'] para determinar si la conexión es segura.
// En entornos con proxy inverso (como Apache con SSL frontal y backend HTTP),
// PHP no marca automáticamente $_SERVER['HTTPS'] como 'on',
// aunque el encabezado X-Forwarded-Proto venga con "https".
//
// Este parche detecta ese encabezado y fuerza la variable 'HTTPS'.
// Esto permite que Laravel::isSecure() devuelva true y evite advertencias del navegador.
//
// Requisitos del entorno:
// - Apache frontal con mod_proxy y mod_headers activados
// - VirtualHost con:
//      ProxyPass / http://localhost:8080/
//      RequestHeader set X-Forwarded-Proto "https"
//      RequestHeader set X-Forwarded-Port "443"
// - TrustProxies.php correctamente configurado
//
// Si en producción Laravel detecta HTTPS por sí solo, este bloque puede eliminarse.
//
// (Implementado localmente el Viernes 09 de Mayo de 2025 a las 21:50 hrs)
//
// NOTA: Este comportamiento fue observado específicamente cuando el backend es Apache + PHP en modo HTTP puro.
// En entornos anteriores del mismo autor con Apache + IIS (.NET), no se requiere este parche,
// dado que IIS maneja correctamente el esquema de la petición reenviada.
//
// ----------------------------------------
if (
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
) {
    $_SERVER['HTTPS'] = 'on';
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
