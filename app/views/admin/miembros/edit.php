<?php
/**
 * Archivo: edit.php
 * Ubicación: /app/views/admin/miembros/edit.php
 * Descripción: Vista para mostrar el formulario de edición de un miembro existente.
 * Los datos del miembro se precargan en el formulario.
 * Esta vista es incluida por MiembrosController::edit().
 */

// Asegurarse de que la sesión esté iniciada y el usuario sea administrador.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Administrador') {
    header('Location: index.php'); // Redirigir al login si no es administrador
    exit();
}

// $miembro, $errors, $success_message se esperan del controlador.
// $page_title se espera del controlador.

// Si por alguna razón el miembro no está definido (ej. ID inválido), redirigir.
if (!isset($miembro) || !$miembro) {
    $_SESSION['error_message'] = 'Miembro no encontrado para edición.';
    header('Location: miembros.php?action=index');
    exit();
}

// Incluir el encabezado de la página (que incluye la barra lateral)
include '../app/views/shared/header.php';
?>

<!-- Estilos CSS para corregir el color del texto en los inputs -->
<style>
    /*
     * Se aplica un color de texto negro a los campos de formulario (`.form-control`)
     * y a los menús desplegables (`.form-select`).
     * El `!important` se usa para asegurar que este estilo anule cualquier
     * otro que pueda estar causando el problema de visibilidad.
     */
    .form-control,
    .form-select {
        color: #000000 !important; /* Color de texto negro */
    }
</style>

<div class="container-fluid">
    <h1 class="mt-4">Editar Miembro: <?php echo htmlspecialchars($miembro->nombre); ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="miembros.php?action=index">Miembros</a></li>
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
            Formulario de Edición de Miembro
        </div>
        <div class="card-body">
            <form action="miembros.php?action=edit&id=<?php echo htmlspecialchars($miembro->id); ?>" method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre Completo</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($miembro->nombre); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($miembro->correo); ?>" required>
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
                    <select class="form-select" id="genero" name="genero" required>
                        <option value="">Selecciona...</option>
                        <option value="Masculino" <?php echo ($miembro->genero === 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                        <option value="Femenino" <?php echo ($miembro->genero === 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                        <option value="Otro" <?php echo ($miembro->genero === 'Otro') ? 'selected' : ''; ?>>Otro</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Actualizar Miembro</button>
                <a href="miembros.php?action=index" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php
// Incluir el pie de página
include '../app/views/shared/footer.php';
?>
