<?php
/**
 * Archivo: index.php
 * Ubicación: /app/views/admin/instalaciones/index.php
 * Descripción: Vista para mostrar la lista de todas las instalaciones del gimnasio.
 * Incluye opciones para añadir, editar y eliminar instalaciones.
 * Esta vista es incluida por InstalacionesController::index().
 */

// Ensure session is started and user is administrator.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Administrador') {
    header('Location: index.php'); // Redirect to login if not administrator
    exit();
}

// $instalaciones is expected from the controller ($this->instalacionModel->getAll())
// $page_title is expected from the controller
// $success_message and $error_message come from public/instalaciones.php

// Include the page header (which includes the sidebar)
// The path is relative from public/instalaciones.php
include '../app/views/shared/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4">Gestión de Instalaciones</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Instalaciones</li>
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
                <i class="fas fa-building me-1"></i>
                Listado de Instalaciones
            </div>
            <a href="instalaciones.php?action=create" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle me-1"></i> Añadir Nueva Instalación
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="instalacionesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Capacidad</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($instalaciones)): ?>
                            <?php foreach ($instalaciones as $instalacion): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($instalacion->id); ?></td>
                                    <td><?php echo htmlspecialchars($instalacion->nombre); ?></td>
                                    <td><?php echo htmlspecialchars($instalacion->tipo); ?></td>
                                    <td><?php echo htmlspecialchars($instalacion->capacidad); ?></td>
                                    <td><?php echo htmlspecialchars($instalacion->descripcion); ?></td>
                                    <td>
                                        <a href="instalaciones.php?action=edit&id=<?php echo htmlspecialchars($instalacion->id); ?>" class="btn btn-warning btn-sm me-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo htmlspecialchars($instalacion->id); ?>" title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No hay instalaciones registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar esta instalación? Esta acción no se puede deshacer.
                Ten en cuenta que si hay clases asociadas a esta instalación, la eliminación podría fallar.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a id="confirmDeleteButton" href="#" class="btn btn-danger">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<?php
// Include the footer
include '../app/views/shared/footer.php';
?>

<script>
    // Script to handle the delete modal
    document.addEventListener('DOMContentLoaded', function() {
        var deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            var button = event.relatedTarget;
            // Extract info from data-* attributes
            var instalacionId = button.getAttribute('data-id');
            // Update the modal's confirm button link
            var confirmDeleteButton = deleteModal.querySelector('#confirmDeleteButton');
            confirmDeleteButton.href = 'instalaciones.php?action=delete&id=' + instalacionId;
        });
    });
</script>
