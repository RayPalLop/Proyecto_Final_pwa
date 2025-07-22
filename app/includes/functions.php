<?php
/**
 * Archivo: functions.php
 * Ubicación: /app/includes/functions.php
 * Descripción: Contiene funciones de utilidad generales que pueden ser reutilizadas
 * en diferentes partes de la aplicación para tareas comunes como sanitización,
 * redirección o manejo de mensajes.
 */

/**
 * Función para sanear una cadena de texto.
 * Utiliza htmlspecialchars para prevenir ataques XSS y trim para eliminar espacios en blanco.
 * @param string $data La cadena de texto a sanear.
 * @return string La cadena de texto saneada.
 */
function sanitize_input($data) {
    $data = trim($data); // Elimina espacios en blanco al inicio y al final
    $data = stripslashes($data); // Elimina barras invertidas
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); // Convierte caracteres especiales a entidades HTML
    return $data;
}

/**
 * Función para redirigir a una URL específica.
 * @param string $url La URL a la que se desea redirigir.
 * @param string $message Mensaje opcional para almacenar en la sesión (ej. éxito/error).
 * @param string $type Tipo de mensaje ('success', 'error', 'info', etc.).
 */
function redirect($url, $message = null, $type = 'success') {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if ($message !== null) {
        $_SESSION['message'] = ['text' => $message, 'type' => $type];
    }
    header("Location: " . $url);
    exit();
}

/**
 * Función para mostrar y limpiar mensajes de sesión.
 * Los mensajes se almacenan en $_SESSION['message'] y se eliminan después de mostrarlos.
 * @return string HTML con el mensaje formateado, o cadena vacía si no hay mensaje.
 */
function display_session_message() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $html = '';
    if (isset($_SESSION['message'])) {
        $message_text = htmlspecialchars($_SESSION['message']['text']);
        $message_type = htmlspecialchars($_SESSION['message']['type']);

        // Mapear tipos de mensaje a clases de Bootstrap
        $alert_class = 'alert-info'; // Por defecto
        switch ($message_type) {
            case 'success':
                $alert_class = 'alert-success';
                break;
            case 'error':
                $alert_class = 'alert-danger';
                break;
            case 'warning':
                $alert_class = 'alert-warning';
                break;
            case 'info':
            default:
                $alert_class = 'alert-info';
                break;
        }

        $html = "
            <div class='alert {$alert_class} alert-dismissible fade show' role='alert'>
                {$message_text}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        ";
        unset($_SESSION['message']); // Limpiar el mensaje después de mostrarlo
    }
    return $html;
}

// Puedes añadir más funciones de utilidad aquí, como:
// - Funciones de validación más complejas
// - Funciones para formatear fechas
// - Funciones para depuración
// - Funciones para manejar subidas de archivos, etc.

?>
