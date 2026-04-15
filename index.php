<?php
// Mostrar errores importantes de PHP, pero ocultar avisos pequeños que no rompen el sitio.
error_reporting(E_ALL ^ E_NOTICE);

// Iniciamos la sesión para poder recordar al usuario que está conectado.
session_start();

// Procesamos el login antes de enviar cualquier salida al navegador.
require_once("templates/backend/login_back.php");
?>

<?php
// Cargamos la cabecera común de la página (estilos, seguridad y menú si está logueado).
require_once("templates/frontend/head.php");
?>

<?php
// Mostramos el formulario de inicio de sesión.
require_once("templates/frontend/login.php");
?>

<?php
// Cerramos la página con el pie de página común.
require_once("templates/frontend/footer.php");
?>
