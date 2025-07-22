<?php
/**
 * Archivo: AuthController.php
 * Ubicación: /app/controllers/AuthController.php
 * Descripción: Controlador para manejar la lógica de autenticación de usuarios.
 * Incluye métodos para el inicio de sesión y el cierre de sesión.
 */

// Incluir el modelo Usuario para interactuar con la tabla de usuarios
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private $pdo; // Objeto PDO para la conexión a la base de datos
    private $usuarioModel; // Instancia del modelo Usuario

    /**
     * Constructor del controlador de autenticación.
     * @param PDO $pdo Instancia de la conexión PDO a la base de datos.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->usuarioModel = new Usuario($pdo);
    }

    /**
     * Procesa el intento de inicio de sesión.
     * Si las credenciales son válidas, establece la sesión y redirige al dashboard.
     * En caso contrario, establece un mensaje de error.
     * @param string $correo Correo electrónico del usuario.
     * @param string $contrasena Contraseña del usuario.
     */
    public function login($correo, $contrasena) {
        $error_message = '';

        // Buscar el usuario por correo electrónico
        $usuario = $this->usuarioModel->getByCorreo($correo);

        // Verificar si el usuario existe y si la contraseña es correcta
        // password_verify() es crucial para contraseñas hasheadas con password_hash()
        if ($usuario && password_verify($contrasena, $usuario->contraseña)) {
            // Autenticación exitosa: establecer variables de sesión
            $_SESSION['usuario_id'] = $usuario->id;
            $_SESSION['rol_nombre'] = $usuario->rol_nombre;
            $_SESSION['correo'] = $usuario->correo; // Almacenar el correo en sesión

            // Redirigir al dashboard
            header('Location: dashboard.php');
            exit();
        } else {
            // Credenciales inválidas
            $error_message = 'Correo o contraseña incorrectos.';
        }
        return $error_message; // Devuelve el mensaje de error si falla el login
    }

    /**
     * Cierra la sesión del usuario actual.
     * Destruye todas las variables de sesión y redirige a la página de inicio de sesión.
     */
    public function logout() {
        // Eliminar todas las variables de sesión
        $_SESSION = array();

        // Si se usan sesiones basadas en cookies, también se debe eliminar la cookie de sesión.
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finalmente, destruir la sesión
        session_destroy();

        // Redirigir a la página de inicio de sesión
        header('Location: index.php');
        exit();
    }
}
