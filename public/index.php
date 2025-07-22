<?php
/**
 * Archivo: index.php
 * Ubicación: /public/index.php
 * Descripción: Página principal del sistema de gestión del gimnasio.
 * Contiene el formulario de inicio de sesión y maneja la autenticación.
 */

// Incluir el archivo de configuración de la base de datos y la sesión.
// La ruta es relativa desde public/index.php hasta config/config.php
require_once '../config/config.php';

// Si el usuario ya está logueado (sesión activa), redirigir al dashboard.
// Esto evita que un usuario autenticado vea la página de login.
if (isset($_SESSION['usuario_id']) && isset($_SESSION['rol_nombre'])) {
    header('Location: dashboard.php'); // Redirige al dashboard principal
    exit(); // Termina la ejecución del script para asegurar la redirección
}

$error_message = ''; // Variable para almacenar y mostrar mensajes de error de login.

// Verificar si la solicitud HTTP es de tipo POST, lo que indica que el formulario ha sido enviado.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y sanear los datos del formulario.
    // trim() elimina espacios en blanco al inicio y al final.
    // ?? '' proporciona un valor por defecto si la variable no está definida (PHP 7+).
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');

    // Validar que los campos de correo y contraseña no estén vacíos.
    if (empty($correo) || empty($contrasena)) {
        $error_message = 'Por favor, introduce tu correo y contraseña.';
    } else {
        try {
            // Preparar la consulta SQL para buscar el usuario por correo.
            // Usamos sentencias preparadas (:correo) para prevenir inyecciones SQL.
            // Se realiza un JOIN con la tabla 'roles' para obtener el nombre del rol.
            $stmt = $pdo->prepare("SELECT u.id, u.contraseña, r.nombre AS rol_nombre
                                   FROM usuarios u
                                   JOIN roles r ON u.rol_id = r.id
                                   WHERE u.correo = :correo");
            // Vincular el parámetro :correo con el valor proporcionado por el usuario.
            $stmt->bindParam(':correo', $correo);
            // Ejecutar la consulta preparada.
            $stmt->execute();
            // Recuperar la primera fila del resultado como un objeto.
            $usuario = $stmt->fetch(PDO::FETCH_OBJ);

            // Verificar si se encontró el usuario y si la contraseña es correcta.
            // IMPORTANTE: password_verify() es la función correcta para verificar contraseñas hasheadas
            // con password_hash() en PHP. Si las contraseñas en tu DB fueron insertadas con PASSWORD() de MySQL,
            // esta verificación fallará a menos que uses una lógica diferente o regeneres los hashes.
            // Para este ejemplo, asumimos que las contraseñas en la DB están hasheadas con password_hash() de PHP.
            if ($usuario && password_verify($contrasena, $usuario->contraseña)) {
                // Si la autenticación es exitosa, establecer variables de sesión.
                // Estas variables se usarán para mantener al usuario logueado y para controlar el acceso.
                $_SESSION['usuario_id'] = $usuario->id;
                $_SESSION['rol_nombre'] = $usuario->rol_nombre;
                $_SESSION['correo'] = $correo; // Opcional: almacenar el correo en sesión para mostrarlo

                // Redirigir al usuario al dashboard después del login exitoso.
                header('Location: dashboard.php');
                exit(); // Asegura que no se ejecute más código PHP después de la redirección.
            } else {
                // Si el usuario no se encuentra o la contraseña es incorrecta.
                $error_message = 'Correo o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            // Capturar cualquier error de la base de datos durante el proceso de login.
            error_log("Error de login en la base de datos: " . $e->getMessage());
            $error_message = 'Ocurrió un error inesperado al intentar iniciar sesión. Por favor, inténtalo de nuevo.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Gestión de Gimnasio</title>
    <!-- Incluir Bootstrap 5 CSS desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluir nuestro archivo CSS personalizado -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <h2 class="text-center mb-4">Acceso al Gimnasio</h2>
        <?php if ($error_message): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        <form action="index.php" method="POST">
            <div class="mb-3">
                <label for="correo" class="form-label visually-hidden">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" placeholder="Correo Electrónico" required>
            </div>
            <div class="mb-4">
                <label for="contrasena" class="form-label visually-hidden">Contraseña</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena" placeholder="Contraseña" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </div>
        </form>
    </div>

    <!-- Incluir Bootstrap 5 JS (Popper y JS Bundle) desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Incluir nuestro archivo JavaScript personalizado -->
    <script src="js/main.js"></script>
    <script>
        // Pequeño script para enfocar el primer campo de entrada al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const correoInput = document.getElementById('correo');
            if (correoInput) {
                correoInput.focus();
            }
        });
    </script>
</body>
</html>
