<?php
/**
 * CLASE DE USUARIO (CPUser)
 * ------------------------
 * Esta clase administra los datos de los usuarios del sistema y su relación
 * con la base de datos.
 */
require_once __DIR__ . '/CPGenerico.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/CPRole.php';

class CPUser extends CPGenerico {
    protected $id;
    protected $username;
    protected $apellidos;
    protected $DNI;
    protected $fechaNacimiento;
    protected $telefono;
    protected $email;
    protected $password;
    protected $role;
    protected $activo = 1;

    /**
     * Constructor: crea un objeto usuario con todos sus datos.
     */
    public function __construct($id = null, $username = '', $email = '', $password = '', $role = null, $apellidos = '', $DNI = '', $fechaNacimiento = '', $telefono = '', $activo = 1) {
        parent::__construct($id, $username, '');
        $this->id = $id;
        $this->username = $username;
        $this->apellidos = $apellidos;
        $this->DNI = $DNI;
        $this->fechaNacimiento = $fechaNacimiento;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->setPassword($password);
        $this->role = $role;
        $this->activo = $activo;
    }

    // Métodos para obtener y cambiar los datos del usuario.
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function setUsername($u) { $this->username = $u; $this->actualizarFechaModificacion(); }
    public function getApellidos() { return $this->apellidos; }
    public function setApellidos($a) { $this->apellidos = $a; $this->actualizarFechaModificacion(); }
    public function getDNI() { return $this->DNI; }
    public function setDNI($d) { $this->DNI = $d; $this->actualizarFechaModificacion(); }
    public function getFechaNacimiento() { return $this->fechaNacimiento; }
    public function setFechaNacimiento($f) { $this->fechaNacimiento = $f; $this->actualizarFechaModificacion(); }
    public function getTelefono() { return $this->telefono; }
    public function setTelefono($t) { $this->telefono = $t; $this->actualizarFechaModificacion(); }
    public function getEmail() { return $this->email; }
    public function setEmail($e) { $this->email = $e; $this->actualizarFechaModificacion(); }
    public function getPassword() { return $this->password; }

    /**
     * Guarda la contraseña en formato seguro. Si ya está cifrada, la deja igual.
     */
    public function setPassword($p) {
        if (!empty($p)) {
            $algo = password_get_info($p)['algo'];
            if ($algo === 0 || $algo === null) {
                $this->password = password_hash($p, PASSWORD_DEFAULT);
            } else {
                $this->password = $p;
            }
            $this->actualizarFechaModificacion();
        }
    }

    /**
     * Comprueba si una contraseña en texto plano coincide con la guardada.
     */
    public function verificarPassword($passwordPlano): bool {
        return password_verify($passwordPlano, $this->password);
    }

    public function getRole() { return $this->role; }

    /**
     * Normaliza el rol para compararlo siempre en minúsculas.
     */
    public function getRoleNormalized(): string {
        $role = mb_strtolower(trim($this->role ?? 'empleado'));
        return $role === 'standard' ? 'empleado' : $role;
    }

    public function esAdministrador(): bool {
        return $this->getRoleNormalized() === 'administrador';
    }

    public function isActivo(): bool {
        return (bool)$this->activo;
    }

    public function setActivo($activo): void {
        $this->activo = $activo ? 1 : 0;
        $this->actualizarFechaModificacion();
    }

    /**
     * Devuelve si el usuario tiene un permiso concreto según su rol.
     * Para el administrador siempre devuelve verdadero.
     */
    public function hasPermission(string $permission): bool {
        $role = $this->getRoleNormalized();
        if ($role === 'administrador') {
            return true;
        }

        $permissions = [
            'empleado' => [
                'view_all_direcciones' => false,
                'view_own_direcciones' => true,
                'add_direcciones' => true,
                'delete_direcciones' => false,
                'view_creator' => false,
                'edit_direcciones' => false,
            ],
            'gerente' => [
                'view_all_direcciones' => true,
                'view_own_direcciones' => true,
                'add_direcciones' => false,
                'delete_direcciones' => false,
                'view_creator' => true,
                'edit_direcciones' => false,
            ],
            'propietario' => [
                'view_all_direcciones' => false,
                'view_own_direcciones' => true,
                'add_direcciones' => true,
                'delete_direcciones' => true,
                'view_creator' => true,
                'edit_direcciones' => true,
            ],
        ];

        if (isset($permissions[$role])) {
            return $permissions[$role][$permission] ?? false;
        }

        $roleObj = CPRole::buscarPorNombre($role);
        if (!$roleObj) {
            return false;
        }

        switch ($permission) {
            case 'view_all_direcciones':
                return $roleObj->hasPermission('view_direcciones');
            case 'view_own_direcciones':
                return true;
            case 'add_direcciones':
                return $roleObj->hasPermission('add_direcciones');
            case 'delete_direcciones':
                return $roleObj->hasPermission('delete_direcciones');
            case 'view_creator':
                return $roleObj->hasPermission('view_creator');
            case 'edit_direcciones':
                return $roleObj->hasPermission('edit_direcciones');
            default:
                return false;
        }
    }

