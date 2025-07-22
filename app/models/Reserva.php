<?php
/**
 * Archivo: Reserva.php
 * Ubicación: /app/models/Reserva.php
 * Descripción: Clase modelo para la gestión de reservas de clases en la base de datos.
 * Contiene métodos para operaciones CRUD de reservas, incluyendo la obtención de
 * información de miembros y clases asociadas.
 * ACTUALIZADO: Añadido método countReservationsByClassId.
 */

class Reserva {
    private $pdo; // Objeto PDO para la conexión a la base de datos

    /**
     * Constructor de la clase Reserva.
     * @param PDO $pdo Instancia de la conexión PDO a la base de datos.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene todas las reservas del sistema, con información de miembro y clase.
     * @return array Un array de objetos que representan las reservas.
     */
    public function getAll() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT r.id, r.fecha_reserva, r.estado,
                       m.nombre AS miembro_nombre, m.id AS miembro_id,
                       c.nombre AS clase_nombre, c.fecha_hora AS clase_fecha_hora, c.id AS clase_id,
                       i.nombre AS instructor_nombre, inst.nombre AS instalacion_nombre
                FROM reservas r
                JOIN miembros m ON r.miembro_id = m.id
                JOIN clases c ON r.clase_id = c.id
                JOIN instructores i ON c.instructor_id = i.id
                JOIN instalaciones inst ON c.instalacion_id = inst.id
                ORDER BY r.fecha_reserva DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener todas las reservas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene una reserva por su ID.
     * @param int $id El ID de la reserva.
     * @return object|false El objeto reserva si se encuentra, o false si no.
     */
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT r.id, r.fecha_reserva, r.estado,
                       m.nombre AS miembro_nombre, m.id AS miembro_id,
                       c.nombre AS clase_nombre, c.fecha_hora AS clase_fecha_hora, c.id AS clase_id,
                       i.nombre AS instructor_nombre, inst.nombre AS instalacion_nombre
                FROM reservas r
                JOIN miembros m ON r.miembro_id = m.id
                JOIN clases c ON r.clase_id = c.id
                JOIN instructores i ON c.instructor_id = i.id
                JOIN instalaciones inst ON c.instalacion_id = inst.id
                WHERE r.id = :id
            ");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener reserva por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todas las reservas de un miembro específico.
     * @param int $miembro_id El ID del miembro.
     * @return array Un array de objetos que representan las reservas del miembro.
     */
    public function getByMiembroId($miembro_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT r.id, r.fecha_reserva, r.estado,
                       c.nombre AS clase_nombre, c.fecha_hora AS clase_fecha_hora, c.duracion_minutos,
                       i.nombre AS instructor_nombre,
                       inst.nombre AS instalacion_nombre
                FROM reservas r
                JOIN clases c ON r.clase_id = c.id
                JOIN instructores i ON c.instructor_id = i.id
                JOIN instalaciones inst ON c.instalacion_id = inst.id
                WHERE r.miembro_id = :miembro_id
                ORDER BY c.fecha_hora DESC
            ");
            $stmt->bindParam(':miembro_id', $miembro_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener reservas por miembro ID: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Cuenta el número de reservas confirmadas para una clase específica.
     * @param int $clase_id El ID de la clase.
     * @return int El número de reservas confirmadas.
     */
    public function countReservationsByClassId($clase_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(id) FROM reservas
                WHERE clase_id = :clase_id AND estado = 'Confirmada'
            ");
            $stmt->bindParam(':clase_id', $clase_id, PDO::PARAM_INT);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error al contar reservas por clase ID: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Verifica si un miembro ya tiene una reserva para una clase específica.
     * @param int $miembro_id ID del miembro.
     * @param int $clase_id ID de la clase.
     * @return bool True si ya existe una reserva, false en caso contrario.
     */
    public function hasReservation($miembro_id, $clase_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM reservas
                WHERE miembro_id = :miembro_id AND clase_id = :clase_id AND estado = 'Confirmada'
            ");
            $stmt->bindParam(':miembro_id', $miembro_id, PDO::PARAM_INT);
            $stmt->bindParam(':clase_id', $clase_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar reserva existente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea una nueva reserva.
     * @param int $miembro_id ID del miembro que realiza la reserva.
     * @param int $clase_id ID de la clase a reservar.
     * @param string $estado Estado inicial de la reserva (ej. 'Confirmada').
     * @return bool True si se crea correctamente, false en caso contrario.
     */
    public function create($miembro_id, $clase_id, $estado = 'Confirmada') {
        try {
            // Verificar si la clase tiene cupo disponible y si la fecha es futura
            $stmt_check_class = $this->pdo->prepare("
                SELECT c.cupo_maximo, COUNT(r.id) AS current_reservations
                FROM clases c
                LEFT JOIN reservas r ON c.id = r.clase_id AND r.estado = 'Confirmada'
                WHERE c.id = :clase_id AND c.fecha_hora > NOW()
                GROUP BY c.id, c.cupo_maximo
            ");
            $stmt_check_class->bindParam(':clase_id', $clase_id, PDO::PARAM_INT);
            $stmt_check_class->execute();
            $class_info = $stmt_check_class->fetch(PDO::FETCH_OBJ);

            if (!$class_info || $class_info->current_reservations >= $class_info->cupo_maximo) {
                error_log("Intento de reserva fallido: Clase no encontrada, ya pasó o no hay cupo.");
                return false; // Clase no disponible o sin cupo
            }

            // Verificar si el miembro ya tiene una reserva para esta clase
            if ($this->hasReservation($miembro_id, $clase_id)) {
                error_log("Intento de reserva fallido: El miembro ya tiene una reserva para esta clase.");
                return false;
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO reservas (miembro_id, clase_id, fecha_reserva, estado)
                VALUES (:miembro_id, :clase_id, NOW(), :estado)
            ");
            $stmt->bindParam(':miembro_id', $miembro_id, PDO::PARAM_INT);
            $stmt->bindParam(':clase_id', $clase_id, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al crear reserva: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza una reserva existente.
     * @param int $id El ID de la reserva a actualizar.
     * @param int $miembro_id Nuevo ID del miembro.
     * @param int $clase_id Nuevo ID de la clase.
     * @param string $estado Nuevo estado de la reserva.
     * @return bool True si se actualiza correctamente, false en caso contrario.
     */
    public function update($id, $miembro_id, $clase_id, $estado) {
        try {
            // Antes de actualizar, verificar si la nueva combinación miembro_id/clase_id ya existe
            // para otra reserva (que no sea la que estamos editando)
            $stmt_check_unique = $this->pdo->prepare("
                SELECT COUNT(*) FROM reservas
                WHERE miembro_id = :miembro_id AND clase_id = :clase_id AND id != :id AND estado = 'Confirmada'
            ");
            $stmt_check_unique->bindParam(':miembro_id', $miembro_id, PDO::PARAM_INT);
            $stmt_check_unique->bindParam(':clase_id', $clase_id, PDO::PARAM_INT);
            $stmt_check_unique->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_check_unique->execute();

            if ($stmt_check_unique->fetchColumn() > 0) {
                error_log("Error de actualización: La combinación miembro/clase ya existe para otra reserva.");
                return false; // Duplicado
            }

            $stmt = $this->pdo->prepare("
                UPDATE reservas
                SET miembro_id = :miembro_id, clase_id = :clase_id, estado = :estado
                WHERE id = :id
            ");
            $stmt->bindParam(':miembro_id', $miembro_id, PDO::PARAM_INT);
            $stmt->bindParam(':clase_id', $clase_id, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar reserva: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina una reserva.
     * @param int $id El ID de la reserva a eliminar.
     * @return bool True si se elimina correctamente, false en caso contrario.
     */
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM reservas WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar reserva: " . $e->getMessage());
            return false;
        }
    }
}
