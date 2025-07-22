<?php
/**
 * Archivo: InstructorController.php
 * Ubicación: /app/controllers/InstructorController.php
 * Descripción: Controlador para las funcionalidades específicas del rol de Instructor.
 * Maneja la visualización de sus clases y horarios.
 */

// Incluir los modelos necesarios
require_once __DIR__ . '/../models/Clase.php';
require_once __DIR__ . '/../models/Instructor.php'; // Para obtener el ID del instructor logueado
require_once __DIR__ . '/../models/Reserva.php'; // Para contar inscritos en clases

class InstructorController {
    private $pdo;
    private $claseModel;
    private $instructorModel;
    private $reservaModel; // Nuevo: para contar reservas

    /**
     * Constructor del controlador.
     * @param PDO $pdo Instancia de la conexión PDO.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->claseModel = new Clase($pdo);
        $this->instructorModel = new Instructor($pdo);
        $this->reservaModel = new Reserva($pdo); // Inicializar el modelo de Reserva
    }

    /**
     * Muestra el dashboard del instructor.
     */
    public function dashboard() {
        $page_title = 'Dashboard de Instructor';
        // Aquí podrías cargar datos específicos del instructor para mostrar en el dashboard
        // Por ejemplo, próximas clases, etc.
        include __DIR__ . '/../views/instructor/dashboard.php';
    }

    /**
     * Muestra el horario de clases del instructor logueado.
     */
    public function misHorarios() {
        $errors = [];
        $instructor_clases = [];

        // Obtener el ID del usuario logueado
        $usuario_id_logueado = $_SESSION['usuario_id'] ?? null;
        if (!$usuario_id_logueado) {
            $errors[] = 'No se pudo identificar al instructor. Por favor, inicia sesión de nuevo.';
        }

        // Obtener el ID de la tabla 'instructores' a partir del 'usuario_id'
        $instructor_data = $this->instructorModel->getInstructorByUsuarioId($usuario_id_logueado);
        $instructor_id = $instructor_data ? $instructor_data->id : null;

        if (!$instructor_id) {
            $errors[] = 'No se encontró tu perfil de instructor asociado. Contacta al administrador.';
        } else {
            // Obtener todas las clases de este instructor
            $instructor_clases = $this->claseModel->getByInstructorId($instructor_id);
        }

        $page_title = 'Mis Horarios';
        include __DIR__ . '/../views/instructor/mis_horarios.php';
    }

    /**
     * Muestra las clases asignadas al instructor, incluyendo el número de inscritos.
     */
    public function misClases() {
        $errors = [];
        $instructor_clases = [];

        // Obtener el ID del usuario logueado
        $usuario_id_logueado = $_SESSION['usuario_id'] ?? null;
        if (!$usuario_id_logueado) {
            $errors[] = 'No se pudo identificar al instructor. Por favor, inicia sesión de nuevo.';
        }

        // Obtener el ID de la tabla 'instructores' a partir del 'usuario_id'
        $instructor_data = $this->instructorModel->getInstructorByUsuarioId($usuario_id_logueado);
        $instructor_id = $instructor_data ? $instructor_data->id : null;

        if (!$instructor_id) {
            $errors[] = 'No se encontró tu perfil de instructor asociado. Contacta al administrador.';
        } else {
            // Obtener todas las clases de este instructor
            $clases_raw = $this->claseModel->getByInstructorId($instructor_id);

            // Para cada clase, obtener el número de inscritos
            foreach ($clases_raw as $clase) {
                $clase->inscritos = $this->reservaModel->countReservationsByClassId($clase->id);
                $instructor_clases[] = $clase;
            }
        }

        $page_title = 'Mis Clases';
        include __DIR__ . '/../views/instructor/mis_clases.php';
    }
}
