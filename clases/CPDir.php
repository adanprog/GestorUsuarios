<?php
/**
 * CLASE DE DIRECCIÓN (CPDir)
 * -------------------------
 * Representa una dirección física y ofrece funciones para leer y guardar
 * direcciones en la base de datos, además de crear carpetas en el disco.
 */
require_once __DIR__ . '/CPUser.php';
require_once __DIR__ . '/Database.php';

class CPDir extends CPUser {
    protected $calle;
    protected $numero;
    protected $piso;
    protected $puerta;
    protected $escalera;
    protected $codigoPostal;
    protected $ciudad;
    protected $provincia;
    protected $ubicacion;
    protected $userId;

    /**
     * Constructor: crea el objeto dirección con todos sus detalles.
     */
    public function __construct($id = null, $nombre = '', $descripcion = '', $ubicacion = '', $calle='', $numero='', $piso='', $puerta='', $escalera='', $codigoPostal='', $ciudad='', $provincia='', $email='', $password='', $role='propietario', $userId=null) {
        parent::__construct($id, $nombre, $email, $password, $role);

        $this->setDescripcion($descripcion);
        $this->ubicacion = $ubicacion;
        $this->calle = $calle;
        $this->numero = $numero;
        $this->piso = $piso;
        $this->puerta = $puerta;
        $this->escalera = $escalera;
        $this->codigoPostal = $codigoPostal;
        $this->ciudad = $ciudad;
        $this->provincia = $provincia;
        $this->userId = $userId;
    }

    private static function getPdo() {
        return Database::getConnection();
    }

    public static function fromRow(array $row): self {
        $dir = new self(
            $row['id'] ?? null,
            $row['nombre'] ?? '',
            $row['descripcion'] ?? '',
            $row['ubicacion'] ?? '',
            $row['calle'] ?? '',
            $row['numero'] ?? '',
            $row['piso'] ?? '',
            $row['puerta'] ?? '',
            $row['escalera'] ?? '',
            $row['codigoPostal'] ?? '',
            $row['ciudad'] ?? '',
            $row['provincia'] ?? '',
            $row['email'] ?? '',
            '',
            $row['role'] ?? 'propietario',
            $row['userId'] ?? null
        );

        if (isset($row['fechaCreacion'])) {
            $dir->setFechaCreacion($row['fechaCreacion']);
        }
        if (isset($row['fechaModificacion'])) {
            $dir->setFechaModificacion($row['fechaModificacion']);
        }

        return $dir;
    }

    public function guardar(): bool {
        $pdo = self::getPdo();

        if ($this->id) {
            $stmt = $pdo->prepare(
                'UPDATE direcciones SET nombre = ?, descripcion = ?, ubicacion = ?, calle = ?, numero = ?, piso = ?, puerta = ?, escalera = ?, codigoPostal = ?, ciudad = ?, provincia = ?, email = ?, role = ?, userId = ?, fechaModificacion = NOW() WHERE id = ?'
            );
            return $stmt->execute([
                $this->nombre,
                $this->descripcion,
                $this->ubicacion,
                $this->calle,
                $this->numero,
                $this->piso,
                $this->puerta,
                $this->escalera,
                $this->codigoPostal,
                $this->ciudad,
                $this->provincia,
                $this->email,
                $this->role,
                $this->userId,
                $this->id
            ]);
        }

        $stmt = $pdo->prepare(
            'INSERT INTO direcciones (nombre, descripcion, ubicacion, calle, numero, piso, puerta, escalera, codigoPostal, ciudad, provincia, email, role, userId) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $result = $stmt->execute([
            $this->nombre,
            $this->descripcion,
            $this->ubicacion,
            $this->calle,
            $this->numero,
            $this->piso,
            $this->puerta,
            $this->escalera,
            $this->codigoPostal,
            $this->ciudad,
            $this->provincia,
            $this->email,
            $this->role,
            $this->userId
        ]);

        if ($result) {
            $this->id = $pdo->lastInsertId();
        }

        return $result;
    }

    public static function leerTodo(): array {
        $stmt = self::getPdo()->query('SELECT * FROM direcciones ORDER BY nombre');
        $rows = $stmt->fetchAll();
        return array_map(fn($row) => self::fromRow($row), $rows);
    }

    public static function buscarPorId($id): ?self {
        $stmt = self::getPdo()->prepare('SELECT * FROM direcciones WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? self::fromRow($row) : null;
    }

    public static function borrarPorId($id): bool {
        $stmt = self::getPdo()->prepare('DELETE FROM direcciones WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Devuelve la dirección escrita de forma completa.
     */
    public function obtenerDireccionCompleta(): string {
        $partes = array_filter([
            $this->calle,
            $this->numero,
            $this->piso ? 'Piso ' . $this->piso : null,
            $this->puerta ? 'Puerta ' . $this->puerta : null,
            $this->codigoPostal,
            $this->ciudad,
            $this->provincia
        ], fn($valor) => trim((string)$valor) !== '');

        return implode(', ', $partes);
    }

    public function obtenerGoogleMapsUrl(): string {
        $partes = array_filter([
            $this->calle,
            $this->numero,
            $this->piso ? 'Piso ' . $this->piso : null,
            $this->puerta ? 'Puerta ' . $this->puerta : null,
            $this->codigoPostal,
            $this->ciudad,
            $this->provincia,
            'España'
        ], fn($valor) => trim((string)$valor) !== '');

        $consulta = rawurlencode(implode(', ', $partes));
        return "https://www.google.com/maps/search/?api=1&query={$consulta}";
    }

    public function getUbicacion() { return $this->ubicacion; }
    public function getCalle() { return $this->calle; }
    public function getNumero() { return $this->numero; }
    public function getPiso() { return $this->piso; }
    public function getPuerta() { return $this->puerta; }
    public function getEscalera() { return $this->escalera; }
    public function getCodigoPostal() { return $this->codigoPostal; }
    public function getCiudad() { return $this->ciudad; }
    public function getProvincia() { return $this->provincia; }
    public function getUserId() { return $this->userId; }

    protected static function obtenerRutaArchivo(): string {
        return dirname(__DIR__) . '/direcciones.txt';
    }

    public function aCsv(): string {
        return implode('|', [
            $this->id, $this->nombre, $this->descripcion, $this->ubicacion,
            $this->calle, $this->numero, $this->piso, $this->puerta, $this->escalera,
            $this->codigoPostal, $this->ciudad, $this->provincia,
            $this->email, $this->password, $this->role,
            $this->fechaCreacion, $this->fechaModificacion, $this->userId
        ]);
    }

    public static function desdeCsv(string $linea): self {
        $p = explode('|', trim($linea));
        $res = new self($p[0],$p[1],$p[2],$p[3],$p[4],$p[5],$p[6],$p[7],$p[8],$p[9],$p[10],$p[11],$p[12],$p[13],$p[14], isset($p[17]) ? $p[17] : null);

        if (isset($p[15])) $res->setFechaCreacion($p[15]);
        if (isset($p[16])) $res->setFechaModificacion($p[16]);
        return $res;
    }

    public function sincronizarConDisco() {
        if (!empty($this->ubicacion) && !file_exists($this->ubicacion)) {
            mkdir($this->ubicacion, 0777, true);
        }
    }

    public function mantenimiento(array $subcarpetas) {
        $this->sincronizarConDisco();
        foreach($subcarpetas as $s) {
            if(!empty($s)) {
                mkdir($this->ubicacion . DIRECTORY_SEPARATOR . $s, 0777, true);
            }
        }
    }
}
?>
