<?php
/**
 * Archivo: Miembro.php
 * Ubicación: /app/models/Miembro.php
 * Descripción: Clase modelo para la gestión de miembros en la base de datos.
 * Contiene métodos para operaciones CRUD (Crear, Leer, Actualizar, Eliminar) de miembros,
 * incluyendo la interacción con la tabla 'usuarios' para la autenticación.
 * ACTUALIZADO: Añadido método getMiembroByUsuarioId.
 */

class Miembro {
    private $pdo; // Objeto PDO para la conexión a la base de datos

    /**
     * Constructor de la clase Miembro.
     * @param PDO $pdo Instancia de la conexión PDO a la base de datos.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene todos los miembros del sistema.
     * Realiza un JOIN con la tabla 'usuarios' para obtener el correo electrónico.
     * @return array Un array de objetos que representan a los miembros.
     */
    public function getAll() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT m.id, m.nombre, m.fecha_nacimiento, m.genero, m.fecha_registro,
                       u.id AS usuario_id, u.correo
                FROM miembros m
                JOIN usuarios u ON m.usuario_id = u.id
                ORDER BY m.nombre ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ); // Devuelve todos los resultados como objetos
        } catch (PDOException $e) {
            error_log("Error al obtener todos los miembros: " . $e->getMessage());
            return []; // Devuelve un array vacío en caso de error
        }
    }

    /**
     * Obtiene un miembro por su ID.
     * @param int $id El ID del miembro.
     * @return object|false El objeto miembro si se encuentra, o false si no.
     */
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT m.id, m.nombre, m.fecha_nacimiento, m.genero, m.fecha_registro,
                       u.id AS usuario_id, u.correo
                FROM miembros m
                JOIN usuarios u ON m.usuario_id = u.id
                WHERE m.id = :id
            ");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ); // Devuelve el primer resultado como objeto
        } catch (PDOException $e) {
            error_log("Error al obtener miembro por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene un miembro por su ID de usuario asociado.
     * @param int $usuario_id El ID del usuario asociado al miembro.
     * @return object|false El objeto miembro si se encuentra, o false si no.
     */
    public function getMiembroByUsuarioId($usuario_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT m.id, m.nombre, m.fecha_nacimiento, m.genero, m.fecha_registro, u.correo
                FROM miembros m
                JOIN usuarios u ON m.usuario_id = u.id
                WHERE m.usuario_id = :usuario_id
            ");
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener miembro por usuario_id: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea un nuevo miembro y su usuario asociado.
     * @param string $nombre Nombre completo del miembro.
     * @param string $correo Correo electrónico del miembro (para el login).
     * @param string $contrasena Contraseña del miembro (se hashea antes de guardar).
     * @param string $fecha_nacimiento Fecha de nacimiento del miembro.
     * @param string $genero Género del miembro.
     * @return bool True si se crea correctamente, false en caso contrario.
     */
    public function create($nombre, $correo, $contrasena, $fecha_nacimiento, $genero) {
        try {
            // Iniciar una transacción para asegurar la atomicidad de las operaciones
            $this->pdo->beginTransaction();

            // 1. Insertar en la tabla 'usuarios'
            // Obtener el ID del rol 'Miembro'
            $stmt_rol = $this->pdo->prepare("SELECT id FROM roles WHERE nombre = 'Miembro'");
            $stmt_rol->execute();
            $rol_miembro_id = $stmt_rol->fetchColumn();

            if (!$rol_miembro_id) {
                throw new Exception("El rol 'Miembro' no se encontró en la base de datos.");
            }

            // Hashear la contraseña antes de almacenarla
            $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

            $stmt_user = $this->pdo->prepare("
                INSERT INTO usuarios (correo, contraseña, rol_id)
                VALUES (:correo, :contrasena, :rol_id)
            ");
            $stmt_user->bindParam(':correo', $correo);
            $stmt_user->bindParam(':contrasena', $hashed_password);
            $stmt_user->bindParam(':rol_id', $rol_miembro_id, PDO::PARAM_INT);
            $stmt_user->execute();

            $usuario_id = $this->pdo->lastInsertId(); // Obtener el ID del usuario recién insertado

            // 2. Insertar en la tabla 'miembros'
            $stmt_miembro = $this->pdo->prepare("
                INSERT INTO miembros (usuario_id, nombre, fecha_nacimiento, genero)
                VALUES (:usuario_id, :nombre, :fecha_nacimiento, :genero)
            ");
            $stmt_miembro->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt_miembro->bindParam(':nombre', $nombre);
            $stmt_miembro->bindParam(':fecha_nacimiento', $fecha_nacimiento);
            $stmt_miembro->bindParam(':genero', $genero);
            $stmt_miembro->execute();

            // Confirmar la transacción
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            // Revertir la transacción en caso de error
            $this->pdo->rollBack();
            error_log("Error al crear miembro: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error lógico al crear miembro: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un miembro existente y su correo asociado.
     * @param int $id El ID del miembro a actualizar.
     * @param int $usuario_id El ID del usuario asociado al miembro.
     * @param string $nombre Nuevo nombre del miembro.
     * @param string $correo Nuevo correo electrónico del miembro.
     * @param string $fecha_nacimiento Nueva fecha de nacimiento.
     * @param string $genero Nuevo género.
     * @param string|null $contrasena Nueva contraseña (opcional, solo si se va a cambiar).
     * @return bool True si se actualiza correctamente, false en caso contrario.
     */
    public function update($id, $usuario_id, $nombre, $correo, $fecha_nacimiento, $genero, $contrasena = null) {
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

            // 2. Actualizar la tabla 'miembros'
            $stmt_miembro = $this->pdo->prepare("
                UPDATE miembros
                SET nombre = :nombre, fecha_nacimiento = :fecha_nacimiento, genero = :genero
                WHERE id = :id
            ");
            $stmt_miembro->bindParam(':nombre', $nombre);
            $stmt_miembro->bindParam(':fecha_nacimiento', $fecha_nacimiento);
            $stmt_miembro->bindParam(':genero', $genero);
            $stmt_miembro->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_miembro->execute();

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error al actualizar miembro: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza el perfil de un miembro (nombre, correo, contraseña).
     * @param int $id El ID del miembro a actualizar.
     * @param int $usuario_id El ID del usuario asociado.
     * @param string $nombre Nuevo nombre del miembro.
     * @param string $correo Nuevo correo electrónico.
     * @param string|null $contrasena Nueva contraseña (opcional).
     * @return bool True si se actualiza correctamente, false en caso contrario.
     */
    public function updateProfile($id, $usuario_id, $nombre, $correo, $contrasena = null) {
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

            // 2. Actualizar la tabla 'miembros'
            $stmt_miembro = $this->pdo->prepare("
                UPDATE miembros SET nombre = :nombre WHERE id = :id
            ");
            $stmt_miembro->bindParam(':nombre', $nombre);
            $stmt_miembro->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_miembro->execute();

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error al actualizar perfil: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un miembro y su usuario asociado.
     * Debido a la configuración ON DELETE CASCADE en la clave foránea de 'miembros' a 'usuarios',
     * al eliminar el usuario, el miembro asociado también se eliminará automáticamente.
     * @param int $id El ID del miembro a eliminar.
     * @return bool True si se elimina correctamente, false en caso contrario.
     */
    public function delete($id) {
        try {
            $this->pdo->beginTransaction();

            // Primero, obtener el usuario_id asociado al miembro
            $stmt_get_user_id = $this->pdo->prepare("SELECT usuario_id FROM miembros WHERE id = :id");
            $stmt_get_user_id->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_get_user_id->execute();
            $usuario_id = $stmt_get_user_id->fetchColumn();

            if (!$usuario_id) {
                throw new Exception("Miembro no encontrado o sin usuario asociado.");
            }

            // Eliminar el usuario. Esto debería eliminar el miembro debido a ON DELETE CASCADE.
            $stmt_user = $this->pdo->prepare("DELETE FROM usuarios WHERE id = :usuario_id");
            $stmt_user->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt_user->execute();

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error al eliminar miembro: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error lógico al eliminar miembro: " . $e->getMessage());
            return false;
        }
    }
}
