<?php
/**
 * Archivo: logout.php
 * Ubicación: /public/logout.php
 * Descripción: Script para cerrar la sesión del usuario.
 * Destruye todas las variables de sesión y redirige al login.
 */

// Iniciar la sesión si aún no está iniciada.
// Es importante para poder acceder a las variables de sesión y destruirlas.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Eliminar todas las variables de sesión.
// Esto borra los datos de la sesión actual.
$_SESSION = array();

// Si se usa sesiones basadas en cookies, también es necesario eliminar la cookie de sesión.
// Nota: Esto destruirá la sesión, y no solo los datos de la sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión.
// Esto elimina el archivo de sesión en el servidor.
session_destroy();

// Redirigir al usuario a la página de inicio de sesión (index.php) después de cerrar la sesión.
header('Location: index.php');
exit(); // Asegura que no se ejecute más código después de la redirección.
?>
