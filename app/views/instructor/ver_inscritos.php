<?php
/**
 * Archivo: ver_inscritos.php
 * Ubicación: /app/views/instructor/ver_inscritos.php
 * Descripción: Vista que muestra la lista de miembros inscritos en una clase específica.
 */

// Se asume que $page_title, $errors, $clase, y $inscritos ya están definidos.
include '../app/views/shared/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4">
        <?php echo htmlspecialchars($page_title ?? 'Inscritos'); ?>
        <?php if ($clase): ?>
            <small class="text-muted">- <?php echo htmlspecialchars($clase->nombre); ?></small>
        <?php endif; ?>
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="mis_clases.php">Mis Clases</a></li>
        <li class="breadcrumb-item active">Ver Inscritos</li>
    </ol>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($clase && empty($errors)): ?>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-users me-1"></i>
            Lista de Miembros Inscritos
        </div>
        <div class="card-body">
            <?php if (empty($inscritos)): ?>
                <div class="alert alert-info" role="alert">
                    No hay miembros inscritos en esta clase todavía.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nombre del Miembro</th>
                                <th>Correo Electrónico</th>
                                <th>Fecha de Reserva</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inscritos as $miembro): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($miembro->nombre); ?></td>
                                    <td><?php echo htmlspecialchars($miembro->correo); ?></td>
                                    <td><?php echo htmlspecialchars(formatDateTime($miembro->fecha_reserva)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
include '../app/views/shared/footer.php';
?>