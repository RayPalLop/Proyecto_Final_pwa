<?php
/**
 * Archivo: ClasesController.php
 * Ubicación: /app/controllers/ClasesController.php
 * Descripción: Controlador para la gestión de clases del gimnasio.
 * Maneja las solicitudes para listar, añadir, editar y eliminar clases.
 */

// Incluir los modelos necesarios
require_once __DIR__ . '/../models/Clase.php';
require_once __DIR__ . '/../models/Instructor.php'; // Para obtener la lista de instructores
require_once __DIR__ . '/../models/Instalacion.php'; // Para obtener la lista de instalaciones

class ClasesController {
    private $claseModel;
    private $instructorModel;
    private $instalacionModel;

    /**
     * Constructor del controlador.
     * @param PDO $pdo Instancia de la conexión PDO.
     */
    public function __construct(PDO $pdo) {
        $this->claseModel = new Clase($pdo);
        $this->instructorModel = new Instructor($pdo);
        $this->instalacionModel = new Instalacion($pdo);
    }

    /**
     * Muestra la lista de todas las clases.
     */
    public function index() {
        // Obtener todas las clases de la base de datos
        $clases = $this->claseModel->getAll();

        // Establecer el título de la página para el header
        $page_title = 'Gestión de Clases';

        // Incluir la vista que mostrará la lista de clases
        // La ruta es relativa desde public/clases.php (donde se llamará al controlador)
        // hasta app/views/admin/clases/index.php
        include __DIR__ . '/../views/admin/clases/index.php';
    }

    /**
     * Muestra el formulario para crear una nueva clase y procesa su envío.
     */
    public function create() {
        $errors = [];
        $success_message = '';
        $instructores = $this->instructorModel->getAll(); // Obtener lista de instructores
        $instalaciones = $this->instalacionModel->getAll(); // Obtener lista de instalaciones

        // Verificar si la solicitud es POST (envío del formulario)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger y sanear los datos del formulario
            $nombre = trim($_POST['nombre'] ?? '');
            $tipo = trim($_POST['tipo'] ?? '');
            $instructor_id = $_POST['instructor_id'] ?? null;
            $instalacion_id = $_POST['instalacion_id'] ?? null;
            $fecha_hora = trim($_POST['fecha_hora'] ?? '');
            $duracion_minutos = trim($_POST['duracion_minutos'] ?? '');
            $cupo_maximo = trim($_POST['cupo_maximo'] ?? '');

            // Validaciones básicas
            if (empty($nombre)) {
                $errors[] = 'El nombre de la clase es obligatorio.';
            }
            if (empty($tipo)) {
                $errors[] = 'El tipo de clase es obligatorio.';
            }
            if (empty($instructor_id) || !is_numeric($instructor_id)) {
                $errors[] = 'Debe seleccionar un instructor válido.';
            }
            if (empty($instalacion_id) || !is_numeric($instalacion_id)) {
                $errors[] = 'Debe seleccionar una instalación válida.';
            }
            if (empty($fecha_hora)) {
                $errors[] = 'La fecha y hora de la clase son obligatorias.';
            } elseif (strtotime($fecha_hora) < time()) {
                $errors[] = 'La fecha y hora de la clase no pueden ser en el pasado.';
            }
            if (empty($duracion_minutos) || !is_numeric($duracion_minutos) || $duracion_minutos <= 0) {
                $errors[] = 'La duración en minutos es obligatoria y debe ser un número positivo.';
            }
            if (empty($cupo_maximo) || !is_numeric($cupo_maximo) || $cupo_maximo <= 0) {
                $errors[] = 'El cupo máximo es obligatorio y debe ser un número positivo.';
            }

            // Si no hay errores, intentar crear la clase
            if (empty($errors)) {
                if ($this->claseModel->create($nombre, $tipo, $instructor_id, $instalacion_id, $fecha_hora, $duracion_minutos, $cupo_maximo)) {
                    $success_message = 'Clase creada exitosamente.';
                    // Opcional: Redirigir a la lista de clases después de crear
                    // header('Location: clases.php?action=index&success=created');
                    // exit();
                    // Limpiar campos del formulario después del éxito para evitar doble envío
                    $nombre = $tipo = $instructor_id = $instalacion_id = $fecha_hora = $duracion_minutos = $cupo_maximo = '';
                } else {
                    $errors[] = 'Error al crear la clase. Por favor, inténtalo de nuevo.';
                }
            }
        }

