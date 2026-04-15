<?php
/**
 * INICIO (HOME)
 * ------------
 * Esta es la página de bienvenida que se ve justo después de entrar.
 */
error_reporting(E_ALL ^ E_NOTICE); // Evita que salgan avisos molestos
session_start();

// Cargamos la cabecera (incluye estilos y seguridad) y el menú lateral
require_once("head.php");
require_once("sidebar.php");
?>

<!-- El contenido principal de la bienvenida -->
<main class="flex-grow-1 d-flex justify-content-center align-items-center p-3 bg-light" style="min-height: calc(100vh - 56px);">
    <div class="text-center">
        <!-- Títulos de bienvenida -->
        <h1 class="display-4 fw-bold text-dark">¡Hola de nuevo!</h1>
        <p class="lead text-secondary opacity-75">Has entrado a tu panel de administración personal.</p>
        
        <!-- Línea decorativa -->
        <div class="mt-3 pt-4 border-top">
            <p class="small text-muted mb-0">Utiliza el menú de la izquierda para gestionar tus datos y documentos.</p>
        </div>
    </div>
</main>

<!-- Cerramos con el pie de página -->
<?php require_once("footer.php"); ?>