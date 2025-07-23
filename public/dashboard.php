<?php
/**
 * Archivo: dashboard.php
 * Ubicación: /public/dashboard.php
 * Descripción: Página del dashboard principal.
 * Redirige a la página de login si el usuario no está autenticado.
 * Incluye la vista del dashboard específica según el rol del usuario.
 * ACTUALIZADO: Obtiene datos para el gráfico de clases más populares.
 */

// HABILITAR REPORTE DE ERRORES PARA DEPURACIÓN (ELIMINAR EN PRODUCCIÓN)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir el archivo de configuración para asegurar que la sesión esté iniciada
// y que la conexión PDO esté disponible si fuera necesaria.
require_once '../config/config.php';
// Incluir las funciones de ayuda para verificar roles
require_once '../app/includes/helpers.php';
// Incluir el modelo de Reserva para obtener datos para el gráfico
require_once '../app/models/Reserva.php';


// Verificar si el usuario NO ha iniciado sesión.
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php'); // Redirige a la página de login
    exit(); // Termina la ejecución del script
}

// Obtener el nombre del usuario y el rol de la sesión para mostrarlos.
$user_email = htmlspecialchars($_SESSION['correo'] ?? 'Usuario');
$user_role = htmlspecialchars($_SESSION['rol_nombre'] ?? 'Desconocido');

// Establecer un título para la página que se usará en el header.
$page_title = 'Dashboard Principal';

// Inicializar datos para el gráfico (solo para el administrador)
$top_classes_data = [];
if ($user_role === 'Administrador') {
    $reservaModel = new Reserva($pdo);
    $top_classes_data = $reservaModel->getTopClassesByReservations(5); // Obtener las 5 clases más populares
}

// Incluir el encabezado de la página (que incluye la barra lateral)
include '../app/views/shared/header.php';

// Cargar la vista del dashboard según el rol del usuario
switch ($user_role) {
    case 'Administrador':
        // Pasar los datos del gráfico a la vista del administrador
        include '../app/views/admin/dashboard.php';
        break;
    case 'Instructor':
        // Incluir la vista del dashboard del instructor
        include '../app/views/instructor/dashboard.php';
        break;
    case 'Miembro':
        // Incluir la vista del dashboard del miembro
        include '../app/views/miembro/dashboard.php';
        break;
    default:
        // Si el rol no es reconocido o no tiene una vista específica
        echo '<div class="container-fluid"><div class="alert alert-warning mt-4" role="alert">Tu rol no tiene un dashboard específico aún.</div></div>';
        // Incluir el footer si no hay una vista específica que lo haga
        include '../app/views/shared/footer.php';
        break;
}
?>
