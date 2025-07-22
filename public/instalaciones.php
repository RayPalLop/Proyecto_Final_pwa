<?php
/**
 * Archivo: instalaciones.php
 * Ubicación: /public/instalaciones.php
 * Descripción: Punto de entrada para la gestión de instalaciones del gimnasio.
 * Requiere autenticación de administrador para acceder.
 * Dirige las solicitudes a las acciones del InstalacionesController.
 */

// Include the database configuration file and session management.
require_once '../config/config.php';
// Include the facilities controller.
require_once '../app/controllers/InstalacionesController.php';

// Check if the user is logged in and has the Administrator role.
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Administrador') {
    // If not authenticated or not an administrator, redirect to login.
    header('Location: index.php');
    exit();
}

// Create an instance of the facilities controller, passing the PDO connection.
$controller = new InstalacionesController($pdo);

// Determine the action to perform. By default, display the list of facilities.
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null; // Get the ID if present in the URL

// Handle success or error messages from the session (e.g., after a redirect)
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message']); // Clear the message after displaying it
unset($_SESSION['error_message']);

// Action routing
switch ($action) {
    case 'index':
        $controller->index(); // Display the list of facilities
        break;
    case 'create':
        $controller->create(); // Display/process creation form
        break;
    case 'edit':
        if ($id) {
            $controller->edit($id); // Display/process edit form
        } else {
            $_SESSION['error_message'] = 'ID de instalación no especificado para edición.';
            header('Location: instalaciones.php?action=index');
            exit();
        }
        break;
    case 'delete':
        if ($id) {
            $controller->delete($id); // Delete facility
        } else {
            $_SESSION['error_message'] = 'ID de instalación no especificado para eliminación.';
            header('Location: instalaciones.php?action=index');
            exit();
        }
        break;
    default:
        // If the action is not recognized, redirect to the index or show a 404 error.
        $_SESSION['error_message'] = 'Acción no válida.';
        header('Location: instalaciones.php?action=index');
        exit();
        break;
}
