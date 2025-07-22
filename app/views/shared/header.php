<?php
/**
 * Archivo: header.php
 * Ubicación: /app/views/shared/header.php
 * Descripción: Contiene la sección <head> del HTML, la inclusión de CSS (Bootstrap y personalizado)
 * y la barra de navegación superior para todas las páginas de la aplicación.
 * Debe ser incluido al inicio de cada vista.
 */

// Asegurarse de que la sesión esté iniciada para acceder a las variables de usuario.
// Esto es redundante si config.php ya lo hace y es incluido antes, pero es una buena práctica defensiva.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Obtener el nombre del usuario y el rol de la sesión para mostrar en la barra de navegación.
$user_email = htmlspecialchars($_SESSION['correo'] ?? 'Invitado');
$user_role = htmlspecialchars($_SESSION['rol_nombre'] ?? 'Desconocido');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Sistema de Gestión de Gimnasio'; ?></title>
    <!-- Incluir Bootstrap 5 CSS desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluir Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Incluir nuestro archivo CSS personalizado -->
    <!-- La ruta debe ser relativa desde la ubicación del archivo PHP que incluye este header.
         Si el archivo PHP está en public/, entonces 'css/style.css' es correcto. -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Barra lateral (Sidebar) se incluirá aquí -->
        <?php include 'sidebar.php'; ?>

        <!-- Contenido de la página (Page Content Wrapper) -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <!-- Botón para alternar la barra lateral en pantallas pequeñas -->
                    <button class="btn btn-primary" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-user-circle"></i> <?php echo $user_email; ?> (<?php echo $user_role; ?>)
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="#">Perfil</a>
                                    <a class="dropdown-item" href="#">Configuración</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="logout.php">Cerrar Sesión</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- El contenido específico de cada página se insertará después de este punto -->
            <div class="container-fluid py-4">
                <!-- Aquí es donde el contenido de la página individual será cargado -->
