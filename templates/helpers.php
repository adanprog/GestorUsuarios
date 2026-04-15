<?php
// helpers.php contiene funciones pequeñas que se usan en muchas partes del proyecto.

if (!function_exists('h')) {
    // Limpia un texto para que se pueda mostrar en HTML sin peligro.
    // Por ejemplo, convierte caracteres especiales para que no rompan la página.
    function h($value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('cp_post_string')) {
    // Lee un valor enviado desde un formulario y lo devuelve como texto limpio.
    function cp_post_string(string $key, string $default = ''): string {
        $value = $_POST[$key] ?? $default;
        return trim((string)$value);
    }
}

if (!function_exists('cp_post_int')) {
    // Lee un valor enviado desde un formulario y lo convierte a número entero.
    function cp_post_int(string $key, int $default = 0): int {
        $value = $_POST[$key] ?? null;
        if ($value === null) {
            return $default;
        }
        return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['default' => $default]]);
    }
}
