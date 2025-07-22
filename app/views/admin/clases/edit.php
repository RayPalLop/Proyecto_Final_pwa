<?php
/**
 * Archivo: edit.php
 * Ubicación: /app/views/admin/clases/edit.php
 * Descripción: Vista para mostrar el formulario de edición de una clase existente.
 * Los datos de la clase se precargan en el formulario.
 * Esta vista es incluida por ClasesController::edit().
 */

// Asegurarse de que la sesión esté iniciada y el usuario sea administrador.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Administrador') {
    header('Location: index.php'); // Redirigir al login si no es administrador
    exit();
}

// $clase, $errors, $success_message, $instructores, $instalaciones se esperan del controlador.
// $page_title se espera del controlador.

// Si por alguna razón la clase no está definida (ej. ID inválido), redirigir.
if (!isset($clase) || !$clase) {
    $_SESSION['error_message'] = 'Clase no encontrada para edición.';
    header('Location: clases.php?action=index');
    exit();
}

// Formatear la fecha y hora para el input datetime-local
$clase_fecha_hora_formatted = date('Y-m-d\TH:i', strtotime($clase->fecha_hora));

// Incluir el encabezado de la página (que incluye la barra lateral)
include '../app/views/shared/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4">Editar Clase: <?php echo htmlspecialchars($clase->nombre); ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="clases.php?action=index">Clases</a></li>
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
            Formulario de Edición de Clase
        </div>
        <div class="card-body">
            <form action="clases.php?action=edit&id=<?php echo htmlspecialchars($clase->id); ?>" method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre de la Clase</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($clase->nombre); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo de Clase</label>
                    <input type="text" class="form-control" id="tipo" name="tipo" value="<?php echo htmlspecialchars($clase->tipo); ?>" placeholder="Ej: Cardio, Fuerza, Yoga" required>
                </div>
                <div class="mb-3">
                    <label for="instructor_id" class="form-label">Instructor</label>
                    <select class="form-select" id="instructor_id" name="instructor_id" required>
                        <option value="">Selecciona un instructor...</option>
                        <?php foreach ($instructores as $instructor): ?>
                            <option value="<?php echo htmlspecialchars($instructor->id); ?>" <?php echo ($clase->instructor_id == $instructor->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($instructor->nombre); ?> (<?php echo htmlspecialchars($instructor->especialidad); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="instalacion_id" class="form-label">Instalación</label>
                    <select class="form-select" id="instalacion_id" name="instalacion_id" required>
                        <option value="">Selecciona una instalación...</option>
                        <?php foreach ($instalaciones as $instalacion): ?>
                            <option value="<?php echo htmlspecialchars($instalacion->id); ?>" <?php echo ($clase->instalacion_id == $instalacion->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($instalacion->nombre); ?> (Capacidad: <?php echo htmlspecialchars($instalacion->capacidad); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="fecha_hora" class="form-label">Fecha y Hora</label>
                    <input type="datetime-local" class="form-control" id="fecha_hora" name="fecha_hora" value="<?php echo htmlspecialchars($clase_fecha_hora_formatted); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="duracion_minutos" class="form-label">Duración (minutos)</label>
                    <input type="number" class="form-control" id="duracion_minutos" name="duracion_minutos" value="<?php echo htmlspecialchars($clase->duracion_minutos); ?>" min="1" required>
                </div>
                <div class="mb-3">
                    <label for="cupo_maximo" class="form-label">Cupo Máximo</label>
                    <input type="number" class="form-control" id="cupo_maximo" name="cupo_maximo" value="<?php echo htmlspecialchars($clase->cupo_maximo); ?>" min="1" required>
                </div>
                <button type="submit" class="btn btn-success">Actualizar Clase</button>
                <a href="clases.php?action=index" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php
// Incluir el pie de página
include '../app/views/shared/footer.php';
?>
