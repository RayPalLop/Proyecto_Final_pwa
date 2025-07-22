<?php
/**
 * Archivo: sidebar.php
 * Ubicación: /app/views/shared/sidebar.php
 * Descripción: Contiene la barra lateral de navegación del sistema.
 * Los elementos del menú se adaptan según el rol del usuario logueado.
 * Debe ser incluido en el header.php.
 * ACTUALIZADO: Añadido enlace "Reservar Clase" para el rol de Miembro.
 */

// Asegurarse de que la sesión esté iniciada para acceder al rol del usuario.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$current_role = $_SESSION['rol_nombre'] ?? 'Invitado'; // Obtener el rol del usuario
?>

<!-- Sidebar (Barra Lateral) -->
<div class="bg-dark border-right" id="sidebar-wrapper">
    <div class="sidebar-heading text-white text-center py-4">
        <i class="fas fa-dumbbell fa-2x me-2"></i> Gestión Gimnasio
    </div>
    <div class="list-group list-group-flush">
        <!-- Enlaces comunes para todos los roles (o la mayoría) -->
        <a href="dashboard.php" class="list-group-item list-group-item-action bg-dark text-white">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
        </a>

        <?php if ($current_role === 'Administrador'): ?>
            <!-- Enlaces específicos para el Administrador -->
            <a href="miembros.php" class="list-group-item list-group-item-action bg-dark text-white">
                <i class="fas fa-users me-2"></i> Miembros
            </a>
            <a href="instructores.php" class="list-group-item list-group-item-action bg-dark text-white">
                <i class="fas fa-user-tie me-2"></i> Instructores
            </a>
            <a href="clases.php" class="list-group-item list-group-item-action bg-dark text-white">
                <i class="fas fa-calendar-alt me-2"></i> Clases
            </a>
            <a href="instalaciones.php" class="list-group-item list-group-item-action bg-dark text-white">
                <i class="fas fa-building me-2"></i> Instalaciones
            </a>
            <a href="reservas.php" class="list-group-item list-group-item-action bg-dark text-white">
                <i class="fas fa-book-open me-2"></i> Reservas
            </a>
            <a href="usuarios.php" class="list-group-item list-group-item-action bg-dark text-white">
                <i class="fas fa-user-cog me-2"></i> Gestión Usuarios
            </a>
        <?php elseif ($current_role === 'Instructor'): ?>
            <!-- Enlaces específicos para el Instructor -->
            <a href="mis_clases.php" class="list-group-item list-group-item-action bg-dark text-white">
                <i class="fas fa-chalkboard-teacher me-2"></i> Mis Clases
            </a>
            <a href="mis_horarios.php" class="list-group-item list-group-item-action bg-dark text-white">
                <i class="fas fa-clock me-2"></i> Mis Horarios
            </a>
        <?php elseif ($current_role === 'Miembro'): ?>
            <!-- Enlaces específicos para el Miembro -->
            <a href="reservar_clase.php" class="list-group-item list-group-item-action bg-dark text-white">
                <i class="fas fa-calendar-plus me-2"></i> Reservar Clase
            </a>
            <a href="mis_reservas.php" class="list-group-item list-group-item-action bg-dark text-white">
                <i class="fas fa-history me-2"></i> Mi Historial
            </a>
            <a href="perfil.php" class="list-group-item list-group-item-action bg-dark text-white">
                <i class="fas fa-user-circle me-2"></i> Mi Perfil
            </a>
        <?php endif; ?>

        <!-- Enlace de Cerrar Sesión (común para todos los logueados) -->
        <a href="logout.php" class="list-group-item list-group-item-action bg-dark text-white mt-auto">
            <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
        </a>
    </div>
</div>
<!-- /#sidebar-wrapper -->

