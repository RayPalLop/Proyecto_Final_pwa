<?php
/**
 * Archivo: perfil.php
 * Ubicación: /public/perfil.php
 * Descripción: Punto de entrada para que los miembros gestionen su perfil.
 * Requiere autenticación de miembro para acceder.
 * Dirige las solicitudes al MiembroController.
 */

// HABILITAR REPORTE DE ERRORES PARA DEPURACIÓN (ELIMINAR EN PRODUCCIÓN)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir el archivo de configuración para la conexión a la base de datos y la gestión de sesión.
require_once '../config/config.php';
// Incluir el controlador específico para las acciones del Miembro.
require_once '../app/controllers/MiembroController.php';
// Incluir funciones de ayuda para verificar roles
require_once '../app/includes/helpers.php';

// Verificar si el usuario ha iniciado sesión y si tiene el rol de Miembro.
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Miembro') {
    // Si no está autenticado o no es miembro, redirigir al login.
    header('Location: index.php');
    exit();
}

// Crear una instancia del controlador de Miembro, pasándole la conexión PDO.
$controller = new MiembroController($pdo);

// Determinar la acción a realizar. Por ahora, solo tenemos 'miPerfil'.
$action = $_GET['action'] ?? 'miPerfil'; // Por defecto, mostrar el perfil

// Manejar mensajes de éxito o error de la sesión (ej. después de un redirect)
// Estos mensajes se pasarán a la vista a través del controlador.
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message']); // Limpiar el mensaje después de mostrarlo
unset($_SESSION['error_message']);

// Enrutamiento de acciones
switch ($action) {
    case 'miPerfil':
        $controller->miPerfil(); // Mostrar/procesar el formulario de perfil
        break;
    default:
        // Si la acción no es reconocida, redirigir al dashboard del miembro.
        $_SESSION['error_message'] = 'Acción no válida.';
        header('Location: dashboard.php'); // Redirige al dashboard del miembro
        exit();
        break;
}
