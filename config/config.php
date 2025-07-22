<?php
/**
 * Archivo: config.php
 * Ubicación: /config/config.php
 * Descripción: Contiene la configuración de la base de datos y establece la conexión PDO.
 * Ahora utiliza la clase Database para gestionar la conexión.
 * También inicia la sesión PHP para la gestión de usuarios.
 */

// Iniciar la sesión PHP si aún no está iniciada.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Definición de constantes para la conexión a la base de datos
// Asegúrate de que estos valores coincidan con tu configuración de MySQL.
define('DB_HOST', 'localhost');      // Host de la base de datos (generalmente 'localhost')
define('DB_NAME', 'system_gym_db');  // Nombre de la base de datos que ya creaste
define('DB_USER', 'root');           // Usuario de la base de datos (cambiar si es diferente)
define('DB_PASS', '');               // Contraseña del usuario de la base de datos (cambiar si tienes una)

// Incluir la clase Database
require_once __DIR__ . '/../app/models/Database.php';

// Obtener la instancia de la conexión PDO a través de la clase Database
// Esto asegura que solo haya una conexión a la base de datos en toda la aplicación.
try {
    $pdo = Database::getConnection();
} catch (Exception $e) {
    // La clase Database ya maneja los errores de conexión, pero esto es un fallback.
    error_log("Error al obtener la conexión PDO: " . $e->getMessage());
    die("Error crítico de la aplicación. No se pudo conectar a la base de datos.");
}

// Opcional: Mensaje de depuración si la conexión es exitosa
// echo "Conexión a la base de datos exitosa.";
?>
