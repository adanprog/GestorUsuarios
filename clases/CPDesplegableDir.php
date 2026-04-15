<?php
/**
 * Clase CPDesplegableDir
 * ----------------------
 * Lee un archivo Excel con códigos postales, ciudades y provincias
 * para usar esos datos en los formularios de direcciones.
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/CPDir.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class CPDesplegableDir extends CPDir {
    protected $archivo;
    protected $columna;

    /**
     * Constructor: recibe el archivo Excel y la columna principal a leer.
     */
    public function __construct($archivo, $columna) {
        $this->archivo = $archivo;
        $this->columna = $columna;
    }
    
    /**
     * Obtiene los valores de una columna específica del archivo Excel.
     * Esto sirve para listas desplegables o sugerencias.
     */
    public function obtenerNombres() {
        $spreadsheet = IOFactory::load($this->archivo);
        $hoja = $spreadsheet->getActiveSheet();

        $nombres = [];

        foreach ($hoja->getRowIterator() as $fila) {
            $valor = $hoja->getCell($this->columna . $fila->getRowIndex())->getValue();
            if ($valor !== null) {
                $nombres[] = $valor;
            }
        }

        return $nombres;
    }

    /**
     * Lee el archivo Excel y devuelve un mapa de código postal a ciudad/provincia.
     * Si hay códigos postales repetidos, solo guarda el primero encontrado.
     */
    public function obtenerMapaCpCiudadProvincia($colCp = 'A', $colCiudad = 'C', $colProvincia = 'B') {
        $spreadsheet = IOFactory::load($this->archivo);
        $hoja = $spreadsheet->getActiveSheet();

        $mapa = [];
        $ultimaFila = $hoja->getHighestDataRow();

        for ($fila = 1; $fila <= $ultimaFila; $fila++) {
            $codigo = trim((string)$hoja->getCell($colCp . $fila)->getValue());
            $ciudad = trim((string)$hoja->getCell($colCiudad . $fila)->getValue());
            $provincia = trim((string)$hoja->getCell($colProvincia . $fila)->getValue());

            if ($codigo !== '') {
                if (!isset($mapa[$codigo])) {
                    $mapa[$codigo] = [
                        'ciudad' => $ciudad,
                        'provincia' => $provincia,
                    ];
                }
            }
        }

        return $mapa;
    }

}
?>