    public function canViewAllDirecciones(): bool {
        return $this->hasPermission('view_all_direcciones');
    }
    public function canViewOwnDirecciones(): bool {
        return $this->hasPermission('view_own_direcciones');
    }
    public function canAddDirecciones(): bool {
        return $this->hasPermission('add_direcciones');
    }
    public function canDeleteDirecciones(): bool {
        return $this->hasPermission('delete_direcciones');
    }
    public function canViewCreator(): bool {
        return $this->hasPermission('view_creator');
    }
    public function canEditDirecciones(): bool {
        return $this->hasPermission('edit_direcciones');
    }

    public function setRole($r) { $this->role = $r; $this->actualizarFechaModificacion(); }

    protected static function obtenerRutaArchivo(): string {
        return dirname(__DIR__) . '/usuarios.txt';
    }

    public function aCsv(): string {
        return implode(',', [
            $this->id,
            $this->username,
            $this->apellidos,
            $this->DNI,
            $this->fechaNacimiento,
            $this->telefono,
            $this->email,
            $this->password,
            $this->role,
            $this->fechaCreacion,
            $this->fechaModificacion
        ]);
    }

    public static function desdeCsv(string $linea): ?self {
        $p = explode(',', trim($linea));

        if (count($p) === 3) {
            return new self(null, $p[0], $p[0], $p[1], $p[2]);
        }

        if (count($p) === 6) {
            $usuario = new self($p[0], $p[1], $p[1], $p[2], $p[3]);
            if (isset($p[4])) $usuario->setFechaCreacion($p[4]);
            if (isset($p[5])) $usuario->setFechaModificacion($p[5]);
            return $usuario;
        }

        if (count($p) >= 11) {
            $usuario = new self($p[0], $p[1], $p[6], $p[7], $p[8], $p[2], $p[3], $p[4], $p[5]);
            if (isset($p[9])) $usuario->setFechaCreacion($p[9]);
            if (isset($p[10])) $usuario->setFechaModificacion($p[10]);
            return $usuario;
        }

        return null;
    }

    private static $hasActivoColumn = null;

    private static function getPdo() {
        return Database::getConnection();
    }

    private static function ensureActivoColumn(): void {
        if (self::$hasActivoColumn !== null) {
            return;
        }

        $pdo = self::getPdo();
        try {
            $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'activo'");
            $columnExists = (bool)$stmt->fetch();
            if (!$columnExists) {
                $pdo->exec("ALTER TABLE users ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1");
            }
            self::$hasActivoColumn = true;
        } catch (PDOException $e) {
            self::$hasActivoColumn = false;
        }
    }

    public static function fromRow(array $row): self {
        $usuario = new self(
            $row['id'] ?? null,
            $row['username'] ?? '',
            $row['email'] ?? '',
            $row['password'] ?? '',
            $row['role'] ?? null,
            $row['apellidos'] ?? '',
            $row['DNI'] ?? '',
            $row['fechaNacimiento'] ?? '',
            $row['telefono'] ?? '',
            $row['activo'] ?? 1
        );

        if (isset($row['fechaCreacion'])) {
            $usuario->setFechaCreacion($row['fechaCreacion']);
        }
        if (isset($row['fechaModificacion'])) {
            $usuario->setFechaModificacion($row['fechaModificacion']);
        }

        return $usuario;
    }

