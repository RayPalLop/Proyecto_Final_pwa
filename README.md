Sistema de Gestión de Gimnasio

Este es un sistema web básico para la gestión de un gimnasio, desarrollado con PHP, MySQL y Bootstrap. Permite la administración de miembros, instructores, clases, instalaciones y reservas, con diferentes niveles de acceso para administradores y miembros.
Estructura del Proyecto

El proyecto sigue una estructura MVC (Modelo-Vista-Controlador) básica para organizar el código:

    config/: Contiene archivos de configuración de la aplicación, como la conexión a la base de datos.

        config.php: Configuración de la base de datos y conexión PDO.

    public/: Contiene los archivos accesibles directamente desde el navegador. Es el "punto de entrada" de la aplicación.

        index.php: Página de inicio de sesión.

        dashboard.php: Dashboard principal que redirige según el rol del usuario.

        logout.php: Script para cerrar la sesión.

        css/: Archivos de estilos CSS.

            style.css: Estilos personalizados.

        js/: Archivos JavaScript.

            main.js: Scripts JavaScript personalizados.

        assets/: Contiene recursos como imágenes.

            img/: Imágenes del proyecto (ej. logo.png).

        miembros.php: Punto de entrada para la gestión de miembros (CRUD).

        usuarios.php: Punto de entrada para la gestión de usuarios y roles (CRUD).

        instructores.php: Punto de entrada para la gestión de instructores (CRUD).

        clases.php: Punto de entrada para la gestión de clases (CRUD).

        instalaciones.php: Punto de entrada para la gestión de instalaciones (CRUD).

        reservas.php: Punto de entrada para la gestión de reservas (CRUD).

        reservar_clase.php: Punto de entrada para la funcionalidad de reserva de clases para miembros.

        mis_reservas.php: Punto de entrada para el historial de reservas de miembros.

        perfil.php: Punto de entrada para la gestión del perfil del miembro.

    app/: Contiene la lógica de la aplicación (no accesible directamente desde el navegador).

        controllers/: Manejan la lógica de negocio y la interacción entre modelos y vistas.

            AuthController.php: Lógica de inicio y cierre de sesión.

            MiembrosController.php: Lógica CRUD para miembros (administrador).

            InstructoresController.php: Lógica CRUD para instructores (administrador).

            ClasesController.php: Lógica CRUD para clases (administrador).

            InstalacionesController.php: Lógica CRUD para instalaciones (administrador).

            ReservasController.php: Lógica CRUD para reservas (administrador).

            UsuariosController.php: Lógica CRUD para usuarios y roles (administrador).

            MiembroController.php: Lógica para funcionalidades del rol "Miembro" (reservar, historial, perfil).

        models/: Interactúan directamente con la base de datos.

            Database.php: Clase Singleton para la conexión PDO.

            Usuario.php: Modelo para la tabla usuarios y roles.

            Miembro.php: Modelo para la tabla miembros.

            Instructor.php: Modelo para la tabla instructores.

            Instalacion.php: Modelo para la tabla instalaciones.

            Clase.php: Modelo para la tabla clases.

            Reserva.php: Modelo para la tabla reservas.

        views/: Contienen los archivos de interfaz de usuario (HTML con PHP incrustado).

            shared/: Vistas comunes (header, footer, sidebar).

                header.php: Encabezado HTML y barra de navegación superior.

                footer.php: Pie de página HTML y scripts JS.

                sidebar.php: Barra lateral de navegación dinámica por rol.

            admin/: Vistas específicas para el rol de Administrador.

                dashboard.php: Dashboard del administrador.

                miembros/: Vistas CRUD para miembros.

                    index.php: Listado de miembros.

                    create.php: Formulario para añadir miembro.

                    edit.php: Formulario para editar miembro.

                clases/: Vistas CRUD para clases.

                    index.php: Listado de clases.

                    create.php: Formulario para añadir clase.

                    edit.php: Formulario para editar clase.

                instructores/: Vistas CRUD para instructores.

                    index.php: Listado de instructores.

                    create.php: Formulario para añadir instructor.

                    edit.php: Formulario para editar instructor.

                instalaciones/: Vistas CRUD para instalaciones.

                    index.php: Listado de instalaciones.

                    create.php: Formulario para añadir instalación.

                    edit.php: Formulario para editar instalación.

                reservas/: Vistas CRUD para reservas.

                    index.php: Listado de reservas.

                    create.php: Formulario para añadir reserva.

                    edit.php: Formulario para editar reserva.

                usuarios/: Vistas CRUD para usuarios.

                    index.php: Listado de usuarios.

                    create.php: Formulario para añadir usuario.

                    edit.php: Formulario para editar usuario.

            instructor/: Vistas específicas para el rol de Instructor.

                dashboard.php: Dashboard del instructor.

                mis_clases.php: Clases asignadas al instructor.

                mis_horarios.php: Horarios del instructor.

            miembro/: Vistas específicas para el rol de Miembro.

                dashboard.php: Dashboard del miembro.

                reservar_clase.php: Formulario para reservar clases.

                mis_reservas.php: Historial de reservas del miembro.

                perfil.php: Formulario para editar el perfil del miembro.

        includes/: Archivos con funciones y helpers de uso general.

            functions.php: Funciones de utilidad generales (sanitización, redirección, mensajes).

            helpers.php: Funciones de ayuda adicionales (verificación de roles, formato de fechas).

Configuración y Ejecución

    Base de Datos:

        Crea una base de datos MySQL llamada system_gym_db.

        Ejecuta el script SQL proporcionado (base_de_datos.sql o el que te di en la conversación) para crear las tablas y poblar los datos iniciales.

        Asegúrate de que las contraseñas en la tabla usuarios estén hasheadas con password_hash() de PHP para que la autenticación funcione correctamente. Puedes usar el script PHP temporal insert_users_with_hash.php que se sugirió previamente.

    Configuración de PHP:

        Edita config/config.php y ajusta las constantes DB_HOST, DB_NAME, DB_USER, DB_PASS con los detalles de tu base de datos.

        Para depuración, asegúrate de que display_errors = On y error_reporting = E_ALL en tu php.ini o añade las líneas ini_set al principio de tus archivos public/ (recuerda quitarlas en producción).

    Servidor Web:

        Coloca la carpeta system_gym en el directorio raíz de tu servidor web (ej. htdocs para XAMPP, www para WAMP).

        Accede a la aplicación a través de tu navegador: http://localhost/system_gym/public/

Credenciales de Prueba (después de ejecutar el script SQL y el script de hash de contraseñas)

    Administrador:

        Correo: admin@gimnasio.com

        Contraseña: admin123 (o la que hayas configurado con password_hash())

    Instructor:

        Correo: juan.perez@gimnasio.com

        Contraseña: instructor123 (o la que hayas configurado con password_hash())

    Miembro:

        Correo: carlos.garcia@gimnasio.com

        Contraseña: miembro123 (o la que hayas configurado con password_hash())

Próximos Pasos

    Implementar las funcionalidades restantes para los roles de Instructor y Miembro.

    Mejorar la interfaz de usuario y la experiencia del usuario (UX).

    Añadir validaciones de formulario más robustas en el lado del cliente (JavaScript).

    Implementar paginación y búsqueda en las tablas de listado.

    Añadir un sistema de mensajes flash más avanzado.

    Considerar el uso de un framework PHP para proyectos más grandes.

¡Disfruta desarrollando tu Sistema de Gestión de Gimnasio!