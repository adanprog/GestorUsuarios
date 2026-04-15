<?php
/**
 * CLASE DE ROL (CPRole)
 * ---------------------
 * Gestiona los roles y permisos disponibles en el sistema.
 */
require_once __DIR__ . '/CPGenerico.php';
require_once __DIR__ . '/Database.php';

class CPRole extends CPGenerico {
    protected $id;
    protected $nombre;
    protected $descripcion;
    protected $activo;
    protected $permisos;

    /**
     * Constructor: crea el objeto rol con sus datos.
     */
    public function __construct($id = null, $nombre = '', $descripcion = '', $activo = 1, $permisos = null) {
        parent::__construct($id, $nombre, $descripcion);
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->activo = $activo ? 1 : 0;
        $this->setPermisos($permisos);
    }

    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function setNombre($n) { $this->nombre = $n; $this->actualizarFechaModificacion(); }
    public function getDescripcion() { return $this->descripcion; }
    public function setDescripcion($d) { $this->descripcion = $d; $this->actualizarFechaModificacion(); }
    public function isActivo() { return $this->activo == 1; }
    public function setActivo($activo) { $this->activo = $activo ? 1 : 0; $this->actualizarFechaModificacion(); }

    /**
     * Devuelve la lista de permisos del rol. Si no hay permisos, usa valores por defecto.
     */
    public function getPermisos(): array {
        return $this->permisos ?? [
            'view_direcciones' => false,
            'view_creator' => false,
            'add_direcciones' => false,
            'edit_direcciones' => false,
            'delete_direcciones' => false,
        ];
    }

    /**
     * Guarda los permisos del rol de forma segura.
     */
    public function setPermisos($permisos): void {
        if (is_string($permisos)) {
            $decoded = json_decode($permisos, true);
            $permisos = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($permisos)) {
            $permisos = [];
        }

        $this->permisos = array_merge([
            'view_direcciones' => false,
            'view_creator' => false,
            'add_direcciones' => false,
            'edit_direcciones' => false,
            'delete_direcciones' => false,
        ], array_intersect_key($permisos, [
            'view_direcciones' => true,
            'view_creator' => true,
            'add_direcciones' => true,
            'edit_direcciones' => true,
            'delete_direcciones' => true,
        ]));

        $this->actualizarFechaModificacion();
    }

    public function hasPermission(string $permiso): bool {
        return $this->getPermisos()[$permiso] ?? false;
    }

    protected static function obtenerRutaArchivo(): string {
        return dirname(__DIR__) . '/roles.txt';
    }

    public function aCsv(): string {
        return implode('|', [
            $this->id,
            str_replace('|', ' ', $this->nombre),
            str_replace('|', ' ', $this->descripcion),
            $this->activo,
            json_encode($this->getPermisos()),
            $this->fechaCreacion,
            $this->fechaModificacion
        ]);
    }

    public static function desdeCsv(string $linea): ?self {
        $p = explode('|', trim($linea));
        if (count($p) < 7) return null;

        $rol = new self($p[0], $p[1], $p[2], $p[3], $p[4]);
        $rol->setFechaCreacion($p[5]);
        $rol->setFechaModificacion($p[6]);
        return $rol;
    }

    private static function getPdo() {
        return Database::getConnection();
    }

    private static function rolesTableHasPermisosColumn(): bool {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }

        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SHOW COLUMNS FROM roles LIKE 'permisos'");
        $stmt->execute();
        $hasColumn = (bool) $stmt->fetch();

        if (!$hasColumn) {
            $pdo->exec("ALTER TABLE roles ADD COLUMN permisos TEXT AFTER activo");
            $hasColumn = true;
        }

