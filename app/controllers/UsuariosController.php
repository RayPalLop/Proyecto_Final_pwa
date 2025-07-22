<?php
/**
 * Archivo: UsuariosController.php
 * Ubicación: /app/controllers/UsuariosController.php
 * Descripción: Controlador para la gestión de usuarios del gimnasio.
 * Maneja las solicitudes para listar, añadir, editar y eliminar usuarios,
 * incluyendo la asignación de roles.
 */

// Incluir el modelo Usuario
require_once __DIR__ . '/../models/Usuario.php';

class UsuariosController {
    private $usuarioModel; // Instancia del modelo Usuario

    /**
     * Constructor del controlador.
     * @param PDO $pdo Instancia de la conexión PDO.
     */
    public function __construct(PDO $pdo) {
        $this->usuarioModel = new Usuario($pdo);
    }

    /**
     * Muestra la lista de todos los usuarios.
     */
    public function index() {
        // Obtener todos los usuarios de la base de datos
        $usuarios = $this->usuarioModel->getAll();

        // Establecer el título de la página para el header
        $page_title = 'Gestión de Usuarios';

        // Incluir la vista que mostrará la lista de usuarios
        // La ruta es relativa desde public/usuarios.php (donde se llamará al controlador)
        // hasta app/views/admin/usuarios/index.php
        include __DIR__ . '/../views/admin/usuarios/index.php';
    }

    /**
     * Muestra el formulario para crear un nuevo usuario y procesa su envío.
     */
    public function create() {
        $errors = []; // Array para almacenar errores de validación
        $success_message = ''; // Mensaje de éxito
        $roles = $this->usuarioModel->getAllRoles(); // Obtener todos los roles disponibles

        // Verificar si la solicitud es POST (envío del formulario)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger y sanear los datos del formulario
            $correo = trim($_POST['correo'] ?? '');
            $contrasena = $_POST['contrasena'] ?? '';
            $confirm_contrasena = $_POST['confirm_contrasena'] ?? '';
            $rol_id = $_POST['rol_id'] ?? '';

            // Validaciones básicas
            if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'El correo electrónico es obligatorio y debe ser válido.';
            }
            if (empty($contrasena)) {
                $errors[] = 'La contraseña es obligatoria.';
            } elseif (strlen($contrasena) < 6) {
                $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
            }
            if ($contrasena !== $confirm_contrasena) {
                $errors[] = 'Las contraseñas no coinciden.';
            }
            if (empty($rol_id)) {
                $errors[] = 'El rol es obligatorio.';
            }

            // Verificar si el correo ya existe
            if ($this->usuarioModel->getByCorreo($correo)) {
                $errors[] = 'El correo electrónico ya está registrado.';
            }

            // Si no hay errores, intentar crear el usuario
            if (empty($errors)) {
                if ($this->usuarioModel->create($correo, $contrasena, $rol_id)) {
                    $success_message = 'Usuario creado exitosamente.';
                    // Limpiar campos del formulario después del éxito para evitar doble envío
                    $correo = $contrasena = $confirm_contrasena = $rol_id = '';
                } else {
                    $errors[] = 'Error al crear el usuario. Por favor, inténtalo de nuevo.';
                }
            }
        }

        // Establecer el título de la página
        $page_title = 'Añadir Nuevo Usuario';

        // Incluir la vista del formulario de creación
        include __DIR__ . '/../views/admin/usuarios/create.php';
    }

    /**
     * Muestra el formulario para editar un usuario existente y procesa su envío.
     * @param int $id El ID del usuario a editar.
     */
    public function edit($id) {
        $usuario = $this->usuarioModel->getById($id); // Obtener el usuario por ID
        $errors = [];
        $success_message = '';
        $roles = $this->usuarioModel->getAllRoles(); // Obtener todos los roles disponibles

        // Si el usuario no existe, redirigir o mostrar un error
        if (!$usuario) {
            $_SESSION['error_message'] = 'Usuario no encontrado.';
            header('Location: usuarios.php?action=index');
            exit();
        }

        // Verificar si la solicitud es POST (envío del formulario de edición)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger y sanear los datos del formulario
            $correo = trim($_POST['correo'] ?? '');
            $contrasena = $_POST['contrasena'] ?? '';
            $confirm_contrasena = $_POST['confirm_contrasena'] ?? '';
            $rol_id = $_POST['rol_id'] ?? '';

            // Validaciones básicas
            if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'El correo electrónico es obligatorio y debe ser válido.';
            }
            if (!empty($contrasena)) { // Solo validar si se ha introducido una nueva contraseña
                if (strlen($contrasena) < 6) {
                    $errors[] = 'La nueva contraseña debe tener al menos 6 caracteres.';
                }
                if ($contrasena !== $confirm_contrasena) {
                    $errors[] = 'Las contraseñas no coinciden.';
                }
            }
            if (empty($rol_id)) {
                $errors[] = 'El rol es obligatorio.';
            }

            // Verificar si el correo ya existe para otro usuario (excluyendo el actual)
            $existing_user_with_email = $this->usuarioModel->getByCorreo($correo);
            if ($existing_user_with_email && $existing_user_with_email->id != $id) {
                $errors[] = 'El correo electrónico ya está registrado por otro usuario.';
            }

            // Si no hay errores, intentar actualizar el usuario
            if (empty($errors)) {
                // Si la contraseña está vacía, se pasa null para no actualizarla
                $new_password = empty($contrasena) ? null : $contrasena;

                if ($this->usuarioModel->update($id, $correo, $rol_id, $new_password)) {
                    $success_message = 'Usuario actualizado exitosamente.';
                    // Recargar el usuario para mostrar los datos actualizados en el formulario
                    $usuario = $this->usuarioModel->getById($id);
                    // Actualizar la sesión si el usuario actual ha cambiado su propio rol/correo
                    if ($_SESSION['usuario_id'] == $id) {
                        $_SESSION['correo'] = $usuario->correo;
                        $_SESSION['rol_nombre'] = $usuario->rol_nombre;
                    }
                } else {
                    $errors[] = 'Error al actualizar el usuario. Por favor, inténtalo de nuevo.';
                }
            }
        }

        // Establecer el título de la página
        $page_title = 'Editar Usuario';

        // Incluir la vista del formulario de edición
        include __DIR__ . '/../views/admin/usuarios/edit.php';
    }

    /**
     * Elimina un usuario.
     * @param int $id El ID del usuario a eliminar.
     */
    public function delete($id) {
        // Prevenir que un administrador se elimine a sí mismo
        if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id) {
            $_SESSION['error_message'] = 'No puedes eliminar tu propia cuenta de administrador.';
            header('Location: usuarios.php?action=index');
            exit();
        }

        if ($this->usuarioModel->delete($id)) {
            $_SESSION['success_message'] = 'Usuario eliminado exitosamente.';
        } else {
            $_SESSION['error_message'] = 'Error al eliminar el usuario. Asegúrate de que no tenga registros dependientes (miembros/instructores).';
        }
        // Redirigir siempre a la lista de usuarios después de la eliminación
        header('Location: usuarios.php?action=index');
        exit();
    }
}
