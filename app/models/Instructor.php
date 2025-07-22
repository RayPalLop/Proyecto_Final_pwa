<?php
/**
 * Archivo: Instructor.php
 * Ubicación: /app/models/Instructor.php
 * Descripción: Clase modelo para la gestión de instructores en la base de datos.
 * Contiene métodos para operaciones CRUD de instructores,
 * incluyendo la interacción con la tabla 'usuarios' para la autenticación.
 * ACTUALIZADO: Añadido método getInstructorByUsuarioId.
 */

class Instructor {
    private $pdo; // Objeto PDO para la conexión a la base de datos

    /**
     * Constructor de la clase Instructor.
     * @param PDO $pdo Instancia de la conexión PDO a la base de datos.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene todos los instructores del sistema.
     * Realiza un JOIN con la tabla 'usuarios' para obtener el correo electrónico.
     * @return array Un array de objetos que representan a los instructores.
     */
    public function getAll() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT i.id, i.nombre, i.especialidad, i.fecha_contratacion,
                       u.id AS usuario_id, u.correo
                FROM instructores i
                JOIN usuarios u ON i.usuario_id = u.id
                ORDER BY i.nombre ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener todos los instructores: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un instructor por su ID.
     * @param int $id El ID del instructor.
     * @return object|false El objeto instructor si se encuentra, o false si no.
     */
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT i.id, i.nombre, i.especialidad, i.fecha_contratacion,
                       u.id AS usuario_id, u.correo
                FROM instructores i
                JOIN usuarios u ON i.usuario_id = u.id
                WHERE i.id = :id
            ");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener instructor por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene un instructor por su ID de usuario asociado.
     * @param int $usuario_id El ID del usuario asociado al instructor.
     * @return object|false El objeto instructor si se encuentra, o false si no.
     */
    public function getInstructorByUsuarioId($usuario_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, nombre, especialidad, fecha_contratacion
                FROM instructores
                WHERE usuario_id = :usuario_id
            ");
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener instructor por usuario_id: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea un nuevo instructor y su usuario asociado.
     * @param string $nombre Nombre completo del instructor.
     * @param string $correo Correo electrónico del instructor (para el login).
     * @param string $contrasena Contraseña del instructor (se hashea antes de guardar).
     * @param string $especialidad Especialidad del instructor.
     * @return bool True si se crea correctamente, false en caso contrario.
     */
    public function create($nombre, $correo, $contrasena, $especialidad) {
        try {
            $this->pdo->beginTransaction();

            // 1. Insertar en la tabla 'usuarios'
            $stmt_rol = $this->pdo->prepare("SELECT id FROM roles WHERE nombre = 'Instructor'");
            $stmt_rol->execute();
            $rol_instructor_id = $stmt_rol->fetchColumn();

            if (!$rol_instructor_id) {
                throw new Exception("El rol 'Instructor' no se encontró en la base de datos.");
            }

            $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

            $stmt_user = $this->pdo->prepare("
                INSERT INTO usuarios (correo, contraseña, rol_id)
                VALUES (:correo, :contrasena, :rol_id)
            ");
            $stmt_user->bindParam(':correo', $correo);
            $stmt_user->bindParam(':contrasena', $hashed_password);
            $stmt_user->bindParam(':rol_id', $rol_instructor_id, PDO::PARAM_INT);
            $stmt_user->execute();

            $usuario_id = $this->pdo->lastInsertId();

            // 2. Insertar en la tabla 'instructores'
            $stmt_instructor = $this->pdo->prepare("
                INSERT INTO instructores (usuario_id, nombre, especialidad)
                VALUES (:usuario_id, :nombre, :especialidad)
            ");
            $stmt_instructor->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt_instructor->bindParam(':nombre', $nombre);
            $stmt_instructor->bindParam(':especialidad', $especialidad);
            $stmt_instructor->execute();

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error al crear instructor: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error lógico al crear instructor: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un instructor existente y su correo asociado.
     * @param int $id El ID del instructor a actualizar.
     * @param int $usuario_id El ID del usuario asociado al instructor.
     * @param string $nombre Nuevo nombre del instructor.
     * @param string $correo Nuevo correo electrónico del instructor.
     * @param string $especialidad Nueva especialidad.
     * @param string|null $contrasena Nueva contraseña (opcional).
     * @return bool True si se actualiza correctamente, false en caso contrario.
     */
    public function update($id, $usuario_id, $nombre, $correo, $especialidad, $contrasena = null) {
        try {
            $this->pdo->beginTransaction();

            // 1. Actualizar la tabla 'usuarios'
            $sql_user = "UPDATE usuarios SET correo = :correo";
            if ($contrasena !== null && !empty($contrasena)) {
                $sql_user .= ", contraseña = :contrasena";
            }
            $sql_user .= " WHERE id = :usuario_id";

            $stmt_user = $this->pdo->prepare($sql_user);
            $stmt_user->bindParam(':correo', $correo);
            if ($contrasena !== null && !empty($contrasena)) {
                $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);
                $stmt_user->bindParam(':contrasena', $hashed_password);
            }
            $stmt_user->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt_user->execute();

            // 2. Actualizar la tabla 'instructores'
            $stmt_instructor = $this->pdo->prepare("
                UPDATE instructores
                SET nombre = :nombre, especialidad = :especialidad
                WHERE id = :id
            ");
            $stmt_instructor->bindParam(':nombre', $nombre);
            $stmt_instructor->bindParam(':especialidad', $especialidad);
            $stmt_instructor->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_instructor->execute();

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error al actualizar instructor: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un instructor y su usuario asociado.
     * @param int $id El ID del instructor a eliminar.
     * @return bool True si se elimina correctamente, false en caso contrario.
     */
    public function delete($id) {
        try {
            $this->pdo->beginTransaction();

            // Obtener el usuario_id asociado al instructor
            $stmt_get_user_id = $this->pdo->prepare("SELECT usuario_id FROM instructores WHERE id = :id");
            $stmt_get_user_id->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_get_user_id->execute();
            $usuario_id = $stmt_get_user_id->fetchColumn();

            if (!$usuario_id) {
                throw new Exception("Instructor no encontrado o sin usuario asociado.");
            }

            // Eliminar el usuario. Esto debería eliminar el instructor debido a ON DELETE CASCADE.
            $stmt_user = $this->pdo->prepare("DELETE FROM usuarios WHERE id = :usuario_id");
            $stmt_user->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt_user->execute();

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error al eliminar instructor: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error lógico al eliminar instructor: " . $e->getMessage());
            return false;
        }
    }
}