        return $cached = $hasColumn;
    }

    public function guardar(): bool {
        $pdo = self::getPdo();
        $permisosJson = json_encode($this->getPermisos());

        if ($this->id) {
            if (self::rolesTableHasPermisosColumn()) {
                $stmt = $pdo->prepare('UPDATE roles SET nombre = ?, descripcion = ?, activo = ?, permisos = ?, fechaModificacion = NOW() WHERE id = ?');
                return $stmt->execute([
                    $this->nombre,
                    $this->descripcion,
                    $this->activo,
                    $permisosJson,
                    $this->id
                ]);
            }

            $stmt = $pdo->prepare('UPDATE roles SET nombre = ?, descripcion = ?, activo = ?, fechaModificacion = NOW() WHERE id = ?');
            return $stmt->execute([
                $this->nombre,
                $this->descripcion,
                $this->activo,
                $this->id
            ]);
        }

        if (self::rolesTableHasPermisosColumn()) {
            $stmt = $pdo->prepare('INSERT INTO roles (nombre, descripcion, activo, permisos) VALUES (?, ?, ?, ?)');
            $result = $stmt->execute([
                $this->nombre,
                $this->descripcion,
                $this->activo,
                $permisosJson
            ]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO roles (nombre, descripcion, activo) VALUES (?, ?, ?)');
            $result = $stmt->execute([
                $this->nombre,
                $this->descripcion,
                $this->activo
            ]);
        }

        if ($result) {
            $this->id = $pdo->lastInsertId();
        }

        return $result;
    }

    public static function fromRow(array $row): self {
        $rol = new self(
            $row['id'] ?? null,
            $row['nombre'] ?? '',
            $row['descripcion'] ?? '',
            $row['activo'] ?? 1,
            $row['permisos'] ?? null
        );

        if (isset($row['fechaCreacion'])) {
            $rol->setFechaCreacion($row['fechaCreacion']);
        }
        if (isset($row['fechaModificacion'])) {
            $rol->setFechaModificacion($row['fechaModificacion']);
        }

        return $rol;
    }

    public static function leerTodo(): array {
        $stmt = self::getPdo()->query('SELECT * FROM roles ORDER BY nombre');
        $rows = $stmt->fetchAll();
        return array_map(fn($row) => self::fromRow($row), $rows);
    }

    public static function borrarPorId($id): bool {
        $stmt = self::getPdo()->prepare('DELETE FROM roles WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public static function buscarPorId($id): ?self {
        $stmt = self::getPdo()->prepare('SELECT * FROM roles WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? self::fromRow($row) : null;
    }

    public static function buscarPorNombre($nombre): ?self {
        $stmt = self::getPdo()->prepare('SELECT * FROM roles WHERE LOWER(nombre) = LOWER(?) LIMIT 1');
        $stmt->execute([$nombre]);
        $row = $stmt->fetch();
        return $row ? self::fromRow($row) : null;
    }

    public static function agregar($nombre, $descripcion, $activo, array $permisos = []) {
        if (self::buscarPorNombre($nombre)) return "¡Error! Ya existe un rol con ese nombre.";
        $nuevo = new self(null, $nombre, $descripcion, $activo, $permisos);
        return $nuevo->guardar() ? "Rol creado correctamente." : "Error al guardar el rol.";
    }

    public static function actualizar($id, $nombre, $descripcion, $activo, array $permisos = []) {
        $rol = self::buscarPorId($id);
        if (!$rol) return "Rol no encontrado para editar.";
        $existe = self::buscarPorNombre($nombre);
        if ($existe && $existe->getId() !== $rol->getId()) {
            return "¡Error! Ya existe otro rol con ese nombre.";
        }

        $rol->setNombre($nombre);
        $rol->setDescripcion($descripcion);
        $rol->setActivo($activo);
        $rol->setPermisos($permisos);
        return $rol->guardar() ? "Rol actualizado correctamente." : "Error al actualizar el rol.";
    }

    public static function borrar($id) {
        return self::borrarPorId($id) ? "Rol eliminado correctamente." : "No se encontró el rol.";
    }
}
