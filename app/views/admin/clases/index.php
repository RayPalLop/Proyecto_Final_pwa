<?php
/**
 * Archivo: index.php
 * Ubicación: /app/views/admin/clases/index.php
 * Descripción: Vista para mostrar la lista de todas las clases del gimnasio.
 * Incluye opciones para añadir, editar y eliminar clases.
 * Esta vista es incluida por ClasesController::index().
 */

// Asegurarse de que la sesión esté iniciada y el usuario sea administrador.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Administrador') {
    header('Location: index.php'); // Redirigir al login si no es administrador
    exit();
}

// $clases se espera que venga del controlador ($this->claseModel->getAll())
// $page_title se espera que venga del controlador
// $success_message y $error_message vienen de public/clases.php

// Incluir el encabezado de la página (que incluye la barra lateral)
// La ruta es relativa desde public/clases.php
include '../app/views/shared/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4">Gestión de Clases</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Clases</li>
    </ol>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-calendar-alt me-1"></i>
                Listado de Clases
            </div>
            <a href="clases.php?action=create" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle me-1"></i> Añadir Nueva Clase
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="clasesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Instructor</th>
                            <th>Instalación</th>
                            <th>Fecha y Hora</th>
                            <th>Duración (min)</th>
                            <th>Cupo Máximo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($clases)): ?>
                            <?php foreach ($clases as $clase): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($clase->id); ?></td>
                                    <td><?php echo htmlspecialchars($clase->nombre); ?></td>
                                    <td><?php echo htmlspecialchars($clase->tipo); ?></td>
                                    <td><?php echo htmlspecialchars($clase->instructor_nombre); ?></td>
                                    <td><?php echo htmlspecialchars($clase->instalacion_nombre); ?></td>
                                    <td><?php echo htmlspecialchars($clase->fecha_hora); ?></td>
                                    <td><?php echo htmlspecialchars($clase->duracion_minutos); ?></td>
                                    <td><?php echo htmlspecialchars($clase->cupo_maximo); ?></td>
                                    <td>
                                        <a href="clases.php?action=edit&id=<?php echo htmlspecialchars($clase->id); ?>" class="btn btn-warning btn-sm me-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo htmlspecialchars($clase->id); ?>" title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No hay clases registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar esta clase? Esta acción no se puede deshacer.
                Ten en cuenta que si hay reservas asociadas a esta clase, la eliminación podría fallar.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a id="confirmDeleteButton" href="#" class="btn btn-danger">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el pie de página
include '../app/views/shared/footer.php';
?>

<script>
    // Script para manejar el modal de eliminación
    document.addEventListener('DOMContentLoaded', function() {
        var deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            // Botón que disparó el modal
            var button = event.relatedTarget;
            // Extraer información de los atributos data-*
            var claseId = button.getAttribute('data-id');
            // Actualizar el enlace del botón de confirmación del modal
            var confirmDeleteButton = deleteModal.querySelector('#confirmDeleteButton');
            confirmDeleteButton.href = 'clases.php?action=delete&id=' + claseId;
        });
    });
</script>
