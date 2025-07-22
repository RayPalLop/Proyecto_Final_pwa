<?php
/**
 * Archivo: perfil.php
 * Ubicación: /app/views/miembro/perfil.php
 * Descripción: Vista para que los miembros editen su información de perfil.
 * Los datos del miembro se precargan en el formulario.
 * Esta vista es incluida por MiembroController::miPerfil().
 */

// Asegurarse de que la sesión esté iniciada y el usuario sea miembro.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Miembro') {
    header('Location: index.php'); // Redirigir al login si no es miembro
    exit();
}

// $miembro, $errors, $success_message se esperan del controlador.
// $page_title se espera del controlador.

// Si por alguna razón el miembro no está definido (ej. ID inválido), redirigir.
if (!isset($miembro) || !$miembro) {
    $_SESSION['error_message'] = 'No se pudo cargar tu perfil. Contacta al administrador.';
    header('Location: dashboard.php'); // Redirigir al dashboard si no se encuentra el perfil
    exit();
}

// Incluir el encabezado de la página (que incluye la barra lateral)
include '../app/views/shared/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4">Mi Perfil</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Mi Perfil</li>
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
            <i class="fas fa-user-circle me-1"></i>
            Editar Información de Perfil
        </div>
        <div class="card-body">
            <form action="perfil.php" method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre Completo</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($miembro->nombre); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($_SESSION['correo'] ?? ''); ?>" required>
                    <small class="form-text text-muted">Este es el correo que usas para iniciar sesión.</small>
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
                    <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($miembro->fecha_nacimiento); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="genero" class="form-label">Género</label>
                    <?php
                    $genero_options = ['Masculino', 'Femenino', 'Otro'];
                    ?>
                    <select class="form-select" id="genero" name="genero" required>
                        <option value="">Selecciona...</option>
                        <?php foreach ($genero_options as $option): ?>
                            <option value="<?php echo htmlspecialchars($option); ?>" <?php echo ($miembro->genero === $option) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($option); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Actualizar Perfil</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php
// Incluir el pie de página
include '../app/views/shared/footer.php';
?>
