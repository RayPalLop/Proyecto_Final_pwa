<?php
/**
 * Archivo: mis_horarios.php
 * Ubicación: /public/mis_horarios.php
 * Descripción: Punto de entrada para que los instructores consulten su horario de clases.
 * Requiere autenticación de instructor para acceder.
 * Dirige las solicitudes al InstructorController.
 */

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

// Determinar la acción a realizar. Por ahora, solo tenemos 'misHorarios'.
$action = $_GET['action'] ?? 'misHorarios'; // Por defecto, mostrar el horario

// Manejar mensajes de éxito o error de la sesión (ej. después de un redirect)
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message']); // Limpiar el mensaje después de mostrarlo
unset($_SESSION['error_message']);

// Enrutamiento de acciones
switch ($action) {
    case 'misHorarios':
        $controller->misHorarios(); // Mostrar el horario de clases
        break;
    // Aquí se podrían añadir otras acciones específicas del horario si las hubiera
    default:
        // Si la acción no es reconocida, redirigir al dashboard del instructor.
        $_SESSION['error_message'] = 'Acción no válida.';
        header('Location: dashboard.php'); // Redirige al dashboard del instructor
        exit();
        break;
}
