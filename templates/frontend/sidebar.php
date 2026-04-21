<?php
/**
 * VISTA: SIDEBAR
 * -------------
 * Muestra el menú lateral y los datos del usuario que ha iniciado sesión.
 */
require_once("../backend/sidebar_backend.php");
?>

<aside class="d-flex flex-column flex-shrink-0 p-3 bg-white border-end shadow-sm" style="width: 100%; max-width: 280px;">
    <!-- CABECERA DEL MENÚ: Info del usuario -->
    <div class="d-flex align-items-center mb-3">
        <?php if (!empty($avatarUrl)): ?>
            <img src="<?php echo h($avatarUrl); ?>" alt="Avatar" class="rounded-circle me-2 shadow-sm" style="width: 42px; height: 42px; object-fit: cover;">
        <?php else: ?>
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 42px; height: 42px; font-weight: bold;">
                <?php echo h(strtoupper(substr($correoUsuario, 0, 1))); ?>
            </div>
        <?php endif; ?>
        <div class="small overflow-hidden">
            <div class="fw-bold text-dark text-truncate" style="max-width: 180px;">
                <?php echo h(explode('@', $correoUsuario)[0]); ?>
            </div>
            <div class="text-muted smaller"><?php echo h($correoUsuario); ?></div>
            <div class="mt-1">
                <span class="badge bg-primary text-uppercase small"><?php echo h($rolUsuario); ?></span>
            </div>
        </div>
    </div>

    <hr class="mt-2 text-secondary opacity-25">

    <!-- LISTA DE ENLACES -->
    <ul class="nav nav-pills flex-column mb-auto">
        <?php foreach ($opcionesMenu as $item):
            $esActiva = ($paginaActual === $item['enlace']);
        ?>
            <li class="nav-item">
                <a href="<?php echo h($item['enlace']); ?>" class="nav-link mb-1 <?php echo $esActiva ? 'active shadow-sm' : 'link-dark'; ?>">
                    <i class="bi <?php echo h($item['icono']); ?> me-2"></i> <?php echo h($item['etiqueta']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <hr class="text-secondary opacity-25">

    <!-- MENÚ desplegable de Cuenta (Abajo del todo) -->
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle p-2 rounded" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle me-2"></i> Mi Cuenta
        </a>
        <ul class="dropdown-menu shadow rounded-3 border-0 p-2 mt-2">
            <li>
                <!-- Logout: Nos saca del sistema -->
                <a class="dropdown-item p-2 px-3 rounded-2 fw-semibold" href="<?php echo h($raizWeb); ?>/logout.php">
                    <i class="bi bi-door-open me-2 text-danger"></i>Cerrar sesión
                </a>
            </li>
        </ul>
    </div>

</aside>