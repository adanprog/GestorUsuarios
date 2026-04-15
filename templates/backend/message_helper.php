<?php
// Este archivo contiene funciones para mostrar mensajes bonitos en pantalla.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('cp_alert_class')) {
    function cp_alert_class(?string $type = null): string {
        return match ($type) {
            'success' => 'alert-success',
            'danger' => 'alert-danger',
            'warning' => 'alert-warning',
            default => 'alert-info',
        };
    }
}

if (!function_exists('cp_alert_type_from_message')) {
    // Decide el tipo de mensaje (alerta verde, roja, etc.) a partir del texto.
    function cp_alert_type_from_message(?string $message): string {
        if (empty($message)) {
            return 'info';
        }

        $lc = mb_strtolower(trim($message), 'UTF-8');

        if (preg_match('/\b(error|fall(o|ó)|denegad|no\s+(se|encontr|existe)|ya\s+est(a|á)|ya\s+|incorrecto|no\b)/u', $lc)) {
            return 'danger';
        }

        if (preg_match('/\b(éxito|exito|correcto|guardado|creado|eliminado|asignado|actualizado|registrado|bien|completado|satisfactorio)\b/u', $lc)) {
            return 'success';
        }

        return 'info';
    }
}

if (!function_exists('cp_render_alert_message')) {
    // Muestra el mensaje en HTML usando la clase de Bootstrap adecuada.
    function cp_render_alert_message(?string $message, ?string $type = null): void {
        if (empty($message)) {
            return;
        }

        $type = $type ?? cp_alert_type_from_message($message);
        $class = cp_alert_class($type);
        $icon = match ($type) {
            'success' => 'bi-check-circle-fill',
            'danger' => 'bi-exclamation-triangle-fill',
            'warning' => 'bi-exclamation-circle-fill',
            default => 'bi-info-circle-fill',
        };

        echo '<div class="alert ' . $class . ' alert-dismissible fade show shadow-sm d-flex align-items-start" role="alert" aria-live="polite">'
           . '<i class="bi ' . $icon . ' me-2 fs-5" aria-hidden="true"></i>'
           . '<div class="flex-grow-1">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div>'
           . '<button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Cerrar"></button>'
           . '</div>';
    }
}
