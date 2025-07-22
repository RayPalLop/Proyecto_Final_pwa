<?php
/**
 * Archivo: Clase.php
 * Ubicación: /app/models/Clase.php
 * Descripción: Clase modelo para la gestión de clases en la base de datos.
 * Contiene métodos para operaciones CRUD de clases, incluyendo la obtención de
 * información de instructores e instalaciones asociadas.
 * ACTUALIZADO: Añadido método getByInstructorId para el horario del instructor.
 */

class Clase {
    private $pdo; // Objeto PDO para la conexión a la base de datos

    /**
     * Constructor de la clase Clase.
     * @param PDO $pdo Instancia de la conexión PDO a la base de datos.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene todas las clases del sistema, con información de instructor e instalación.
     * @return array Un array de objetos que representan las clases.
     */
    public function getAll() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.id, c.nombre, c.tipo, c.fecha_hora, c.duracion_minutos, c.cupo_maximo,
                       i.nombre AS instructor_nombre, i.id AS instructor_id,
                       inst.nombre AS instalacion_nombre, inst.id AS instalacion_id
                FROM clases c
                JOIN instructores i ON c.instructor_id = i.id
                JOIN instalaciones inst ON c.instalacion_id = inst.id
                ORDER BY c.fecha_hora DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener todas las clases: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene una clase por su ID.
     * @param int $id El ID de la clase.
     * @return object|false El objeto clase si se encuentra, o false si no.
     */
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.id, c.nombre, c.tipo, c.fecha_hora, c.duracion_minutos, c.cupo_maximo,
                       i.nombre AS instructor_nombre, i.id AS instructor_id,
                       inst.nombre AS instalacion_nombre, inst.id AS instalacion_id
                FROM clases c
                JOIN instructores i ON c.instructor_id = i.id
                JOIN instalaciones inst ON c.instalacion_id = inst.id
                WHERE c.id = :id
            ");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener clase por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene las clases disponibles para reserva (futuras y con cupo).
     * Calcula el cupo actual restando las reservas confirmadas.
     * @return array Un array de objetos que representan las clases disponibles.
     */
    public function getAvailableClasses() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.id, c.nombre, c.tipo, c.fecha_hora, c.duracion_minutos, c.cupo_maximo,
                       i.nombre AS instructor_nombre,
                       inst.nombre AS instalacion_nombre,
                       (c.cupo_maximo - COALESCE(COUNT(r.id), 0)) AS cupo_actual
                FROM clases c
                JOIN instructores i ON c.instructor_id = i.id
                JOIN instalaciones inst ON c.instalacion_id = inst.id
                LEFT JOIN reservas r ON c.id = r.clase_id AND r.estado = 'Confirmada'
                WHERE c.fecha_hora > NOW() -- Solo clases futuras
                GROUP BY c.id, c.nombre, c.tipo, c.fecha_hora, c.duracion_minutos, c.cupo_maximo,
                         instructor_nombre, instalacion_nombre
                HAVING cupo_actual > 0 -- Solo clases con cupo disponible
                ORDER BY c.fecha_hora ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener clases disponibles: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene todas las clases asignadas a un instructor específico.
     * @param int $instructor_id El ID del instructor.
     * @return array Un array de objetos que representan las clases del instructor.
     */
    public function getByInstructorId($instructor_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.id, c.nombre, c.tipo, c.fecha_hora, c.duracion_minutos, c.cupo_maximo,
                       inst.nombre AS instalacion_nombre,
                       (SELECT COUNT(r.id) FROM reservas r WHERE r.clase_id = c.id AND r.estado = 'Confirmada') AS inscritos
                FROM clases c
                JOIN instalaciones inst ON c.instalacion_id = inst.id
                WHERE c.instructor_id = :instructor_id
                ORDER BY c.fecha_hora ASC
            ");
            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener clases por instructor ID: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Crea una nueva clase.
     * @param string $nombre Nombre de la clase.
     * @param string $tipo Tipo de clase.
     * @param int $instructor_id ID del instructor que imparte la clase.
     * @param int $instalacion_id ID de la instalación donde se imparte.
     * @param string $fecha_hora Fecha y hora de la clase.
     * @param int $duracion_minutos Duración de la clase en minutos.
     * @param int $cupo_maximo Cupo máximo de la clase.
     * @return bool True si se crea correctamente, false en caso contrario.
     */
    public function create($nombre, $tipo, $instructor_id, $instalacion_id, $fecha_hora, $duracion_minutos, $cupo_maximo) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO clases (nombre, tipo, instructor_id, instalacion_id, fecha_hora, duracion_minutos, cupo_maximo)
                VALUES (:nombre, :tipo, :instructor_id, :instalacion_id, :fecha_hora, :duracion_minutos, :cupo_maximo)
            ");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':instalacion_id', $instalacion_id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_hora', $fecha_hora);
            $stmt->bindParam(':duracion_minutos', $duracion_minutos, PDO::PARAM_INT);
            $stmt->bindParam(':cupo_maximo', $cupo_maximo, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al crear clase: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza una clase existente.
     * @param int $id El ID de la clase a actualizar.
     * @param string $nombre Nuevo nombre de la clase.
     * @param string $tipo Nuevo tipo de clase.
     * @param int $instructor_id Nuevo ID del instructor.
     * @param int $instalacion_id Nuevo ID de la instalación.
     * @param string $fecha_hora Nueva fecha y hora.
     * @param int $duracion_minutos Nueva duración.
     * @param int $cupo_maximo Nuevo cupo máximo.
     * @return bool True si se actualiza correctamente, false en caso contrario.
     */
    public function update($id, $nombre, $tipo, $instructor_id, $instalacion_id, $fecha_hora, $duracion_minutos, $cupo_maximo) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE clases
                SET nombre = :nombre, tipo = :tipo, instructor_id = :instructor_id,
                    instalacion_id = :instalacion_id, fecha_hora = :fecha_hora,
                    duracion_minutos = :duracion_minutos, cupo_maximo = :cupo_maximo
                WHERE id = :id
            ");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':instalacion_id', $instalacion_id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_hora', $fecha_hora);
            $stmt->bindParam(':duracion_minutos', $duracion_minutos, PDO::PARAM_INT);
            $stmt->bindParam(':cupo_maximo', $cupo_maximo, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar clase: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina una clase.
     * NOTA: Si hay reservas asociadas a esta clase, la eliminación podría fallar
     * debido a la restricción de clave foránea (ON DELETE CASCADE en reservas).
     * @param int $id El ID de la clase a eliminar.
     * @return bool True si se elimina correctamente, false en caso contrario.
     */
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM clases WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar clase: " . $e->getMessage());
            return false;
        }
    }
}
