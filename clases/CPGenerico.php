<?php
/**
 * CLASE BASE GENÉRICA (CPGenerico)
 * -------------------------------
 * Esta clase contiene funciones básicas para guardar y leer información,
 * y sirve como base para otras clases como CPUser y CPDir.
 */
abstract class CPGenerico {
    protected $id;               // Identificador único del elemento.
    protected $nombre;           // Nombre principal.
    protected $descripcion;      // Texto descriptivo.
    protected $fechaCreacion;    // Fecha en que se creó el elemento.
    protected $fechaModificacion;// Fecha en que se modificó por última vez.
    
    /**
     * Constructor: crea el objeto con un ID, nombre y descripción.
     * Si no se da un ID, se genera uno único automáticamente.
     */
    public function __construct($id, $nombre, $descripcion) {
        $this->id = $id ?? uniqid();
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->fechaCreacion = date('Y-m-d H:i:s');
        $this->fechaModificacion = date('Y-m-d H:i:s');
    }

    // Funciones para leer los valores guardados en el objeto.
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getDescripcion() { return $this->descripcion; }
    public function getFechaCreacion() { return $this->fechaCreacion; }
    public function getFechaModificacion() { return $this->fechaModificacion; }

    // Funciones para cambiar los valores del objeto.
    public function setId($id) { $this->id = $id; }
    public function setNombre($n) { 
        $this->nombre = $n; 
        $this->actualizarFechaModificacion(); 
    }
    public function setDescripcion($d) { 
        $this->descripcion = $d; 
        $this->actualizarFechaModificacion(); 
    }
    public function setFechaCreacion($f) { $this->fechaCreacion = $f; }
    public function setFechaModificacion($f) { $this->fechaModificacion = $f; }

    /**
     * Actualiza la marca de tiempo cuando algo cambia.
     */
    protected function actualizarFechaModificacion() { 
        $this->fechaModificacion = date('Y-m-d H:i:s'); 
    }

    // Métodos que deben implementar las clases hijas.
    abstract static protected function obtenerRutaArchivo(): string;
    abstract public function aCsv(): string;
    abstract static public function desdeCsv(string $linea): ?self;

    /**
     * Lee todos los registros del archivo correspondiente y devuelve objetos.
     */
    public static function leerTodo(): array {
        $ruta = static::obtenerRutaArchivo();
        $lista = [];

        if (!file_exists($ruta)) return $lista;

        foreach (file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
            $lista[] = static::desdeCsv($linea);
        }
        return $lista;
    }

    /**
     * Guarda o actualiza el registro en el archivo.
     */
    public function guardar(): bool {
        $elementos = static::leerTodo();
        $encontrado = false;

        foreach ($elementos as $indice => $item) {
            if ($item->getId() === $this->id) {
                $elementos[$indice] = $this;
                $encontrado = true;
                break;
            }
        }

        if (!$encontrado) {
            $elementos[] = $this;
        }

        return static::escribirAArchivo($elementos);
    }

    /**
     * Elimina un registro del archivo usando su identificador.
     */
    public static function borrarPorId($id): bool {
        $nuevaLista = array_filter(static::leerTodo(), function($item) use ($id){
            return $item->getId() !== $id;
        });
        return static::escribirAArchivo($nuevaLista);
    }

    /**
     * Escribe los datos en el archivo de forma segura.
     */
    protected static function escribirAArchivo(array $lista): bool {
        $lineas = array_map(fn($item) => $item->aCsv(), $lista);
        return file_put_contents(static::obtenerRutaArchivo(), implode("\n", $lineas) . "\n", LOCK_EX) !== false;
    }
    
    public function formatear_fecha($fecha, $formate, $formats){
        $fec_res = date_create_from_format($formate, $fecha);
        if($fec_res){
            return $fec_res->format($formats);
        }
        return false;
    }
}
?>