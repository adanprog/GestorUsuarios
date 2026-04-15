<?php
// Iniciamos la sesión para poder eliminarla.
session_start();

// Destruye todos los datos de sesión del usuario.
session_destroy();

// Redirige al usuario de vuelta a la página de inicio de sesión.
header('Location: index.php');
exit;

