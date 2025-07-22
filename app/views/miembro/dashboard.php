<?php
/**
 * Archivo: dashboard.php
 * Ubicación: /app/views/miembro/dashboard.php
 * Descripción: Vista específica del dashboard para el rol de Miembro.
 * Muestra un mensaje de bienvenida y enlaces rápidos a funcionalidades de miembro.
 * Esta vista será incluida por public/dashboard.php.
 */

// Asegurarse de que la sesión esté iniciada y el usuario sea miembro.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Miembro') {
    header('Location: index.php'); // Redirigir al login si no es miembro
    exit();
}

// Se asume que $page_title ya está definido en el script que incluye esta vista (ej. public/dashboard.php)
// Si no, puedes definirlo aquí: $page_title = 'Dashboard de Miembro';

// Incluir el encabezado de la página (que incluye la barra lateral)
// La ruta es relativa desde public/dashboard.php (el script que incluye esta vista)
include '../app/views/shared/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4">Dashboard de Miembro</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    <div class="alert alert-info" role="alert">
        ¡Bienvenido, <?php echo htmlspecialchars($_SESSION['correo'] ?? 'Miembro'); ?>! Aquí puedes gestionar tus actividades en el gimnasio.
    </div>

    <div class="row">
        <!-- Tarjeta para Reservar Clase -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Clases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-calendar-plus me-2"></i> Reservar una Clase
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="reservar_clase.php" class="small text-primary stretched-link">Explorar y Reservar <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Tarjeta para Mi Historial de Actividades -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Actividad
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-history me-2"></i> Mi Historial de Actividades
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="mis_reservas.php" class="small text-success stretched-link">Ver mis Reservas <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Tarjeta para Mi Perfil -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Perfil
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-user-circle me-2"></i> Mi Perfil
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="perfil.php" class="small text-info stretched-link">Editar mi Perfil <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
// Incluir el pie de página
include '../app/views/shared/footer.php';
?>
