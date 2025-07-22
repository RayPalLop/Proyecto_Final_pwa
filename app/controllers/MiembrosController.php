<?php
/**
 * Archivo: MiembrosController.php
 * Ubicación: /app/controllers/MiembrosController.php
 * Descripción: Controlador unificado para la gestión de miembros.
 * Maneja tanto las acciones del administrador (CRUD) como las del propio miembro (reservar, ver historial).
 */

// Incluir el modelo Miembro
require_once __DIR__ . '/../models/Miembro.php';
require_once __DIR__ . '/../models/Clase.php';
require_once __DIR__ . '/../models/Reserva.php';

class MiembrosController {
    private $miembroModel; // Instancia del modelo Miembro
    private $claseModel;
    private $reservaModel;

    /**
     * Constructor del controlador.
     * @param PDO $pdo Instancia de la conexión PDO.
     */
    public function __construct(PDO $pdo) {
        $this->miembroModel = new Miembro($pdo);
        $this->claseModel = new Clase($pdo);
        $this->reservaModel = new Reserva($pdo);
    }

    /**
     * [ADMIN] Muestra la lista de todos los miembros.
     */
    public function index() {
        // Obtener todos los miembros de la base de datos
        $miembros = $this->miembroModel->getAll();

        // Establecer el título de la página para el header
        $page_title = 'Gestión de Miembros';

        // Incluir la vista que mostrará la lista de miembros
        // La ruta es relativa desde public/miembros.php (donde se llamará al controlador)
        // hasta app/views/admin/miembros/index.php
        include __DIR__ . '/../views/admin/miembros/index.php';
    }