        // Establecer el título de la página
        $page_title = 'Añadir Nueva Clase';

        // Incluir la vista del formulario de creación
        include __DIR__ . '/../views/admin/clases/create.php';
    }

    /**
     * Muestra el formulario para editar una clase existente y procesa su envío.
     * @param int $id El ID de la clase a editar.
     */
    public function edit($id) {
        $clase = $this->claseModel->getById($id); // Obtener la clase por ID
        $errors = [];
        $success_message = '';
        $instructores = $this->instructorModel->getAll();
        $instalaciones = $this->instalacionModel->getAll();

        // Si la clase no existe, redirigir o mostrar un error
        if (!$clase) {
            $_SESSION['error_message'] = 'Clase no encontrada.';
            header('Location: clases.php?action=index');
            exit();
        }

        // Verificar si la solicitud es POST (envío del formulario de edición)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger y sanear los datos del formulario
            $nombre = trim($_POST['nombre'] ?? '');
            $tipo = trim($_POST['tipo'] ?? '');
            $instructor_id = $_POST['instructor_id'] ?? null;
            $instalacion_id = $_POST['instalacion_id'] ?? null;
            $fecha_hora = trim($_POST['fecha_hora'] ?? '');
            $duracion_minutos = trim($_POST['duracion_minutos'] ?? '');
            $cupo_maximo = trim($_POST['cupo_maximo'] ?? '');

            // Validaciones básicas
            if (empty($nombre)) {
                $errors[] = 'El nombre de la clase es obligatorio.';
            }
            if (empty($tipo)) {
                $errors[] = 'El tipo de clase es obligatorio.';
            }
            if (empty($instructor_id) || !is_numeric($instructor_id)) {
                $errors[] = 'Debe seleccionar un instructor válido.';
            }
            if (empty($instalacion_id) || !is_numeric($instalacion_id)) {
                $errors[] = 'Debe seleccionar una instalación válida.';
            }
            if (empty($fecha_hora)) {
                $errors[] = 'La fecha y hora de la clase son obligatorias.';
            } elseif (strtotime($fecha_hora) < time() && strtotime($fecha_hora) !== strtotime($clase->fecha_hora)) {
                // Permitir guardar si la fecha/hora es la misma y ya pasó, pero no si se cambia a una fecha pasada nueva
                $errors[] = 'La fecha y hora de la clase no pueden ser en el pasado.';
            }
            if (empty($duracion_minutos) || !is_numeric($duracion_minutos) || $duracion_minutos <= 0) {
                $errors[] = 'La duración en minutos es obligatoria y debe ser un número positivo.';
            }
            if (empty($cupo_maximo) || !is_numeric($cupo_maximo) || $cupo_maximo <= 0) {
                $errors[] = 'El cupo máximo es obligatorio y debe ser un número positivo.';
            }

            // Si no hay errores, intentar actualizar la clase
            if (empty($errors)) {
                if ($this->claseModel->update($id, $nombre, $tipo, $instructor_id, $instalacion_id, $fecha_hora, $duracion_minutos, $cupo_maximo)) {
                    $success_message = 'Clase actualizada exitosamente.';
                    // Recargar la clase para mostrar los datos actualizados en el formulario
                    $clase = $this->claseModel->getById($id);
                } else {
                    $errors[] = 'Error al actualizar la clase. Por favor, inténtalo de nuevo.';
                }
            }
        }

        // Establecer el título de la página
        $page_title = 'Editar Clase';

        // Incluir la vista del formulario de edición
        include __DIR__ . '/../views/admin/clases/edit.php';
    }

    /**
     * Elimina una clase.
     * @param int $id El ID de la clase a eliminar.
     */
    public function delete($id) {
        if ($this->claseModel->delete($id)) {
            $_SESSION['success_message'] = 'Clase eliminada exitosamente.';
        } else {
            $_SESSION['error_message'] = 'Error al eliminar la clase. Asegúrate de que no tenga reservas asociadas.';
        }
        // Redirigir siempre a la lista de clases después de la eliminación
        header('Location: clases.php?action=index');
        exit();
    }
}
