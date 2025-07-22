<?php
/**
 * Archivo: create.php
 * Ubicación: /app/views/admin/usuarios/create.php
 * Descripción: Vista para mostrar el formulario de creación de un nuevo usuario.
 * Permite asignar un rol al nuevo usuario.
 * Esta vista es incluida por UsuariosController::create().
 */

// Asegurarse de que la sesión esté iniciada y el usuario sea administrador.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Administrador') {
    header('Location: index.php'); // Redirigir al login si no es administrador
    exit();
}

// $errors, $success_message y $roles se esperan del controlador.
// $page_title se espera del controlador.

// Incluir el encabezado de la página (que incluye la barra lateral)
include '../app/views/shared/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4">Añadir Nuevo Usuario</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="usuarios.php?action=index">Usuarios</a></li>
        <li class="breadcrumb-item active">Añadir Nuevo</li>
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
            <i class="fas fa-user-plus me-1"></i>
            Formulario de Nuevo Usuario
        </div>
        <div class="card-body">
            <form action="usuarios.php?action=create" method="POST">
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($correo ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="contrasena" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                    <small class="form-text text-muted">Mínimo 6 caracteres.</small>
                </div>
                <div class="mb-3">
                    <label for="confirm_contrasena" class="form-label">Confirmar Contraseña</label>
                    <input type="password" class="form-control" id="confirm_contrasena" name="confirm_contrasena" required>
                </div>
                <div class="mb-3">
                    <label for="rol_id" class="form-label">Rol</label>
                    <select class="form-select" id="rol_id" name="rol_id" required>
                        <option value="">Selecciona un rol...</option>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?php echo htmlspecialchars($rol->id); ?>" <?php echo (isset($rol_id) && $rol_id == $rol->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($rol->nombre); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Guardar Usuario</button>
                <a href="usuarios.php?action=index" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php
// Incluir el pie de página
include '../app/views/shared/footer.php';
?>
