<?php
/**
 * Archivo: index.php
 * Ubicación: /app/views/admin/miembros/index.php
 * Descripción: Vista para mostrar la lista de todos los miembros del gimnasio.
 * Incluye opciones para añadir, editar y eliminar miembros.
 * Esta vista es incluida por MiembrosController::index().
 */

// Asegurarse de que la sesión esté iniciada y el usuario sea administrador.
// Esta verificación ya se hace en public/miembros.php, pero es una buena práctica defensiva.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Administrador') {
    header('Location: index.php'); // Redirigir al login si no es administrador
    exit();
}

// $miembros se espera que venga del controlador ($this->miembroModel->getAll())
// $page_title se espera que venga del controlador

// Incluir el encabezado de la página (que incluye la barra lateral)
// La ruta es relativa desde public/miembros.php
include '../app/views/shared/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4">Gestión de Miembros</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Miembros</li>
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
                <i class="fas fa-users me-1"></i>
                Listado de Miembros
            </div>
            <a href="miembros.php?action=create" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle me-1"></i> Añadir Nuevo Miembro
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="miembrosTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Fecha Nacimiento</th>
                            <th>Género</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($miembros)): ?>
                            <?php foreach ($miembros as $miembro): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($miembro->id); ?></td>
                                    <td><?php echo htmlspecialchars($miembro->nombre); ?></td>
                                    <td><?php echo htmlspecialchars($miembro->correo); ?></td>
                                    <td><?php echo htmlspecialchars($miembro->fecha_nacimiento); ?></td>
                                    <td><?php echo htmlspecialchars($miembro->genero); ?></td>
                                    <td><?php echo htmlspecialchars($miembro->fecha_registro); ?></td>
                                    <td>
                                        <a href="miembros.php?action=edit&id=<?php echo htmlspecialchars($miembro->id); ?>" class="btn btn-warning btn-sm me-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo htmlspecialchars($miembro->id); ?>" title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No hay miembros registrados.</td>
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
                ¿Estás seguro de que deseas eliminar este miembro? Esta acción no se puede deshacer.
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
            var miembroId = button.getAttribute('data-id');
            // Actualizar el enlace del botón de confirmación del modal
            var confirmDeleteButton = deleteModal.querySelector('#confirmDeleteButton');
            confirmDeleteButton.href = 'miembros.php?action=delete&id=' + miembroId;
        });
    });
</script>
