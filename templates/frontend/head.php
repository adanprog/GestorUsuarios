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
    <style>
        body.dark-mode {
            background-color: #0f1720;
            color: #e9ecef;
        }
        body.dark-mode .navbar,
        body.dark-mode .bg-white,
        body.dark-mode .card,
        body.dark-mode .dropdown-menu,
        body.dark-mode .modal-content,
        body.dark-mode .table,
        body.dark-mode .table thead,
        body.dark-mode .form-control,
        body.dark-mode .form-select,
        body.dark-mode .form-check-label,
        body.dark-mode .list-group-item,
        body.dark-mode .border-end,
        body.dark-mode .bg-light {
            background-color: #111827 !important;
            color: #e9ecef !important;
            border-color: #374151 !important;
        }
        body.dark-mode .navbar {
            box-shadow: 0 0.15rem 0.5rem rgba(0, 0, 0, 0.35);
        }
        body.dark-mode .border-top,
        body.dark-mode .border-bottom,
        body.dark-mode .card,
        body.dark-mode .card.border,
        body.dark-mode .table,
        body.dark-mode .table th,
        body.dark-mode .table td,
        body.dark-mode hr {
            border-color: #4b5563 !important;
        }
        body.dark-mode .table {
            color: #e2e8f0;
            background-color: #111827;
        }
        body.dark-mode .table th,
        body.dark-mode .table td {
            border-color: #374151 !important;
        }
        body.dark-mode .table-light,
        body.dark-mode .table-light th,
        body.dark-mode .table-light td {
            background-color: #1f2937 !important;
            color: #d1d5db !important;
        }
        body.dark-mode .table-hover tbody tr:hover {
            background-color: rgba(148, 163, 184, 0.12);
        }
        body.dark-mode .text-body-secondary,
        body.dark-mode .small,
        body.dark-mode .form-text,
        body.dark-mode .text-muted,
        body.dark-mode .link-dark {
            color: #a5b4fc !important;
        }
        body.dark-mode a,
        body.dark-mode .nav-link,
        body.dark-mode .btn-link,
        body.dark-mode .dropdown-item,
        body.dark-mode .badge,
        body.dark-mode .text-dark {
            color: #e2e8f0 !important;
        }
        body.dark-mode .btn-outline-secondary {
            color: #e2e8f0;
            border-color: #475569;
        }
        body.dark-mode .btn-outline-secondary:hover,
        body.dark-mode .btn-outline-secondary:focus {
            background-color: rgba(148, 163, 184, 0.15);
            border-color: #cbd5e1;
        }
        body.dark-mode .btn-primary {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        body.dark-mode .btn-primary:hover,
        body.dark-mode .btn-primary:focus {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }
        body.dark-mode .form-control {
            background-color: #111827;
            color: #e9ecef;
            border-color: #475569;
        }
        body.dark-mode .form-control::placeholder {
            color: #94a3b8;
            opacity: 1;
        }
        .form-signin .form-control {
            min-height: 3.5rem;
            box-shadow: none;
        }
    </style>
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