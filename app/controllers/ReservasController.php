<?php
/**
 * Archivo: ReservasController.php
 * Ubicación: /app/controllers/ReservasController.php
 * Descripción: Controlador para la gestión de reservas del gimnasio.
 * Maneja las solicitudes para listar, añadir, editar y eliminar reservas.
 */

// Incluir los modelos necesarios
require_once __DIR__ . '/../models/Reserva.php';
require_once __DIR__ . '/../models/Miembro.php'; // Para obtener la lista de miembros
require_once __DIR__ . '/../models/Clase.php';    // Para obtener la lista de clases

class ReservasController {
    private $reservaModel;
    private $miembroModel;
    private $claseModel;

    /**
     * Constructor del controlador.
     * @param PDO $pdo Instancia de la conexión PDO.
     */
    public function __construct(PDO $pdo) {
        $this->reservaModel = new Reserva($pdo);
        $this->miembroModel = new Miembro($pdo);
        $this->claseModel = new Clase($pdo);
    }

    /**
     * Muestra la lista de todas las reservas.
     */
    public function index() {
        // Obtener todas las reservas de la base de datos
        $reservas = $this->reservaModel->getAll();

        // Establecer el título de la página para el header
        $page_title = 'Gestión de Reservas';

        // Incluir la vista que mostrará la lista de reservas
        // La ruta es relativa desde public/reservas.php (donde se llamará al controlador)
        // hasta app/views/admin/reservas/index.php
        include __DIR__ . '/../views/admin/reservas/index.php';
    }

    /**
     * Muestra el formulario para crear una nueva reserva y procesa su envío.
     */
    public function create() {
        $errors = [];
        $success_message = '';
        $miembros = $this->miembroModel->getAll(); // Obtener lista de miembros
        $clases = $this->claseModel->getAll();    // Obtener lista de clases

        // Verificar si la solicitud es POST (envío del formulario)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger y sanear los datos del formulario
            $miembro_id = $_POST['miembro_id'] ?? null;
            $clase_id = $_POST['clase_id'] ?? null;
            $estado = trim($_POST['estado'] ?? 'Confirmada'); // Valor por defecto

            // Validaciones básicas
            if (empty($miembro_id) || !is_numeric($miembro_id)) {
                $errors[] = 'Debe seleccionar un miembro válido.';
            }
            if (empty($clase_id) || !is_numeric($clase_id)) {
                $errors[] = 'Debe seleccionar una clase válida.';
            }
            if (empty($estado)) {
                $errors[] = 'El estado de la reserva es obligatorio.';
            }

            // Si no hay errores, intentar crear la reserva
            if (empty($errors)) {
                if ($this->reservaModel->create($miembro_id, $clase_id, $estado)) {
                    $success_message = 'Reserva creada exitosamente.';
                    // Limpiar campos del formulario después del éxito para evitar doble envío
                    $miembro_id = $clase_id = $estado = '';
                } else {
                    $errors[] = 'Error al crear la reserva. Un miembro no puede reservar la misma clase dos veces o la clase ya pasó.';
                }
            }
        }

        // Establecer el título de la página
        $page_title = 'Añadir Nueva Reserva';

        // Incluir la vista del formulario de creación
        include __DIR__ . '/../views/admin/reservas/create.php';
    }

    /**
     * Muestra el formulario para editar una reserva existente y procesa su envío.
     * @param int $id El ID de la reserva a editar.
     */
    public function edit($id) {
        $reserva = $this->reservaModel->getById($id); // Obtener la reserva por ID
        $errors = [];
        $success_message = '';
        $miembros = $this->miembroModel->getAll();
        $clases = $this->claseModel->getAll();

        // Si la reserva no existe, redirigir o mostrar un error
        if (!$reserva) {
            $_SESSION['error_message'] = 'Reserva no encontrada.';
            header('Location: reservas.php?action=index');
            exit();
        }

        // Verificar si la solicitud es POST (envío del formulario de edición)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger y sanear los datos del formulario
            $miembro_id = $_POST['miembro_id'] ?? null;
            $clase_id = $_POST['clase_id'] ?? null;
            $estado = trim($_POST['estado'] ?? '');

            // Validaciones básicas
            if (empty($miembro_id) || !is_numeric($miembro_id)) {
                $errors[] = 'Debe seleccionar un miembro válido.';
            }
            if (empty($clase_id) || !is_numeric($clase_id)) {
                $errors[] = 'Debe seleccionar una clase válida.';
            }
            if (empty($estado)) {
                $errors[] = 'El estado de la reserva es obligatorio.';
            }

            // Si no hay errores, intentar actualizar la reserva
            if (empty($errors)) {
                if ($this->reservaModel->update($id, $miembro_id, $clase_id, $estado)) {
                    $success_message = 'Reserva actualizada exitosamente.';
                    // Recargar la reserva para mostrar los datos actualizados en el formulario
                    $reserva = $this->reservaModel->getById($id);
                } else {
                    $errors[] = 'Error al actualizar la reserva. Asegúrate de que el miembro no tenga ya esta clase reservada.';
                }
            }
        }

        // Establecer el título de la página
        $page_title = 'Editar Reserva';

        // Incluir la vista del formulario de edición
        include __DIR__ . '/../views/admin/reservas/edit.php';
    }

    /**
     * Elimina una reserva.
     * @param int $id El ID de la reserva a eliminar.
     */
    public function delete($id) {
        if ($this->reservaModel->delete($id)) {
            $_SESSION['success_message'] = 'Reserva eliminada exitosamente.';
        } else {
            $_SESSION['error_message'] = 'Error al eliminar la reserva.';
        }
        // Redirigir siempre a la lista de reservas después de la eliminación
        header('Location: reservas.php?action=index');
        exit();
    }
}
