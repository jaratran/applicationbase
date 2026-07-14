<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AgrupaDominios
{
    /**
        * Agrupa direcciones de correo por dominio (To + CC).
        * Si un dominio no tiene To, pero sí CC, mueve todos los CC a To.
        * 
        * Usado al enviar una misma notificación a destinatarios de varios dominios.
        * El propósito es evitar la excepción bloqueante de GMAIL 'Error 451 Multiple destination domains'.
        *
        * @param array $to Lista de destinatarios principales (To)
        * @param array $cc Lista de destinatarios en copia (CC)
        * @return array Array asociativo con dominios como clave y subarrays 'to' y 'cc'
        */
     public static function agrupaDominios(array $to = [], array $cc = []): array {
        $g = [];                                                                    // Aquí vamos a guardar el resultado final

        // Procesar destinatarios "To"
        foreach ($to as $mail) {
            $dom = Str::afterLast($mail, '@');                                      // Extraemos el dominio de la dirección (todo lo que está después de @)
            $g[$dom]['to'][] = $mail;                                               // Añadimos este correo dentro de la lista 'to' del dominio correspondiente
        }

        // Procesar destinatarios "CC"
        foreach ($cc as $mail) {
            $dom = Str::afterLast($mail, '@');                                      // Igual que arriba, extraemos dominio
            $g[$dom]['cc'][] = $mail;                                               // Añadimos este correo dentro de la lista 'cc' del dominio correspondiente
        }

        // Ajuste: si un dominio no tiene To pero sí CC, mover todos los CC a To
        foreach ($g as $dominio => &$destinos) {
            if (empty($destinos['to'] ?? []) && !empty($destinos['cc'] ?? [])) {
                $destinos['to'] = $destinos['cc'];                                  // Mover todo a To
                $destinos['cc'] = [];                                               // Vaciar CC
            }
        }
        unset($destinos);                                                           // Buena práctica al usar referencias en foreach

        return $g;
    }
}
