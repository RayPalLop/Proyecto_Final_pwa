<?php
/**
 * Archivo: reservas.php
 * Ubicación: /public/reservas.php
 * Descripción: Punto de entrada para la gestión de reservas del gimnasio.
 * Requiere autenticación de administrador para acceder.
 * Dirige las solicitudes a las acciones del ReservasController.
 */

// Incluir el archivo de configuración para la conexión a la base de datos y la gestión de sesión.
require_once '../config/config.php';
// Incluir el controlador de reservas.
require_once '../app/controllers/ReservasController.php';

// Verificar si el usuario ha iniciado sesión y si tiene el rol de Administrador.
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Administrador') {
    // Si no está autenticado o no es administrador, redirigir al login.
    header('Location: index.php');
    exit();
}

// Crear una instancia del controlador de reservas, pasándole la conexión PDO.
$controller = new ReservasController($pdo);

// Determinar la acción a realizar. Por defecto, mostrar la lista de reservas.
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null; // Obtener el ID si está presente en la URL

// Manejar mensajes de éxito o error de la sesión (ej. después de un redirect)
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message']); // Limpiar el mensaje después de mostrarlo
unset($_SESSION['error_message']);

// Enrutamiento de acciones
switch ($action) {
    case 'index':
        $controller->index(); // Mostrar la lista de reservas
        break;
    case 'create':
        $controller->create(); // Mostrar/procesar formulario de creación
        break;
    case 'edit':
        if ($id) {
            $controller->edit($id); // Mostrar/procesar formulario de edición
        } else {
            $_SESSION['error_message'] = 'ID de reserva no especificado para edición.';
            header('Location: reservas.php?action=index');
            exit();
        }
        break;
    case 'delete':
        if ($id) {
            $controller->delete($id); // Eliminar reserva
        } else {
            $_SESSION['error_message'] = 'ID de reserva no especificado para eliminación.';
            header('Location: reservas.php?action=index');
            exit();
        }
        break;
    default:
        // Si la acción no es reconocida, redirigir al índice o mostrar un error 404.
        $_SESSION['error_message'] = 'Acción no válida.';
        header('Location: reservas.php?action=index');
        exit();
        break;
}