<style>
    /* Estilos para la barra lateral (sidebar) */
    #sidebar-wrapper {
        min-height: 100vh;
        margin-left: -15rem; /* Oculta el sidebar por defecto */
        -webkit-transition: margin .25s ease-out;
        -moz-transition: margin .25s ease-out;
        -o-transition: margin .25s ease-out;
        transition: margin .25s ease-out;
        width: 15rem; /* Ancho del sidebar */
        position: fixed; /* Fijo en la pantalla */
        top: 0;
        left: 0;
        z-index: 1000; /* Asegura que esté por encima de otros elementos */
    }

    /* Estilos para el contenido de la página */
    #page-content-wrapper {
        min-width: 100vw; /* Ocupa todo el ancho de la ventana */
        margin-left: 0; /* Por defecto, el contenido no tiene margen extra */
        -webkit-transition: margin .25s ease-out;
        -moz-transition: margin .25s ease-out;
        -o-transition: margin .25s ease-out;
        transition: margin .25s ease-out;
    }

    /* Cuando el sidebar está "toggled" (visible) */
    #wrapper.toggled #sidebar-wrapper {
        margin-left: 0; /* Muestra el sidebar */
    }

    #wrapper.toggled #page-content-wrapper {
        margin-left: 15rem; /* Mueve el contenido para dejar espacio al sidebar */
        min-width: calc(100vw - 15rem); /* Ajusta el ancho del contenido */
    }

    /* Estilos responsivos para pantallas más grandes */
    @media (min-width: 768px) {
        #sidebar-wrapper {
            margin-left: 0; /* Sidebar visible por defecto en desktop */
        }

        #page-content-wrapper {
            min-width: 0;
            width: 100%;
            margin-left: 15rem; /* Contenido con margen para el sidebar */
        }

        #wrapper.toggled #sidebar-wrapper {
            margin-left: -15rem; /* Oculta el sidebar en desktop si se hace toggle */
        }

        #wrapper.toggled #page-content-wrapper {
            margin-left: 0; /* Contenido ocupa todo el ancho */
            min-width: 100vw;
        }
    }

    /* Estilos para los elementos de la lista del sidebar */
    .list-group-item {
        border: none; /* Sin bordes */
        border-radius: 0; /* Sin bordes redondeados */
        padding: 1rem 1.25rem; /* Espaciado interno */
        transition: background-color 0.3s ease; /* Transición suave al hover */
    }

    .list-group-item:hover {
        background-color: rgba(255, 255, 255, 0.1) !important; /* Fondo más claro al hover */
        color: #fff; /* Texto blanco al hover */
    }

    .sidebar-heading {
        font-size: 1.5rem; /* Tamaño del título del sidebar */
        font-weight: bold; /* Negrita */
        border-bottom: 1px solid rgba(255, 255, 255, 0.1); /* Separador */
    }

    /* Estilos para el botón de toggle del sidebar */
    #sidebarToggle {
        background-color: #007bff; /* Color azul de Bootstrap */
        border-color: #007bff;
        transition: background-color 0.3s ease;
    }

    #sidebarToggle:hover {
        background-color: #0056b3; /* Azul más oscuro al hover */
        border-color: #0056b3;
    }

    /* Estilos para la barra de navegación superior */
    .navbar-light {
        background-color: #f8f9fa !important; /* Fondo claro */
        border-bottom: 1px solid #dee2e6; /* Borde inferior */
    }

    .navbar-brand, .nav-link {
        color: #333 !important; /* Color de texto oscuro */
    }

    .navbar-nav .nav-link.dropdown-toggle {
        color: #007bff !important; /* Color azul para el usuario logueado */
    }

    .dropdown-menu {
        border-radius: 0.5rem; /* Bordes redondeados para el menú desplegable */
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15); /* Sombra */
    }
</style>

<script>
    // JavaScript para el toggle de la barra lateral
    document.addEventListener('DOMContentLoaded', function() {
        var sidebarToggle = document.getElementById('sidebarToggle');
        var wrapper = document.getElementById('wrapper');

        if (sidebarToggle && wrapper) {
            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                wrapper.classList.toggle('toggled');
            });
        }
    });
</script>
