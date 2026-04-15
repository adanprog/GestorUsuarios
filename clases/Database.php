<?php
/**
 * Database connection helper usando PDO.
 *
 * Esta clase se encarga de crear una sola conexión a la base de datos
 * y devolverla cuando otras partes del proyecto la necesiten.
 */
class Database {
    // Aquí guardamos la conexión activa para no crearla varias veces.
    private static $pdo = null;

    public static function getConnection() {
        // Si ya tenemos una conexión creada, la devolvemos directamente.
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        // Cargamos la configuración desde el archivo config.php.
        $config = require dirname(__DIR__) . '/config.php';

        $host = $config['db_host'];
        $port = $config['db_port'] ?? '3306';
        $db   = $config['db_name'];
        $user = $config['db_user'];
        $pass = $config['db_pass'];
        $charset = $config['db_charset'];

        // Creamos la dirección de conexión para MySQL.
        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";

        $options = [
            // Hacemos que PDO lance errores cuando algo va mal.
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Queremos recibir los resultados como arreglos asociativos.
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Evitamos emular consultas preparadas por seguridad.
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        // Creamos la conexión y la guardamos en la variable estática.
        self::$pdo = new PDO($dsn, $user, $pass, $options);
        return self::$pdo;
    }
}
