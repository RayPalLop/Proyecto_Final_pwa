<?php
/**
 * Archivo: mis_reservas.php
 * Ubicación: /app/views/miembro/mis_reservas.php
 * Descripción: Vista para que los miembros consulten su historial de actividades (reservas de clases).
 * Muestra una tabla con todas las reservas realizadas por el miembro.
 * Esta vista es incluida por MiembroController::misReservas().
 */

// Asegurarse de que la sesión esté iniciada y el usuario sea miembro.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Miembro') {
    header('Location: index.php'); // Redirigir al login si no es miembro
    exit();
}

// $miembro_reservas, $errors, $success_message se esperan del controlador.
// $page_title se espera del controlador.

// Incluir el encabezado de la página (que incluye la barra lateral)
include '../app/views/shared/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4">Mi Historial de Actividades</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Mi Historial</li>
    </ol>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-history me-1"></i>
            Mis Reservas de Clases
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="misReservasTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Clase</th>
                            <th>Instructor</th>
                            <th>Instalación</th>
                            <th>Fecha y Hora</th>
                            <th>Duración (min)</th>
                            <th>Fecha de Reserva</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($miembro_reservas)): ?>
                            <?php foreach ($miembro_reservas as $reserva): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reserva->clase_nombre); ?></td>
                                    <td><?php echo htmlspecialchars($reserva->instructor_nombre); ?></td>
                                    <td><?php echo htmlspecialchars($reserva->instalacion_nombre); ?></td>
                                    <td><?php echo htmlspecialchars(format_datetime($reserva->clase_fecha_hora)); ?></td>
                                    <td><?php echo htmlspecialchars($reserva->duracion_minutos); ?></td>
                                    <td><?php echo htmlspecialchars(format_datetime($reserva->fecha_reserva)); ?></td>
                                    <td><?php echo htmlspecialchars($reserva->estado); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No tienes reservas de clases registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el pie de página
include '../app/views/shared/footer.php';
?>
