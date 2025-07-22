<?php
/**
 * Archivo: header.php
 * Ubicación: /app/views/shared/header.php
 * Descripción: Encabezado HTML común para todas las páginas del dashboard.
 * Incluye la barra lateral y los enlaces a CSS y JS.
 * ACTUALIZADO: Eliminada la barra de navegación superior.
 */

// Asegurarse de que la sesión esté iniciada para acceder al rol del usuario.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Obtener el nombre del usuario y el rol de la sesión para mostrarlos en la navbar.
$user_email = htmlspecialchars($_SESSION['correo'] ?? 'Invitado');
$user_role = htmlspecialchars($_SESSION['rol_nombre'] ?? 'Invitado');

// $page_title se espera que sea definido en el script que incluye este header.
// Si no está definido, se usa un valor por defecto.
$page_title = $page_title ?? 'Sistema de Gestión de Gimnasio';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- CSS personalizado -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content Wrapper -->
        <div id="page-content-wrapper">
            <!-- La barra de navegación superior ha sido eliminada aquí -->

            <!-- Page Content (el contenido específico de cada página se insertará aquí) -->
            <div class="container-fluid mt-4">
                <!-- Los mensajes de éxito/error se mostrarán aquí si se usa display_session_message() -->
                <?php
                // Incluir functions.php si display_session_message() no está disponible globalmente
                if (file_exists(__DIR__ . '/../includes/functions.php')) {
                    require_once __DIR__ . '/../includes/functions.php';
                    echo display_session_message();
                }
                ?>
