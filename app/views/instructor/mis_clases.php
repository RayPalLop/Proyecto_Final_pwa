<?php
/**
 * Archivo: mis_clases.php
 * Ubicación: /app/views/instructor/mis_clases.php
 * Descripción: Vista para que los instructores consulten las clases que tienen asignadas.
 * Muestra una tabla con las clases que imparten, incluyendo el cupo actual.
 * Esta vista es incluida por InstructorController::misClases().
 */

// Asegurarse de que la sesión esté iniciada y el usuario sea instructor.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Instructor') {
    header('Location: index.php'); // Redirigir al login si no es instructor
    exit();
}

// $instructor_clases, $errors, $success_message se esperan del controlador.
// $page_title se espera del controlador.

// Incluir el encabezado de la página (que incluye la barra lateral)
include '../app/views/shared/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4">Mis Clases</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Mis Clases</li>
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
            <i class="fas fa-chalkboard-teacher me-1"></i>
            Gestión de Clases Asignadas
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="misClasesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nombre de la Clase</th>
                            <th>Tipo</th>
                            <th>Fecha y Hora</th>
                            <th>Duración (min)</th>
                            <th>Instalación</th>
                            <th>Inscritos / Cupo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($instructor_clases)): ?>
                            <?php foreach ($instructor_clases as $clase): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($clase->nombre); ?></td>
                                    <td><?php echo htmlspecialchars($clase->tipo); ?></td>
                                    <td><?php echo htmlspecialchars(format_datetime($clase->fecha_hora)); ?></td> <!-- CORRECCIÓN AQUÍ -->
                                    <td><?php echo htmlspecialchars($clase->duracion_minutos); ?></td>
                                    <td><?php echo htmlspecialchars($clase->instalacion_nombre); ?></td>
                                    <td><?php echo htmlspecialchars($clase->inscritos ?? 0); ?> / <?php echo htmlspecialchars($clase->cupo_maximo); ?></td>
                                    <td>
                                        <!-- Aquí podrías añadir acciones si el instructor puede gestionar algo de la clase -->
                                        <!-- Por ejemplo, ver lista de inscritos, marcar asistencia, etc. -->
                                        <a href="#" class="btn btn-info btn-sm" title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No tienes clases asignadas en este momento.</td>
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
