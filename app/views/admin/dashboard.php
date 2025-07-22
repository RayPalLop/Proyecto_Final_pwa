<?php
/**
 * Archivo: dashboard.php
 * Ubicación: /app/views/admin/dashboard.php
 * Descripción: Vista específica del dashboard para el rol de Administrador.
 * Muestra un resumen y enlaces a los módulos de gestión.
 * Esta vista será incluida por public/dashboard.php (o un controlador específico para el dashboard).
 */

// Asegurarse de que la sesión esté iniciada y el usuario sea administrador.
// Esta verificación ya se hace en public/dashboard.php, pero es una buena práctica defensiva.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Administrador') {
    header('Location: index.php'); // Redirigir al login si no es administrador
    exit();
}

// Se asume que $page_title ya está definido en el script que incluye esta vista (ej. public/dashboard.php)
// Si no, puedes definirlo aquí: $page_title = 'Dashboard de Administrador';

// Incluir el encabezado de la página (que incluye la barra lateral)
// La ruta es relativa desde public/dashboard.php (el script que probablemente incluye esta vista)
include '../app/views/shared/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4">Dashboard de Administrador</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    

    <div class="row">
        <!-- Tarjeta de Miembros -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Miembros
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <!-- Aquí podrías mostrar un contador de miembros, por ejemplo -->
                                <i class="fas fa-users me-2"></i> Gestión Completa
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="miembros.php" class="small text-primary stretched-link">Ver y Gestionar Miembros <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Instructores -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Instructores
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-user-tie me-2"></i> Gestión Completa
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="instructores.php" class="small text-success stretched-link">Ver y Gestionar Instructores <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Clases -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Clases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-calendar-alt me-2"></i> Gestión Completa
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="clases.php" class="small text-info stretched-link">Ver y Gestionar Clases <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Instalaciones -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Instalaciones
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-building me-2"></i> Gestión Completa
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="instalaciones.php" class="small text-warning stretched-link">Ver y Gestionar Instalaciones <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tarjeta de Reservas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Reservas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-book-open me-2"></i> Gestión Completa
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="reservas.php" class="small text-danger stretched-link">Ver y Gestionar Reservas <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Gestión de Usuarios -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Usuarios
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-user-cog me-2"></i> Gestión de Acceso
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-cog fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="usuarios.php" class="small text-dark stretched-link">Ver y Gestionar Usuarios <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
// Incluir el pie de página
include '../app/views/shared/footer.php';
?>
