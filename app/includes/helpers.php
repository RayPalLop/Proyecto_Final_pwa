<?php
/**
 * Archivo: helpers.php
 * Ubicación: /app/includes/helpers.php
 * Descripción: Contiene funciones de ayuda adicionales y utilidades que complementan
 * las funciones generales y pueden ser usadas en toda la aplicación.
 */

/**
 * Verifica si el usuario actualmente logueado tiene un rol específico.
 * @param string $required_role El nombre del rol que se desea verificar (ej. 'Administrador', 'Instructor', 'Miembro').
 * @return bool True si el usuario tiene el rol requerido, false en caso contrario o si no hay sesión.
 */
function has_role($required_role) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['rol_nombre']) && $_SESSION['rol_nombre'] === $required_role;
}

/**
 * Obtiene el nombre del rol del usuario actualmente logueado.
 * @return string El nombre del rol del usuario, o 'Invitado' si no hay sesión.
 */
function get_user_role() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['rol_nombre'] ?? 'Invitado';
}

/**
 * Formatea una fecha y hora a un formato legible.
 * @param string $datetime La fecha y hora en formato de base de datos (YYYY-MM-DD HH:MM:SS).
 * @param string $format El formato deseado (por defecto 'd/m/Y H:i').
 * @return string La fecha y hora formateada, o una cadena vacía si la entrada es inválida.
 */
function format_datetime($datetime, $format = 'd/m/Y H:i') {
    if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
        return '';
    }
    try {
        $date = new DateTime($datetime);
        return $date->format($format);
    } catch (Exception $e) {
        error_log("Error al formatear fecha: " . $e->getMessage() . " para el valor: " . $datetime);
        return ''; // Devuelve vacío o un mensaje de error si la fecha es inválida
    }
}

/**
 * Formatea una fecha a un formato legible.
 * @param string $date La fecha en formato de base de datos (YYYY-MM-DD).
 * @param string $format El formato deseado (por defecto 'd/m/Y').
 * @return string La fecha formateada, o una cadena vacía si la entrada es inválida.
 */
function format_date($date, $format = 'd/m/Y') {
    if (empty($date) || $date === '0000-00-00') {
        return '';
    }
    try {
        $d = new DateTime($date);
        return $d->format($format);
    } catch (Exception $e) {
        error_log("Error al formatear fecha: " . $e->getMessage() . " para el valor: " . $date);
        return '';
    }
}

// Puedes añadir más funciones de ayuda aquí según las necesidades del proyecto:
// - Funciones para generar URLs amigables
// - Funciones para paginación
// - Funciones para manejo de archivos (subida, eliminación)
// - Funciones para enviar correos electrónicos, etc.

?>
