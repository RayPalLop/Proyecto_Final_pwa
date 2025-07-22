<?php
/**
 * Archivo: create.php
 * Ubicación: /app/views/admin/reservas/create.php
 * Descripción: Vista para mostrar el formulario de creación de una nueva reserva.
 * Permite seleccionar miembro y clase.
 * Esta vista es incluida por ReservasController::create().
 */

// Asegurarse de que la sesión esté iniciada y el usuario sea administrador.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Administrador') {
    header('Location: index.php'); // Redirigir al login si no es administrador
    exit();
}

// $errors, $success_message, $miembros, $clases se esperan del controlador.
// $page_title se espera del controlador.

// Incluir el encabezado de la página (que incluye la barra lateral)
include '../app/views/shared/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4">Añadir Nueva Reserva</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="reservas.php?action=index">Reservas</a></li>
        <li class="breadcrumb-item active">Añadir Nueva</li>
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
            <i class="fas fa-plus-circle me-1"></i>
            Formulario de Nueva Reserva
        </div>
        <div class="card-body">
            <form action="reservas.php?action=create" method="POST">
                <div class="mb-3">
                    <label for="miembro_id" class="form-label">Miembro</label>
                    <select class="form-select" id="miembro_id" name="miembro_id" required>
                        <option value="">Selecciona un miembro...</option>
                        <?php foreach ($miembros as $miembro): ?>
                            <option value="<?php echo htmlspecialchars($miembro->id); ?>" <?php echo (isset($miembro_id) && $miembro_id == $miembro->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($miembro->nombre); ?> (<?php echo htmlspecialchars($miembro->correo); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="clase_id" class="form-label">Clase</label>
                    <select class="form-select" id="clase_id" name="clase_id" required>
                        <option value="">Selecciona una clase...</option>
                        <?php foreach ($clases as $clase): ?>
                            <option value="<?php echo htmlspecialchars($clase->id); ?>" <?php echo (isset($clase_id) && $clase_id == $clase->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($clase->nombre); ?> (<?php echo htmlspecialchars($clase->fecha_hora); ?>) - Instructor: <?php echo htmlspecialchars($clase->instructor_nombre); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado de la Reserva</label>
                    <select class="form-select" id="estado" name="estado" required>
                        <option value="Confirmada" <?php echo (isset($estado) && $estado === 'Confirmada') ? 'selected' : ''; ?>>Confirmada</option>
                        <option value="Cancelada" <?php echo (isset($estado) && $estado === 'Cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                        <option value="Completada" <?php echo (isset($estado) && $estado === 'Completada') ? 'selected' : ''; ?>>Completada</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Guardar Reserva</button>
                <a href="reservas.php?action=index" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php
// Incluir el pie de página
include '../app/views/shared/footer.php';
?>