    /**
     * [ADMIN] Muestra el formulario para crear un nuevo miembro y procesa su envío.
     */
    public function create() {
        $errors = []; // Array para almacenar errores de validación
        $success_message = ''; // Mensaje de éxito

        // Verificar si la solicitud es POST (envío del formulario)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger y sanear los datos del formulario
            $nombre = trim($_POST['nombre'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $contrasena = $_POST['contrasena'] ?? ''; // La contraseña no se trimea para permitir espacios si se desea
            $confirm_contrasena = $_POST['confirm_contrasena'] ?? '';
            $fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
            $genero = trim($_POST['genero'] ?? '');

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
            if (empty($fecha_nacimiento)) {
                $errors[] = 'La fecha de nacimiento es obligatoria.';
            }
            if (empty($genero)) {
                $errors[] = 'El género es obligatorio.';
            }

            // Si no hay errores, intentar crear el miembro
            if (empty($errors)) {
                if ($this->miembroModel->create($nombre, $correo, $contrasena, $fecha_nacimiento, $genero)) {
                    $success_message = 'Miembro creado exitosamente.';
                    // Opcional: Redirigir a la lista de miembros después de crear
                    // header('Location: miembros.php?action=index&success=created');
                    // exit();
                    // Limpiar campos del formulario después del éxito para evitar doble envío
                    $nombre = $correo = $contrasena = $confirm_contrasena = $fecha_nacimiento = $genero = '';
                } else {
                    $errors[] = 'Error al crear el miembro. El correo electrónico podría ya estar registrado.';
                }
            }
        }

        // Establecer el título de la página
        $page_title = 'Añadir Nuevo Miembro';

        // Incluir la vista del formulario de creación
        include __DIR__ . '/../views/admin/miembros/create.php';
    }

    /**
     * [ADMIN] Muestra el formulario para editar un miembro existente y procesa su envío.
     * @param int $id El ID del miembro a editar.
     */
    public function edit($id) {
        $miembro = $this->miembroModel->getById($id); // Obtener el miembro por ID
        $errors = [];
        $success_message = '';

        // Si el miembro no existe, redirigir o mostrar un error
        if (!$miembro) {
            $_SESSION['error_message'] = 'Miembro no encontrado.';
            header('Location: miembros.php?action=index');
            exit();
        }

        // Verificar si la solicitud es POST (envío del formulario de edición)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger y sanear los datos del formulario
            $nombre = trim($_POST['nombre'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $contrasena = $_POST['contrasena'] ?? ''; // Opcional, solo si se cambia
            $confirm_contrasena = $_POST['confirm_contrasena'] ?? '';
            $fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
            $genero = trim($_POST['genero'] ?? '');

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
            if (empty($fecha_nacimiento)) {
                $errors[] = 'La fecha de nacimiento es obligatoria.';
            }
            if (empty($genero)) {
                $errors[] = 'El género es obligatorio.';
            }

            // Si no hay errores, intentar actualizar el miembro
            if (empty($errors)) {
                // Si la contraseña está vacía, se pasa null para no actualizarla
                $new_password = empty($contrasena) ? null : $contrasena;

                if ($this->miembroModel->update($id, $miembro->usuario_id, $nombre, $correo, $fecha_nacimiento, $genero, $new_password)) {
                    $success_message = 'Miembro actualizado exitosamente.';
                    // Recargar el miembro para mostrar los datos actualizados en el formulario
                    $miembro = $this->miembroModel->getById($id);
                } else {
                    $errors[] = 'Error al actualizar el miembro. El correo electrónico podría ya estar registrado por otro usuario.';
                }
            }
        }

        // Establecer el título de la página
        $page_title = 'Editar Miembro';

        // Incluir la vista del formulario de edición
        include __DIR__ . '/../views/admin/miembros/edit.php';
    }

    /**
     * [ADMIN] Elimina un miembro.
     * @param int $id El ID del miembro a eliminar.
     */
    public function delete($id) {
        if ($this->miembroModel->delete($id)) {
            $_SESSION['success_message'] = 'Miembro eliminado exitosamente.';
        } else {
            $_SESSION['error_message'] = 'Error al eliminar el miembro.';
        }
        // Redirigir siempre a la lista de miembros después de la eliminación
        header('Location: miembros.php?action=index');
        exit();
    }

    // --- MÉTODOS PARA EL ROL DE MIEMBRO ---

    /**
     * [MIEMBRO] Muestra las clases disponibles para reservar y procesa la solicitud de reserva.
     */
    public function reservarClase() {
        $errors = [];
        $success_message = '';

        // Obtener el ID del miembro logueado
        $usuario_id_logueado = $_SESSION['usuario_id'] ?? null;
        if (!$usuario_id_logueado) {
            $errors[] = 'No se pudo identificar al miembro. Por favor, inicia sesión de nuevo.';
        }

        // Obtener el ID de la tabla 'miembros' a partir del 'usuario_id'
        $miembro_data = $this->miembroModel->getMiembroByUsuarioId($usuario_id_logueado);
        $miembro_id = $miembro_data ? $miembro_data->id : null;

        if (!$miembro_id) {
            $errors[] = 'No se encontró tu perfil de miembro asociado. Contacta al administrador.';
        }

        // Obtener todas las clases disponibles (futuras y con cupo)
        $clases_disponibles = $this->claseModel->getAvailableClasses();
        
        // Si la solicitud es POST, procesar la reserva
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
            $clase_id = $_POST['clase_id'] ?? null;

            if (empty($clase_id) || !is_numeric($clase_id)) {
                $errors[] = 'Debe seleccionar una clase válida.';
            } else {
                // Intentar crear la reserva
                if ($this->reservaModel->create($miembro_id, $clase_id, 'Confirmada')) {
                    $success_message = '¡Clase reservada exitosamente!';
                    // Opcional: Recargar la lista de clases disponibles para reflejar el cambio de cupo
                    $clases_disponibles = $this->claseModel->getAvailableClasses();
                } else {
                    $errors[] = 'Error al realizar la reserva. La clase podría estar llena, ya ha pasado, o ya la tienes reservada.';
                }
            }
        }

        $page_title = 'Reservar Clase';
        include __DIR__ . '/../views/miembro/reservar_clase.php';
    }

    /**
     * [MIEMBRO] Muestra el historial de reservas del miembro logueado.
     */
    public function misReservas() {
        $errors = [];
        $miembro_reservas = [];

        // Obtener el ID del miembro logueado
        $usuario_id_logueado = $_SESSION['usuario_id'] ?? null;
        if (!$usuario_id_logueado) {
            $errors[] = 'No se pudo identificar al miembro. Por favor, inicia sesión de nuevo.';
        }

        // Obtener el ID de la tabla 'miembros' a partir del 'usuario_id'
        $miembro_data = $this->miembroModel->getMiembroByUsuarioId($usuario_id_logueado);
        $miembro_id = $miembro_data ? $miembro_data->id : null;

        if (!$miembro_id) {
            $errors[] = 'No se encontró tu perfil de miembro asociado. Contacta al administrador.';
        } else {
            // Obtener todas las reservas de este miembro
            $miembro_reservas = $this->reservaModel->getByMiembroId($miembro_id);
        }

        $page_title = 'Mi Historial de Actividades';
        include __DIR__ . '/../views/miembro/mis_reservas.php';
    }

    /**
     * [MIEMBRO] Muestra y procesa el formulario del perfil del miembro.
     */
    public function miPerfil() {
        $errors = [];
        $success_message = '';
        $usuario_id = $_SESSION['usuario_id'];

        // Obtener los datos actuales del miembro
        $miembro = $this->miembroModel->getMiembroByUsuarioId($usuario_id);

        if (!$miembro) {
            // Esto no debería pasar si el usuario está logueado como miembro
            $_SESSION['error_message'] = 'No se pudo encontrar tu perfil.';
            header('Location: dashboard.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Procesar el formulario
            $nombre = trim($_POST['nombre'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $contrasena = $_POST['contrasena'] ?? '';
            $confirm_contrasena = $_POST['confirm_contrasena'] ?? '';

            // Validaciones
            if (empty($nombre)) {
                $errors[] = 'El nombre es obligatorio.';
            }
            if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'El correo electrónico es obligatorio y debe ser válido.';
            }
            if (!empty($contrasena)) {
                if (strlen($contrasena) < 6) {
                    $errors[] = 'La nueva contraseña debe tener al menos 6 caracteres.';
                }
                if ($contrasena !== $confirm_contrasena) {
                    $errors[] = 'Las contraseñas no coinciden.';
                }
            }

            // Si no hay errores, intentar actualizar
            if (empty($errors)) {
                $new_password = empty($contrasena) ? null : $contrasena;
                if ($this->miembroModel->updateProfile($miembro->id, $usuario_id, $nombre, $correo, $new_password)) {
                    $success_message = '¡Perfil actualizado exitosamente!';
                    $_SESSION['correo'] = $correo; // Actualizar el correo en la sesión si cambió
                    $miembro = $this->miembroModel->getMiembroByUsuarioId($usuario_id); // Recargar datos
                } else {
                    $errors[] = 'Error al actualizar el perfil. El correo electrónico podría ya estar en uso.';
                }
            }
        }

        $page_title = 'Mi Perfil';
        include __DIR__ . '/../views/miembro/perfil.php';
    }
}
