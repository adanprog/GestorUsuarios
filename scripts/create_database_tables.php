<?php
require_once __DIR__ . '/../clases/Database.php';

try {
    $pdo = Database::getConnection();

    $pdo->exec("CREATE TABLE IF NOT EXISTS roles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL UNIQUE,
        descripcion TEXT,
        activo TINYINT(1) NOT NULL DEFAULT 1,
        permisos TEXT,
        fechaCreacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        fechaModificacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL,
        apellidos VARCHAR(150),
        DNI VARCHAR(50),
        fechaNacimiento DATE,
        telefono VARCHAR(30),
        email VARCHAR(150) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(100),
        activo TINYINT(1) NOT NULL DEFAULT 1,
        fechaCreacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        fechaModificacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS direcciones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(150) NOT NULL,
        descripcion TEXT,
        ubicacion VARCHAR(255),
        calle VARCHAR(150),
        numero VARCHAR(50),
        piso VARCHAR(50),
        puerta VARCHAR(50),
        escalera VARCHAR(50),
        codigoPostal VARCHAR(20),
        ciudad VARCHAR(100),
        provincia VARCHAR(100),
        email VARCHAR(150),
        role VARCHAR(100),
        userId INT,
        fechaCreacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        fechaModificacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (userId) REFERENCES users(id)
            ON DELETE SET NULL ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    echo "Tablas creadas correctamente.\n";
} catch (PDOException $e) {
    echo "Error creando tablas: " . $e->getMessage() . "\n";
    exit(1);
}
