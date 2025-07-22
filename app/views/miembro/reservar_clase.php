<?php
/**
 * Archivo: reservar_clase.php
 * Ubicación: /app/views/miembro/reservar_clase.php
 * Descripción: Vista para que los miembros vean las clases disponibles y realicen una reserva.
 * Esta vista es incluida por MiembroController::reservarClase().
 */

// Asegurarse de que la sesión esté iniciada y el usuario sea miembro.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Miembro') {
    header('Location: index.php'); // Redirigir al login si no es miembro
    exit();
}

// $clases_disponibles, $errors, $success_message se esperan del controlador.
// $page_title se espera del controlador.

// Incluir el encabezado de la página (que incluye la barra lateral)
include '../app/views/shared/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4">Reservar una Clase</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Reservar Clase</li>
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
            <i class="fas fa-calendar-plus me-1"></i>
            Clases Disponibles para Reserva
        </div>
        <div class="card-body">
            <?php if (!empty($clases_disponibles)): ?>
                <form action="reservar_clase.php" method="POST">
                    <div class="mb-3">
                        <label for="clase_id" class="form-label">Selecciona una Clase</label>
                        <select class="form-select" id="clase_id" name="clase_id" required>
                            <option value="">-- Selecciona una clase --</option>
                            <?php foreach ($clases_disponibles as $clase): ?>
                                <option value="<?php echo htmlspecialchars($clase->id); ?>">
                                    <?php echo htmlspecialchars($clase->nombre); ?> (<?php echo htmlspecialchars($clase->tipo); ?>) -
                                    Instructor: <?php echo htmlspecialchars($clase->instructor_nombre); ?> -
                                    Fecha: <?php echo htmlspecialchars(format_datetime($clase->fecha_hora)); ?> -
                                    Cupo: <?php echo htmlspecialchars($clase->cupo_actual); ?>/<?php echo htmlspecialchars($clase->cupo_maximo); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Confirmar Reserva</button>
                </form>
            <?php else: ?>
                <div class="alert alert-warning" role="alert">
                    No hay clases disponibles para reservar en este momento.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Incluir el pie de página
include '../app/views/shared/footer.php';
?>
