<?php
/**
 * Archivo: Usuario.php
 * Ubicación: /app/models/Usuario.php
 * Descripción: Clase modelo para la gestión de usuarios en la base de datos.
 * Contiene métodos para operaciones CRUD de usuarios y la interacción con la tabla 'roles'.
 * Es fundamental para la autenticación y la gestión de permisos.
 */

class Usuario {
    private $pdo; // Objeto PDO para la conexión a la base de datos

    /**
     * Constructor de la clase Usuario.
     * @param PDO $pdo Instancia de la conexión PDO a la base de datos.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene todos los usuarios del sistema, incluyendo su rol.
     * @return array Un array de objetos que representan a los usuarios.
     */
    public function getAll() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u.id, u.correo, r.nombre AS rol_nombre, r.id AS rol_id
                FROM usuarios u
                JOIN roles r ON u.rol_id = r.id
                ORDER BY u.correo ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener todos los usuarios: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un usuario por su ID.
     * @param int $id El ID del usuario.
     * @return object|false El objeto usuario si se encuentra, o false si no.
     */
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u.id, u.correo, u.contraseña, r.nombre AS rol_nombre, r.id AS rol_id
                FROM usuarios u
                JOIN roles r ON u.rol_id = r.id
                WHERE u.id = :id
            ");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener usuario por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene un usuario por su correo electrónico.
     * @param string $correo El correo electrónico del usuario.
     * @return object|false El objeto usuario si se encuentra, o false si no.
     */
    public function getByCorreo($correo) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u.id, u.correo, u.contraseña, r.nombre AS rol_nombre, r.id AS rol_id
                FROM usuarios u
                JOIN roles r ON u.rol_id = r.id
                WHERE u.correo = :correo
            ");
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener usuario por correo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea un nuevo usuario.
     * @param string $correo Correo electrónico del usuario.
     * @param string $contrasena Contraseña del usuario (se hashea antes de guardar).
     * @param int $rol_id ID del rol del usuario.
     * @return int|false El ID del nuevo usuario si se crea correctamente, o false en caso contrario.
     */
    public function create($correo, $contrasena, $rol_id) {
        try {
            $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("
                INSERT INTO usuarios (correo, contraseña, rol_id)
                VALUES (:correo, :contrasena, :rol_id)
            ");
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':contrasena', $hashed_password);
            $stmt->bindParam(':rol_id', $rol_id, PDO::PARAM_INT);
            $stmt->execute();
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error al crear usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un usuario existente.
     * @param int $id El ID del usuario a actualizar.
     * @param string $correo Nuevo correo electrónico del usuario.
     * @param int $rol_id Nuevo ID del rol del usuario.
     * @param string|null $contrasena Nueva contraseña (opcional, si se va a cambiar).
     * @return bool True si se actualiza correctamente, false en caso contrario.
     */
    public function update($id, $correo, $rol_id, $contrasena = null) {
        try {
            $sql = "UPDATE usuarios SET correo = :correo, rol_id = :rol_id";
            if ($contrasena !== null && !empty($contrasena)) {
                $sql .= ", contraseña = :contrasena";
            }
            $sql .= " WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':rol_id', $rol_id, PDO::PARAM_INT);
            if ($contrasena !== null && !empty($contrasena)) {
                $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);
                $stmt->bindParam(':contrasena', $hashed_password);
            }
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un usuario.
     * NOTA: Debido a las claves foráneas ON DELETE CASCADE, al eliminar un usuario,
     * los registros asociados en 'miembros' o 'instructores' también se eliminarán.
     * @param int $id El ID del usuario a eliminar.
     * @return bool True si se elimina correctamente, false en caso contrario.
     */
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los roles disponibles.
     * @return array Un array de objetos que representan los roles.
     */
    public function getAllRoles() {
        try {
            $stmt = $this->pdo->prepare("SELECT id, nombre FROM roles ORDER BY nombre ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener todos los roles: " . $e->getMessage());
            return [];
        }
    }
}
