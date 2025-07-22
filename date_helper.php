<?php

/**
 * Formatea una cadena de fecha y hora (ej. 'YYYY-MM-DD HH:MM:SS') a un formato localizado y legible.
 *
 * @param string|null $dateTimeString La fecha y hora en formato de base de datos.
 * @param string $locale El locale para el formato (ej. 'es_ES' para español).
 * @return string La fecha formateada o la cadena original si hay un error.
 */
function formatDateTime(?string $dateTimeString, string $locale = 'es_ES'): string
{
    if (empty($dateTimeString)) {
        return '';
    }

    try {
        // Crear un objeto DateTime a partir de la cadena
        $date = new DateTime($dateTimeString);

        // Usar IntlDateFormatter para un formato localizado y amigable
        $formatter = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::SHORT);

        return $formatter->format($date);
    } catch (Exception $e) {
        // Si la fecha no es válida, devuelve la cadena original para no romper la vista.
        return $dateTimeString;
    }
}