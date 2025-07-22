<?php
/**
 * Archivo: InstalacionesController.php
 * Ubicación: /app/controllers/InstalacionesController.php
 * Descripción: Controlador para la gestión de instalaciones del gimnasio.
 * Maneja las solicitudes para listar, añadir, editar y eliminar instalaciones.
 */

// Include the Instalacion model
require_once __DIR__ . '/../models/Instalacion.php';

class InstalacionesController {
    private $instalacionModel; // Instance of the Instalacion model

    /**
     * Constructor of the controller.
     * @param PDO $pdo PDO connection instance.
     */
    public function __construct(PDO $pdo) {
        $this->instalacionModel = new Instalacion($pdo);
    }

    /**
     * Displays the list of all facilities.
     */
    public function index() {
        // Get all facilities from the database
        $instalaciones = $this->instalacionModel->getAll();

        // Set the page title for the header
        $page_title = 'Gestión de Instalaciones';

        // Include the view that will display the list of facilities
        // The path is relative from public/instalaciones.php (where the controller will be called)
        // to app/views/admin/instalaciones/index.php
        include __DIR__ . '/../views/admin/instalaciones/index.php';
    }

    /**
     * Displays the form to create a new facility and processes its submission.
     */
    public function create() {
        $errors = []; // Array to store validation errors
        $success_message = ''; // Success message

        // Check if the request is POST (form submission)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Collect and sanitize form data
            $nombre = trim($_POST['nombre'] ?? '');
            $tipo = trim($_POST['tipo'] ?? '');
            $capacidad = trim($_POST['capacidad'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');

            // Basic validations
            if (empty($nombre)) {
                $errors[] = 'El nombre de la instalación es obligatorio.';
            }
            if (empty($tipo)) {
                $errors[] = 'El tipo de instalación es obligatorio.';
            }
            if (empty($capacidad) || !is_numeric($capacidad) || $capacidad <= 0) {
                $errors[] = 'La capacidad es obligatoria y debe ser un número positivo.';
            }

            // If there are no errors, try to create the facility
            if (empty($errors)) {
                if ($this->instalacionModel->create($nombre, $tipo, $capacidad, $descripcion)) {
                    $success_message = 'Instalación creada exitosamente.';
                    // Optional: Redirect to the facilities list after creation
                    // header('Location: instalaciones.php?action=index&success=created');
                    // exit();
                    // Clear form fields after success to prevent double submission
                    $nombre = $tipo = $capacidad = $descripcion = '';
                } else {
                    $errors[] = 'Error al crear la instalación. El nombre podría ya estar registrado.';
                }
            }
        }

        // Set the page title
        $page_title = 'Añadir Nueva Instalación';

        // Include the creation form view
        include __DIR__ . '/../views/admin/instalaciones/create.php';
    }

    /**
     * Displays the form to edit an existing facility and processes its submission.
     * @param int $id The ID of the facility to edit.
     */
    public function edit($id) {
        $instalacion = $this->instalacionModel->getById($id); // Get the facility by ID
        $errors = [];
        $success_message = '';

        // If the facility does not exist, redirect or show an error
        if (!$instalacion) {
            $_SESSION['error_message'] = 'Instalación no encontrada.';
            header('Location: instalaciones.php?action=index');
            exit();
        }

        // Check if the request is POST (edit form submission)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Collect and sanitize form data
            $nombre = trim($_POST['nombre'] ?? '');
            $tipo = trim($_POST['tipo'] ?? '');
            $capacidad = trim($_POST['capacidad'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');

            // Basic validations
            if (empty($nombre)) {
                $errors[] = 'El nombre de la instalación es obligatorio.';
            }
            if (empty($tipo)) {
                $errors[] = 'El tipo de instalación es obligatorio.';
            }
            if (empty($capacidad) || !is_numeric($capacidad) || $capacidad <= 0) {
                $errors[] = 'La capacidad es obligatoria y debe ser un número positivo.';
            }

            // If there are no errors, try to update the facility
            if (empty($errors)) {
                if ($this->instalacionModel->update($id, $nombre, $tipo, $capacidad, $descripcion)) {
                    $success_message = 'Instalación actualizada exitosamente.';
                    // Reload the facility to show updated data in the form
                    $instalacion = $this->instalacionModel->getById($id);
                } else {
                    $errors[] = 'Error al actualizar la instalación. El nombre podría ya estar registrado por otra instalación.';
                }
            }
        }

        // Set the page title
        $page_title = 'Editar Instalación';

        // Include the edit form view
        include __DIR__ . '/../views/admin/instalaciones/edit.php';
    }

    /**
     * Deletes a facility.
     * @param int $id The ID of the facility to delete.
     */
    public function delete($id) {
        if ($this->instalacionModel->delete($id)) {
            $_SESSION['success_message'] = 'Instalación eliminada exitosamente.';
        } else {
            $_SESSION['error_message'] = 'Error al eliminar la instalación. Asegúrate de que no tenga clases asociadas.';
        }
        // Redirect always to the facilities list after deletion
        header('Location: instalaciones.php?action=index');
        exit();
    }
}
