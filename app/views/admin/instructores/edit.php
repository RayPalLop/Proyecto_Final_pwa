<?php
/**
 * Archivo: edit.php
 * Ubicación: /app/views/admin/instructores/edit.php
 * Descripción: Vista para mostrar el formulario de edición de un instructor existente.
 * Los datos del instructor se precargan en el formulario.
 * Esta vista es incluida por InstructoresController::edit().
 */

// Asegurarse de que la sesión esté iniciada y el usuario sea administrador.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Administrador') {
    header('Location: index.php'); // Redirigir al login si no es administrador
    exit();
}

// $instructor, $errors, $success_message se esperan del controlador.
// $page_title se espera del controlador.

// Si por alguna razón el instructor no está definido (ej. ID inválido), redirigir.
if (!isset($instructor) || !$instructor) {
    $_SESSION['error_message'] = 'Instructor no encontrado para edición.';
    header('Location: instructores.php?action=index');
    exit();
}

// Incluir el encabezado de la página (que incluye la barra lateral)
include '../app/views/shared/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4">Editar Instructor: <?php echo htmlspecialchars($instructor->nombre); ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="instructores.php?action=index">Instructores</a></li>
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
            Formulario de Edición de Instructor
        </div>
        <div class="card-body">
            <form action="instructores.php?action=edit&id=<?php echo htmlspecialchars($instructor->id); ?>" method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre Completo</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($instructor->nombre); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($instructor->correo); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="contrasena" class="form-label">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                    <input type="password" class="form-control" id="contrasena" name="contrasena">
                    <small class="form-text text-muted">Mínimo 6 caracteres si se va a cambiar.</small>
                </div>
                <div class="mb-3">
                    <label for="confirm_contrasena" class="form-label">Confirmar Nueva Contraseña</label>
                    <input type="password" class="form-control" id="confirm_contrasena" name="confirm_contrasena">
                </div>
                <div class="mb-3">
                    <label for="especialidad" class="form-label">Especialidad</label>
                    <input type="text" class="form-control" id="especialidad" name="especialidad" value="<?php echo htmlspecialchars($instructor->especialidad); ?>" required>
                </div>
                <button type="submit" class="btn btn-success">Actualizar Instructor</button>
                <a href="instructores.php?action=index" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php
// Incluir el pie de página
include '../app/views/shared/footer.php';
?>
