<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestor de Direcciones - Panel de Control</title>
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
        .cp-users-card-body {
            background-color: #ffffff;
        }
        .cp-users-table {
            background-color: #ffffff;
            color: #212529;
        }
        .cp-users-table thead {
            background-color: #0d6efd;
            color: #ffffff;
        }
        .cp-users-table th,
        .cp-users-table td {
            border-color: #dee2e6;
        }
        .cp-users-table.table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }
        .cp-users-table.table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.08);
        }
        body.dark-mode .cp-users-card-body,
        body.dark-mode .cp-users-table {
            background-color: #111827 !important;
            color: #e2e8f0;
        }
        body.dark-mode .cp-users-table thead {
            background-color: #1f2937 !important;
            color: #d1d5db !important;
        }
        body.dark-mode .cp-users-table th,
        body.dark-mode .cp-users-table td {
            border-color: #374151 !important;
        }
        body.dark-mode .cp-users-table.table-striped tbody tr:nth-of-type(odd) {
            background-color: #111827 !important;
        }
        body.dark-mode .cp-users-table.table-striped tbody tr:nth-of-type(even) {
            background-color: #111827 !important;
        }
        body.dark-mode .cp-users-table.table-hover tbody tr:hover {
            background-color: rgba(148, 163, 184, 0.12) !important;
        }
        body.dark-mode .cp-users-table th,
        body.dark-mode .cp-users-table td,
        body.dark-mode .cp-users-table tbody,
        body.dark-mode .cp-users-table tbody tr {
            background-color: #111827 !important;
        }
        body.dark-mode .cp-users-table .table-secondary,
        body.dark-mode .cp-users-table .table-secondary td,
        body.dark-mode .cp-users-table .table-secondary th {
            background-color: #1f2937 !important;
            color: #d1d5db !important;
        }
        body.dark-mode .cp-users-table th,
        body.dark-mode .cp-users-table td,
        body.dark-mode .cp-users-table tbody tr,
        body.dark-mode .cp-users-table tbody tr td,
        body.dark-mode .cp-users-table tbody tr th {
            color: #f8fafc !important;
        }
        body.dark-mode .cp-users-table .form-select,
        body.dark-mode .cp-users-table .form-control,
        body.dark-mode .cp-users-table .btn,
        body.dark-mode .cp-users-table .badge,
        body.dark-mode .cp-users-table .text-muted,
        body.dark-mode .cp-users-table .form-check-label,
        body.dark-mode .cp-users-table .form-text,
        body.dark-mode .cp-users-table a {
            color: #f8fafc !important;
        }
        body.dark-mode .cp-users-table .form-select option {
            color: #f8fafc !important;
            background-color: #111827;
        }
        body.dark-mode .form-control::placeholder {
            color: #94a3b8;
            opacity: 1;
        }
        .cp-hover-float {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            transform: translateY(0);
        }
        .cp-hover-float:hover,
        .cp-hover-float:focus-within {
            transform: translateY(-8px);
            box-shadow: 0 0.9rem 1.6rem rgba(15, 23, 40, 0.18);
        }
        .form-signin .form-control {
            min-height: 3.5rem;
            box-shadow: none;
        }
        body {
            padding-top: 72px;
        }
        .navbar.fixed-top {
            position: fixed !important;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
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
<?php if (basename($_SERVER['SCRIPT_NAME']) === 'index.php'): ?>
<style>
body {
    background-image: url('<?php echo h($raizProyecto); ?>/imagen/fondoDir.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}
body.dark-mode {
    background-image: url('<?php echo h($raizProyecto); ?>/imagen/fondoNocheDir.png');
}
</style>
<?php endif; ?>
    <!-- BARRA DE NAVEGACIÓN SUPERIOR -->
    <nav class="navbar fixed-top bg-white border-bottom shadow-sm mb-0 py-2">
        <div class="container-fluid px-4 position-relative d-flex align-items-center">
            <!-- Logo principal que nos lleva al inicio -->
            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?php echo h($raizProyecto); ?>/index.php">
                <img src="<?php echo h($raizProyecto); ?>/imagen/logoAppDir.png" alt="Logo" class="me-2" style="height: 32px;">
                <span class="d-none d-sm-inline">Gestor de Direcciones</span>
            </a>
            
            <div class="position-absolute top-50 start-50 translate-middle">
                <button id="toggleDarkModeBtn" type="button" class="btn btn-sm btn-outline-secondary">
                    Modo noche
                </button>
            </div>

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