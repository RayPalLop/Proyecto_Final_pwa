<?php
/**
 * Archivo: mis_clases.php
 * Ubicación: /public/mis_clases.php
 * Descripción: Punto de entrada para que los instructores consulten sus clases asignadas.
 * Requiere autenticación de instructor para acceder.
 * Dirige las solicitudes al InstructorController.
 */

// HABILITAR REPORTE DE ERRORES PARA DEPURACIÓN (ELIMINAR EN PRODUCCIÓN)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir el archivo de configuración para la conexión a la base de datos y la gestión de sesión.
require_once '../config/config.php';
// Incluir el controlador específico para las acciones del Instructor.
require_once '../app/controllers/InstructorController.php';
// Incluir funciones de ayuda para verificar roles y formatear fechas
require_once '../app/includes/helpers.php';

// Verificar si el usuario ha iniciado sesión y si tiene el rol de Instructor.
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Instructor') {
    // Si no está autenticado o no es instructor, redirigir al login.
    header('Location: index.php');
    exit();
}

// Crear una instancia del controlador de Instructor, pasándole la conexión PDO.
$controller = new InstructorController($pdo);

// Determinar la acción a realizar. Por ahora, solo tenemos 'misClases'.
$action = $_GET['action'] ?? 'misClases'; // Por defecto, mostrar las clases

// Manejar mensajes de éxito o error de la sesión (ej. después de un redirect)
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message']); // Limpiar el mensaje después de mostrarlo
unset($_SESSION['error_message']);

// Enrutamiento de acciones
switch ($action) {
    case 'misClases':
        $controller->misClases(); // Mostrar las clases asignadas
        break;
    // Aquí se podrían añadir otras acciones específicas si las hubiera
    default:
        // Si la acción no es reconocida, redirigir al dashboard del instructor.
        $_SESSION['error_message'] = 'Acción no válida.';
        header('Location: dashboard.php'); // Redirige al dashboard del instructor
        exit();
        break;
}
