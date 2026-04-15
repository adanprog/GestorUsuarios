<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestor Personal - Panel de Control</title>
    <!-- Incluimos Bootstrap para el diseño visual -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Iconos de Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<?php
require_once __DIR__ . '/../helpers.php';
/**
 * LÓGICA DE RUTAS Y SEGURIDAD
 * Este trozo de código decide dónde está la raíz del proyecto para que los enlaces nunca fallen.
 */
$rutaActual = dirname($_SERVER['SCRIPT_NAME']);
// Limpiamos la ruta si estamos dentro de la carpeta 'templates'
$raizProyecto = str_replace(['/templates/frontend', '/templates/backend', '/templates'], '', $rutaActual);
$raizProyecto = rtrim($raizProyecto, '/\\');

// Iniciamos la sesión para reconocer quién es el usuario
if (session_status() === PHP_SESSION_NONE) session_start();

$rolUsuario = $_SESSION['role'] ?? 'invitado';

// SEGURIDAD: Si alguien intenta entrar a una página interna sin loguearse, lo echamos al login
if (!isset($_SESSION['logged_in']) && basename($_SERVER['SCRIPT_NAME']) !== 'index.php') {
    header("Location: $raizProyecto/index.php");
    exit();
}
?>
<body class="d-flex flex-column min-vh-100">
    <!-- BARRA DE NAVEGACIÓN SUPERIOR -->
    <nav class="navbar bg-white border-bottom shadow-sm mb-0 py-2">
        <div class="container-fluid px-4">
            <!-- Logo principal que nos lleva al inicio -->
<a class="navbar-brand fw-bold d-flex align-items-center" href="<?php echo h($raizProyecto); ?>/index.php">
                <i class="bi bi-person-badge-fill me-2 text-primary fs-4"></i>
                <span class="d-none d-sm-inline">Gestor Personal</span>
            </a>
            
            <!-- Derecha: Fecha actual, rol de usuario y botones -->
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="text-muted d-none d-md-inline small">
                    <i class="bi bi-calendar3 me-1"></i><?php echo date('d M Y'); ?>
                </span>
                <span class="badge bg-primary text-white text-uppercase small">
                    <i class="bi bi-person-badge me-1"></i><?php echo htmlspecialchars($rolUsuario); ?>
                </span>
            </div>
        </div>
    </nav>
    <!-- Este DIV abre la estructura de dos columnas (Menú Izquierda + Contenido Derecha) -->
    <div class="d-flex flex-column flex-md-row flex-grow-1">