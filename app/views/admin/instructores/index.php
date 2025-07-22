<?php
/**
 * Archivo: index.php
 * Ubicación: /app/views/admin/instructores/index.php
 * Descripción: Vista para mostrar la lista de todos los instructores del gimnasio.
 * Incluye opciones para añadir, editar y eliminar instructores.
 * Esta vista es incluida por InstructoresController::index().
 */

// Asegurarse de que la sesión esté iniciada y el usuario sea administrador.
// Esta verificación ya se hace en public/instructores.php, pero es una buena práctica defensiva.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Administrador') {
    header('Location: index.php'); // Redirigir al login si no es administrador
    exit();
}

// $instructores se espera que venga del controlador ($this->instructorModel->getAll())
// $page_title se espera que venga del controlador
// $success_message y $error_message vienen de public/instructores.php

// Incluir el encabezado de la página (que incluye la barra lateral)
// La ruta es relativa desde public/instructores.php
include '../app/views/shared/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4">Gestión de Instructores</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Instructores</li>
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
                <i class="fas fa-user-tie me-1"></i>
                Listado de Instructores
            </div>
            <a href="instructores.php?action=create" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle me-1"></i> Añadir Nuevo Instructor
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="instructoresTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Especialidad</th>
                            <th>Fecha Contratación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($instructores)): ?>
                            <?php foreach ($instructores as $instructor): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($instructor->id); ?></td>
                                    <td><?php echo htmlspecialchars($instructor->nombre); ?></td>
                                    <td><?php echo htmlspecialchars($instructor->correo); ?></td>
                                    <td><?php echo htmlspecialchars($instructor->especialidad); ?></td>
                                    <td><?php echo htmlspecialchars($instructor->fecha_contratacion); ?></td>
                                    <td>
                                        <a href="instructores.php?action=edit&id=<?php echo htmlspecialchars($instructor->id); ?>" class="btn btn-warning btn-sm me-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo htmlspecialchars($instructor->id); ?>" title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No hay instructores registrados.</td>
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
                ¿Estás seguro de que deseas eliminar este instructor? Esta acción no se puede deshacer.
                Ten en cuenta que si el instructor tiene clases asignadas, la eliminación podría fallar.
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
            var instructorId = button.getAttribute('data-id');
            // Actualizar el enlace del botón de confirmación del modal
            var confirmDeleteButton = deleteModal.querySelector('#confirmDeleteButton');
            confirmDeleteButton.href = 'instructores.php?action=delete&id=' + instructorId;
        });
    });
</script>
