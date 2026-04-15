<?php
/**
 * MENÚ LATERAL (SIDEBAR)
 * ----------------------
 * Se encarga de mostrar las opciones de navegación a la izquierda de la pantalla.
 */
if (session_status() === PHP_SESSION_NONE)
    session_start();

// Obtenemos los datos del usuario logueado
$correoUsuario = $_SESSION['email'] ?? 'invitado@correo.com';
$rolUsuario = $_SESSION['role'] ?? 'invitado';

// Calculamos la ruta raíz para que el botón de Cerrar Sesión funcione siempre
$rutaScript = dirname($_SERVER['SCRIPT_NAME']);
$raizWeb = str_replace(['/templates/frontend', '/templates/backend', '/templates'], '', $rutaScript);
$raizWeb = rtrim($raizWeb, '/\\');

// Detectamos en qué página estamos para marcarla en azul
$paginaActual = basename($_SERVER['PHP_SELF']);

// Lista de enlaces que salen en el menú
$opcionesMenu = [
    ['enlace' => 'home.php', 'icono' => 'bi-house-door', 'etiqueta' => 'Inicio'],
    ['enlace' => 'direcciones.php', 'icono' => 'bi-geo-alt', 'etiqueta' => 'Mis Direcciones'],
];

// Solo los Administradores ven las opciones de Usuarios y Roles
if ($rolUsuario === 'administrador') {
    $opcionesMenu[] = ['enlace' => 'usuarios.php', 'icono' => 'bi-people', 'etiqueta' => 'Usuarios'];
    $opcionesMenu[] = ['enlace' => 'roles.php', 'icono' => 'bi-shield-lock', 'etiqueta' => 'Roles'];
}
?>

