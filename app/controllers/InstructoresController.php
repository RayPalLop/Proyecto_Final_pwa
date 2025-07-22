<?php
/**
 * Archivo: InstructoresController.php
 * Ubicación: /app/controllers/InstructoresController.php
 * Descripción: Controlador para la gestión de instructores del gimnasio.
 * Maneja las solicitudes para listar, añadir, editar y eliminar instructores.
 */

// Incluir el modelo Instructor
require_once __DIR__ . '/../models/Instructor.php';

class InstructoresController {
    private $instructorModel; // Instancia del modelo Instructor

    /**
     * Constructor del controlador.
     * @param PDO $pdo Instancia de la conexión PDO.
     */
    public function __construct(PDO $pdo) {
        $this->instructorModel = new Instructor($pdo);
    }

    /**
     * Muestra la lista de todos los instructores.
     */
    public function index() {
        // Obtener todos los instructores de la base de datos
        $instructores = $this->instructorModel->getAll();

        // Establecer el título de la página para el header
        $page_title = 'Gestión de Instructores';

        // Incluir la vista que mostrará la lista de instructores
        // La ruta es relativa desde public/instructores.php (donde se llamará al controlador)
        // hasta app/views/admin/instructores/index.php
        include __DIR__ . '/../views/admin/instructores/index.php';
    }

    /**
     * Muestra el formulario para crear un nuevo instructor y procesa su envío.
     */
    public function create() {
        $errors = []; // Array para almacenar errores de validación
        $success_message = ''; // Mensaje de éxito

        // Verificar si la solicitud es POST (envío del formulario)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger y sanear los datos del formulario
            $nombre = trim($_POST['nombre'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $contrasena = $_POST['contrasena'] ?? '';
            $confirm_contrasena = $_POST['confirm_contrasena'] ?? '';
            $especialidad = trim($_POST['especialidad'] ?? '');

            // Validaciones básicas
            if (empty($nombre)) {
                $errors[] = 'El nombre es obligatorio.';
            }
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
            if (empty($especialidad)) {
                $errors[] = 'La especialidad es obligatoria.';
            }

            // Si no hay errores, intentar crear el instructor
            if (empty($errors)) {
                if ($this->instructorModel->create($nombre, $correo, $contrasena, $especialidad)) {
                    $success_message = 'Instructor creado exitosamente.';
                    // Limpiar campos del formulario después del éxito para evitar doble envío
                    $nombre = $correo = $contrasena = $confirm_contrasena = $especialidad = '';
                } else {
                    $errors[] = 'Error al crear el instructor. El correo electrónico podría ya estar registrado.';
                }
            }
        }

        // Establecer el título de la página
        $page_title = 'Añadir Nuevo Instructor';

        // Incluir la vista del formulario de creación
        include __DIR__ . '/../views/admin/instructores/create.php';
    }

    /**
     * Muestra el formulario para editar un instructor existente y procesa su envío.
     * @param int $id El ID del instructor a editar.
     */
    public function edit($id) {
        $instructor = $this->instructorModel->getById($id); // Obtener el instructor por ID
        $errors = [];
        $success_message = '';

        // Si el instructor no existe, redirigir o mostrar un error
        if (!$instructor) {
            $_SESSION['error_message'] = 'Instructor no encontrado.';
            header('Location: instructores.php?action=index');
            exit();
        }

        // Verificar si la solicitud es POST (envío del formulario de edición)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger y sanear los datos del formulario
            $nombre = trim($_POST['nombre'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $contrasena = $_POST['contrasena'] ?? ''; // Opcional, solo si se cambia
            $confirm_contrasena = $_POST['confirm_contrasena'] ?? '';
            $especialidad = trim($_POST['especialidad'] ?? '');

            // Validaciones básicas
            if (empty($nombre)) {
                $errors[] = 'El nombre es obligatorio.';
            }
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
            if (empty($especialidad)) {
                $errors[] = 'La especialidad es obligatoria.';
            }

            // Si no hay errores, intentar actualizar el instructor
            if (empty($errors)) {
                // Si la contraseña está vacía, se pasa null para no actualizarla
                $new_password = empty($contrasena) ? null : $contrasena;

                if ($this->instructorModel->update($id, $instructor->usuario_id, $nombre, $correo, $especialidad, $new_password)) {
                    $success_message = 'Instructor actualizado exitosamente.';
                    // Recargar el instructor para mostrar los datos actualizados en el formulario
                    $instructor = $this->instructorModel->getById($id);
                } else {
                    $errors[] = 'Error al actualizar el instructor. El correo electrónico podría ya estar registrado por otro usuario.';
                }
            }
        }

        // Establecer el título de la página
        $page_title = 'Editar Instructor';

        // Incluir la vista del formulario de edición
        include __DIR__ . '/../views/admin/instructores/edit.php';
    }

    /**
     * Elimina un instructor.
     * @param int $id El ID del instructor a eliminar.
     */
    public function delete($id) {
        if ($this->instructorModel->delete($id)) {
            $_SESSION['success_message'] = 'Instructor eliminado exitosamente.';
        } else {
            $_SESSION['error_message'] = 'Error al eliminar el instructor. Asegúrate de que no tenga clases asignadas.';
        }
        // Redirigir siempre a la lista de instructores después de la eliminación
        header('Location: instructores.php?action=index');
        exit();
    }
}
