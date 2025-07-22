<?php
/**
 * Archivo: MiembroController.php
 * Ubicación: /app/controllers/MiembroController.php
 * Descripción: Controlador para las funcionalidades específicas del rol de Miembro.
 * Maneja la visualización de clases disponibles, la creación de reservas,
 * el historial de actividades del miembro y ahora la gestión de su perfil.
 */

// Incluir los modelos necesarios
require_once __DIR__ . '/../models/Clase.php';
require_once __DIR__ . '/../models/Reserva.php';
require_once __DIR__ . '/../models/Miembro.php'; // Para obtener el ID del miembro logueado y actualizar su perfil

class MiembroController {
    private $pdo;
    private $claseModel;
    private $reservaModel;
    private $miembroModel;

    /**
     * Constructor del controlador.
     * @param PDO $pdo Instancia de la conexión PDO.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->claseModel = new Clase($pdo);
        $this->reservaModel = new Reserva($pdo);
        $this->miembroModel = new Miembro($pdo);
    }

    /**
     * Muestra el dashboard del miembro.
     */
    public function dashboard() {
        $page_title = 'Dashboard de Miembro';
        // Aquí podrías cargar datos específicos del miembro para mostrar en el dashboard
        // Por ejemplo, próximas clases reservadas, etc.
        include __DIR__ . '/../views/miembro/dashboard.php';
    }

    /**
     * Muestra las clases disponibles para reservar y procesa la solicitud de reserva.
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
                    $errors[] = 'Error al realizar la reserva. La clase podría estar llena o ya ha pasado, o ya la tienes reservada.';
                }
            }
        }

        $page_title = 'Reservar Clase';
        include __DIR__ . '/../views/miembro/reservar_clase.php';
    }

    /**
     * Muestra el historial de reservas del miembro logueado.
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
     * Muestra el formulario para editar el perfil del miembro y procesa su envío.
     */
    public function miPerfil() {
        $errors = [];
        $success_message = '';

        // Obtener el ID del usuario logueado
        $usuario_id_logueado = $_SESSION['usuario_id'] ?? null;
        if (!$usuario_id_logueado) {
            $errors[] = 'No se pudo identificar al usuario. Por favor, inicia sesión de nuevo.';
        }

        // Obtener el perfil del miembro y sus datos de usuario asociados
        $miembro = $this->miembroModel->getMiembroByUsuarioId($usuario_id_logueado);

        if (!$miembro) {
            $errors[] = 'No se encontró tu perfil de miembro. Contacta al administrador.';
        }

        // Si la solicitud es POST, procesar la actualización del perfil
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
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

            // Verificar si el correo ya existe para otro usuario (excluyendo el actual)
            $existing_user_with_email = $this->miembroModel->pdo->prepare("SELECT id FROM usuarios WHERE correo = :correo AND id != :current_user_id");
            $existing_user_with_email->bindParam(':correo', $correo);
            $existing_user_with_email->bindParam(':current_user_id', $usuario_id_logueado, PDO::PARAM_INT);
            $existing_user_with_email->execute();
            if ($existing_user_with_email->fetchColumn()) {
                $errors[] = 'El correo electrónico ya está registrado por otro usuario.';
            }


            // Si no hay errores, intentar actualizar el perfil
            if (empty($errors)) {
                $new_password = empty($contrasena) ? null : $contrasena;

                if ($this->miembroModel->update($miembro->id, $usuario_id_logueado, $nombre, $correo, $fecha_nacimiento, $genero, $new_password)) {
                    $success_message = 'Perfil actualizado exitosamente.';
                    // Recargar los datos del miembro para mostrar los cambios en el formulario
                    $miembro = $this->miembroModel->getMiembroByUsuarioId($usuario_id_logueado);
                    // Actualizar el correo en la sesión si ha cambiado
                    $_SESSION['correo'] = $correo;
                } else {
                    $errors[] = 'Error al actualizar el perfil. Por favor, inténtalo de nuevo.';
                }
            }
        }

        $page_title = 'Mi Perfil';
        include __DIR__ . '/../views/miembro/perfil.php';
    }
}
