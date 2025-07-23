<?php
/**
 * Archivo: dashboard.php
 * Ubicación: /app/views/admin/dashboard.php
 * Descripción: Vista específica del dashboard para el rol de Administrador.
 * Muestra un resumen y enlaces a los módulos de gestión.
 * Esta vista será incluida por public/dashboard.php (o un controlador específico para el dashboard).
 * ACTUALIZADO: Añadido gráfico de clases más populares.
 */

// Asegurarse de que la sesión esté iniciada y el usuario sea administrador.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_nombre'] !== 'Administrador') {
    header('Location: index.php'); // Redirigir al login si no es administrador
    exit();
}

// Se asume que $page_title ya está definido en el script que incluye esta vista (ej. public/dashboard.php)
// $top_classes_data se espera que venga de public/dashboard.php

// Incluir el encabezado de la página (que incluye la barra lateral)
include '../app/views/shared/header.php';
?>

<div class="container-fluid">
    <h1 class="mt-4 text-white">Dashboard de Administrador</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php" class="text-white">Dashboard</a></li>
        <li class="breadcrumb-item active text-white">Resumen</li>
    </ol>

    <div class="alert alert-info alert-dismissible fade show" role="alert">
        Bienvenido, administrador **<?php echo htmlspecialchars($_SESSION['correo'] ?? 'desconocido'); ?>**. Desde aquí puedes gestionar todos los aspectos del gimnasio.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="row">
        <!-- Tarjeta de Miembros -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Miembros
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-users me-2"></i> Gestión Completa
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="miembros.php" class="small text-primary stretched-link">Ver y Gestionar Miembros <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Instructores -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Instructores
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-user-tie me-2"></i> Gestión Completa
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="instructores.php" class="small text-success stretched-link">Ver y Gestionar Instructores <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Clases -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Clases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-calendar-alt me-2"></i> Gestión Completa
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="clases.php" class="small text-info stretched-link">Ver y Gestionar Clases <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Instalaciones -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Instalaciones
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-building me-2"></i> Gestión Completa
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="instalaciones.php" class="small text-warning stretched-link">Ver y Gestionar Instalaciones <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tarjeta de Reservas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Reservas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-book-open me-2"></i> Gestión Completa
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="reservas.php" class="small text-danger stretched-link">Ver y Gestionar Reservas <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Gestión de Usuarios -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Usuarios
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-user-cog me-2"></i> Gestión de Acceso
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-cog fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="usuarios.php" class="small text-secondary stretched-link">Ver y Gestionar Usuarios <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Nueva Tarjeta para el Gráfico de Clases Más Populares -->
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Clases Más Populares (por Reservas)
                </div>
                <div class="card-body">
                    <?php if (!empty($top_classes_data)): ?>
                        <canvas id="popularClassesChart" width="400" height="200"></canvas>
                    <?php else: ?>
                        <div class="alert alert-warning text-center" role="alert">
                            No hay datos de reservas para mostrar el gráfico.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el pie de página
include '../app/views/shared/footer.php';
?>

<!-- Script de Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos de PHP pasados a JavaScript
        const topClassesData = <?php echo json_encode($top_classes_data); ?>;

        if (topClassesData && topClassesData.length > 0) {
            const labels = topClassesData.map(item => item.clase_nombre);
            const data = topClassesData.map(item => item.total_reservas);

            // Generar colores dinámicamente para el gráfico
            const backgroundColors = [
                'rgba(255, 99, 132, 0.8)', // Rojo
                'rgba(54, 162, 235, 0.8)', // Azul
                'rgba(255, 206, 86, 0.8)', // Amarillo
                'rgba(75, 192, 192, 0.8)', // Verde
                'rgba(153, 102, 255, 0.8)', // Morado
                'rgba(255, 159, 64, 0.8)', // Naranja
                'rgba(199, 199, 199, 0.8)', // Gris
                'rgba(83, 102, 255, 0.8)', // Azul claro
                'rgba(255, 0, 255, 0.8)',  // Magenta
                'rgba(0, 255, 0, 0.8)'     // Verde lima
            ];
            const borderColors = [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(199, 199, 199, 1)',
                'rgba(83, 102, 255, 1)',
                'rgba(255, 0, 255, 1)',
                'rgba(0, 255, 0, 1)'
            ];

            const ctx = document.getElementById('popularClassesChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut', // Tipo de gráfico de dona
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Número de Reservas',
                        data: data,
                        backgroundColor: backgroundColors.slice(0, labels.length), // Usar solo los colores necesarios
                        borderColor: borderColors.slice(0, labels.length),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Permite controlar el tamaño con width/height en canvas
                    plugins: {
                        legend: {
                            position: 'right', // Leyenda a la derecha como en tu imagen
                            labels: {
                                color: '#333', // Color de texto de la leyenda
                                font: {
                                    size: 12
                                }
                            }
                        },
                        title: {
                            display: false, // El título ya está en el card-header
                            text: 'Clases Más Populares'
                        }
                    }
                }
            });
        }
    });
</script>
