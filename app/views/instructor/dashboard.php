<?php
/**
 * Archivo: dashboard.php
 * Ubicación: /app/views/instructor/dashboard.php
 * Descripción: Vista específica del dashboard para el rol de Instructor.
 * Muestra un mensaje de bienvenida y enlaces rápidos a funcionalidades de instructor.
 * Esta vista será incluida por public/dashboard.php.
 */

// Asegurarse de que la sesión esté iniciada y el usuario sea instructor.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Instructor') {
    header('Location: index.php'); // Redirigir al login si no es instructor
    exit();
}

// Se asume que $page_title ya está definido en el script que incluye esta vista (ej. public/dashboard.php)
// Si no, puedes definirlo aquí: $page_title = 'Dashboard de Instructor';

// Incluir el encabezado de la página (que incluye la barra lateral)
// La ruta es relativa desde public/dashboard.php (el script que incluye esta vista)
include '../app/views/shared/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4 text-white">Dashboard de Instructor</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php" class="text-white">Dashboard</a></li>
        <li class="breadcrumb-item active text-white">Resumen</li>
    </ol>

    <div class="alert alert-info alert-dismissible fade show" role="alert">
        ¡Bienvenido, instructor **<?php echo htmlspecialchars($_SESSION['correo'] ?? 'Instructor'); ?>**! Aquí puedes gestionar tus clases y horarios.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="row">
        <!-- Tarjeta para Mis Clases -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Clases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-chalkboard-teacher me-2"></i> Mis Clases
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="mis_clases.php" class="small text-primary stretched-link">Ver mis Clases <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Tarjeta para Mis Horarios -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Horarios
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-clock me-2"></i> Mis Horarios
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="mis_horarios.php" class="small text-success stretched-link">Ver mi Horario <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Aquí podrías añadir más tarjetas para otras funcionalidades del instructor -->
    </div>

</div>

<?php
// Incluir el pie de página
include '../app/views/shared/footer.php';
?>