    public function guardar(): bool {
        $pdo = self::getPdo();

        self::ensureActivoColumn();
        $useActivoField = self::$hasActivoColumn === true;

        if ($this->id) {
            if ($useActivoField) {
                $stmt = $pdo->prepare('UPDATE users SET username = ?, apellidos = ?, DNI = ?, fechaNacimiento = ?, telefono = ?, email = ?, password = ?, role = ?, activo = ?, fechaModificacion = NOW() WHERE id = ?');
                return $stmt->execute([
                    $this->username,
                    $this->apellidos,
                    $this->DNI,
                    $this->fechaNacimiento,
                    $this->telefono,
                    $this->email,
                    $this->password,
                    $this->role,
                    $this->activo,
                    $this->id
                ]);
            }

            $stmt = $pdo->prepare('UPDATE users SET username = ?, apellidos = ?, DNI = ?, fechaNacimiento = ?, telefono = ?, email = ?, password = ?, role = ?, fechaModificacion = NOW() WHERE id = ?');
            return $stmt->execute([
                $this->username,
                $this->apellidos,
                $this->DNI,
                $this->fechaNacimiento,
                $this->telefono,
                $this->email,
                $this->password,
                $this->role,
                $this->id
            ]);
        }

        if ($useActivoField) {
            $stmt = $pdo->prepare('INSERT INTO users (username, apellidos, DNI, fechaNacimiento, telefono, email, password, role, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $result = $stmt->execute([
                $this->username,
                $this->apellidos,
                $this->DNI,
                $this->fechaNacimiento,
                $this->telefono,
                $this->email,
                $this->password,
                $this->role,
                $this->activo
            ]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO users (username, apellidos, DNI, fechaNacimiento, telefono, email, password, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $result = $stmt->execute([
                $this->username,
                $this->apellidos,
                $this->DNI,
                $this->fechaNacimiento,
                $this->telefono,
                $this->email,
                $this->password,
                $this->role
            ]);
        }

        if ($result) {
            $this->id = $pdo->lastInsertId();
        }

        return $result;
    }

    public static function leerTodo(): array {
        $pdo = self::getPdo();
        $stmt = $pdo->query('SELECT * FROM users ORDER BY username');
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => self::fromRow($row), $rows);
    }

    public static function borrarPorId($id): bool {
        $stmt = self::getPdo()->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public static function buscarPorEmail($email): ?self {
        $stmt = self::getPdo()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ? self::fromRow($row) : null;
    }

    public static function agregar($email, $password, $role) {
        if (self::buscarPorEmail($email)) {
            return "¡Error! El correo ya está registrado.";
        }

        $nuevo = new self(null, $email, $email, $password, $role);
        return $nuevo->guardar() ? "Usuario creado correctamente." : "Error al guardar en la base de datos.";
    }

    public static function borrar($email) {
        $u = self::buscarPorEmail($email);
        return ($u && self::borrarPorId($u->getId())) ? "Eliminado con éxito." : "No se encontró al usuario.";
    }

    public static function actualizar($emailOriginal, $nuevoUsername, $nuevoApellidos, $nuevoDNI, $nuevoTelefono, $nuevoEmail, $nuevaPassword, $nuevoRol, $nuevoFechaNacimiento = '') {
        $u = self::buscarPorEmail($emailOriginal);
        if (!$u) return "Usuario no encontrado para editar.";

        $u->setUsername($nuevoUsername);
        $u->setApellidos($nuevoApellidos);
        $u->setDNI($nuevoDNI);
        $u->setTelefono($nuevoTelefono);
        $u->setEmail($nuevoEmail);
        if (!empty($nuevaPassword)) {
            $u->setPassword($nuevaPassword);
        }
        $u->setRole($nuevoRol);
        if (!empty($nuevoFechaNacimiento)) {
            $u->setFechaNacimiento($nuevoFechaNacimiento);
        }

        return $u->guardar() ? "Datos actualizados correctamente." : "Error al actualizar los datos.";
    }

    public static function buscarPorId($id): ?self {
        $stmt = self::getPdo()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? self::fromRow($row) : null;
    }

    public static function actualizarRolePorId($id, $nuevoRol) {
        $u = self::buscarPorId($id);
        if (!$u) return "Usuario no encontrado.";
        $u->setRole($nuevoRol);
        return $u->guardar() ? "Rol asignado correctamente al usuario." : "Error al actualizar el rol del usuario.";
    }

    public static function actualizarActivoPorId($id, $activo) {
        $u = self::buscarPorId($id);
        if (!$u) return "Usuario no encontrado.";
        $u->setActivo($activo);
        return $u->guardar() ? "Estado del usuario actualizado correctamente." : "Error al actualizar el estado del usuario.";
    }

    public static function actualizarActivoPorEmail($email, $activo) {
        $u = self::buscarPorEmail($email);
        if (!$u) return "Usuario no encontrado.";
        $u->setActivo($activo);
        return $u->guardar() ? "Estado del usuario actualizado correctamente." : "Error al actualizar el estado del usuario.";
    }
}

