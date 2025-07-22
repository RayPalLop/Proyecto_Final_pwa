<?php
/**
 * Archivo: Instalacion.php
 * Ubicación: /app/models/Instalacion.php
 * Descripción: Clase modelo para la gestión de instalaciones del gimnasio en la base de datos.
 * Contiene métodos para operaciones CRUD (Crear, Leer, Actualizar, Eliminar) de instalaciones.
 */

class Instalacion {
    private $pdo; // Objeto PDO para la conexión a la base de datos

    /**
     * Constructor de la clase Instalacion.
     * @param PDO $pdo Instancia de la conexión PDO a la base de datos.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene todas las instalaciones del sistema.
     * @return array Un array de objetos que representan las instalaciones.
     */
    public function getAll() {
        try {
            $stmt = $this->pdo->prepare("SELECT id, nombre, tipo, capacidad, descripcion FROM instalaciones ORDER BY nombre ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener todas las instalaciones: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene una instalación por su ID.
     * @param int $id El ID de la instalación.
     * @return object|false El objeto instalación si se encuentra, o false si no.
     */
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, nombre, tipo, capacidad, descripcion FROM instalaciones WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener instalación por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea una nueva instalación.
     * @param string $nombre Nombre de la instalación.
     * @param string $tipo Tipo de instalación.
     * @param int $capacidad Capacidad de la instalación.
     * @param string|null $descripcion Descripción de la instalación.
     * @return bool True si se crea correctamente, false en caso contrario.
     */
    public function create($nombre, $tipo, $capacidad, $descripcion = null) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO instalaciones (nombre, tipo, capacidad, descripcion)
                VALUES (:nombre, :tipo, :capacidad, :descripcion)
            ");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':capacidad', $capacidad, PDO::PARAM_INT);
            $stmt->bindParam(':descripcion', $descripcion);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al crear instalación: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza una instalación existente.
     * @param int $id El ID de la instalación a actualizar.
     * @param string $nombre Nuevo nombre de la instalación.
     * @param string $tipo Nuevo tipo de instalación.
     * @param int $capacidad Nueva capacidad de la instalación.
     * @param string|null $descripcion Nueva descripción de la instalación.
     * @return bool True si se actualiza correctamente, false en caso contrario.
     */
    public function update($id, $nombre, $tipo, $capacidad, $descripcion = null) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE instalaciones
                SET nombre = :nombre, tipo = :tipo, capacidad = :capacidad, descripcion = :descripcion
                WHERE id = :id
            ");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':capacidad', $capacidad, PDO::PARAM_INT);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar instalación: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina una instalación.
     * NOTA: Si hay clases asociadas a esta instalación, la eliminación podría fallar
     * debido a la restricción de clave foránea (ON DELETE RESTRICT).
     * Se recomienda verificar si hay clases antes de eliminar.
     * @param int $id El ID de la instalación a eliminar.
     * @return bool True si se elimina correctamente, false en caso contrario.
     */
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM instalaciones WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar instalación: " . $e->getMessage());
            return false;
        }
    }
}
