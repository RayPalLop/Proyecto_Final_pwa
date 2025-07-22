<?php
/**
 * Archivo: Database.php
 * Ubicación: /app/models/Database.php
 * Descripción: Clase para gestionar la conexión a la base de datos utilizando PDO.
 * Implementa el patrón Singleton para asegurar una única instancia de la conexión PDO.
 */

class Database {
    private static $instance = null; // Almacena la única instancia de la conexión PDO
    private $pdo; // Objeto PDO para la conexión a la base de datos

    // Constructor privado para evitar la instanciación directa
    private function __construct() {
        // Cargar la configuración de la base de datos (se asume que config.php ya está incluido
        // o que las constantes DB_HOST, DB_NAME, DB_USER, DB_PASS están definidas globalmente).
        // Para asegurar que config.php se carga, lo incluiremos aquí o nos aseguraremos que
        // se cargue antes de usar Database::getConnection().
        // Por la estructura actual, config.php ya define las constantes y la conexión $pdo.
        // Para una clase Database, es mejor que ella misma se encargue de la conexión.

        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Error de conexión a la base de datos desde Database.php: " . $e->getMessage());
            // En un entorno de producción, es crucial no exponer detalles del error al usuario final.
            die("Error de conexión a la base de datos. Por favor, inténtelo de nuevo más tarde.");
        }
    }

    /**
     * Obtiene la única instancia de la conexión PDO.
     * Si la instancia no existe, la crea.
     * @return PDO La instancia de la conexión PDO.
     */
    public static function getConnection() {
        // Si no hay una instancia de la clase Database, crearla.
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        // Devolver el objeto PDO almacenado en la instancia.
        return self::$instance->pdo;
    }

    // Métodos mágicos para prevenir la clonación y la deserialización de la instancia
    private function __clone() {}
    public function __wakeup() {}
}
