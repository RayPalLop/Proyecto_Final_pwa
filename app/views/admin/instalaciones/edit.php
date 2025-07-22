<?php
/**
 * Archivo: edit.php
 * Ubicación: /app/views/admin/instalaciones/edit.php
 * Descripción: Vista para mostrar el formulario de edición de una instalación existente.
 * Los datos de la instalación se precargan en el formulario.
 * Esta vista es incluida por InstalacionesController::edit().
 */

// Ensure session is started and user is administrator.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Administrador') {
    header('Location: index.php'); // Redirect to login if not administrator
    exit();
}

// $instalacion, $errors, $success_message are expected from the controller.
// $page_title is expected from the controller.

// If for some reason the facility is not defined (e.g., invalid ID), redirect.
if (!isset($instalacion) || !$instalacion) {
    $_SESSION['error_message'] = 'Instalación no encontrada para edición.';
    header('Location: instalaciones.php?action=index');
    exit();
}

// Include the page header (which includes the sidebar)
include '../app/views/shared/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4">Editar Instalación: <?php echo htmlspecialchars($instalacion->nombre); ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="instalaciones.php?action=index">Instalaciones</a></li>
        <li class="breadcrumb-item active">Editar</li>
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
            <i class="fas fa-edit me-1"></i>
            Formulario de Edición de Instalación
        </div>
        <div class="card-body">
            <form action="instalaciones.php?action=edit&id=<?php echo htmlspecialchars($instalacion->id); ?>" method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre de la Instalación</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($instalacion->nombre); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo de Instalación</label>
                    <input type="text" class="form-control" id="tipo" name="tipo" value="<?php echo htmlspecialchars($instalacion->tipo); ?>" placeholder="Ej: Sala, Piscina, Estudio" required>
                </div>
                <div class="mb-3">
                    <label for="capacidad" class="form-label">Capacidad</label>
                    <input type="number" class="form-control" id="capacidad" name="capacidad" value="<?php echo htmlspecialchars($instalacion->capacidad); ?>" min="1" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($instalacion->descripcion); ?></textarea>
                </div>
                <button type="submit" class="btn btn-success">Actualizar Instalación</button>
                <a href="instalaciones.php?action=index" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php
// Include the footer
include '../app/views/shared/footer.php';
?>
