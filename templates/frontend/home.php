<?php
/**
 * INICIO (HOME)
 * ------------
 * Esta es la página de bienvenida que se ve justo después de entrar.
 */
error_reporting(E_ALL ^ E_NOTICE); // Evita que salgan avisos molestos
session_start();

require_once __DIR__ . '/../../clases/CPUser.php';
require_once __DIR__ . '/../../clases/CPRole.php';
require_once __DIR__ . '/../../clases/CPDir.php';

// Cargamos la cabecera (incluye estilos y seguridad) y el menú lateral
require_once("head.php");
require_once("sidebar.php");

$usuarios = array_filter(CPUser::leerTodo(), fn($usuario) => $usuario !== null);
$roles = array_filter(CPRole::leerTodo(), fn($rol) => $rol !== null);
$direcciones = array_filter(CPDir::leerTodo(), fn($direccion) => $direccion !== null);

$usuariosActivos = count(array_filter($usuarios, fn($usuario) => $usuario->isActivo()));
$usuariosTotales = count($usuarios);
$rolesTotales = count($roles);
$direccionesTotales = count($direcciones);

$ordenarPorFechaCreacionDesc = fn($a, $b) => strtotime($b->getFechaCreacion() ?: '1970-01-01') <=> strtotime($a->getFechaCreacion() ?: '1970-01-01');

$usuariosRecientes = $usuarios;
usort($usuariosRecientes, $ordenarPorFechaCreacionDesc);
$usuariosRecientes = array_slice($usuariosRecientes, 0, 5);

$direccionesRecientes = $direcciones;
usort($direccionesRecientes, $ordenarPorFechaCreacionDesc);
$direccionesRecientes = array_slice($direccionesRecientes, 0, 5);

$rolesRecientes = $roles;
usort($rolesRecientes, $ordenarPorFechaCreacionDesc);
$rolesRecientes = array_slice($rolesRecientes, 0, 5);

// Verificar si el usuario actual es administrador
$currentUser = CPUser::buscarPorEmail($_SESSION['email'] ?? '');
$isAdminUser = $currentUser ? $currentUser->esAdministrador() : false;

// Datos personalizados para usuarios no-admin
if (!$isAdminUser && $currentUser) {
    // Obtener direcciones del usuario actual
    $misDirectiones = array_filter($direcciones, fn($dir) => $dir->getEmail() === $currentUser->getEmail());
    usort($misDirectiones, fn($a, $b) => strtotime($b->getFechaCreacion() ?: '1970-01-01') <=> strtotime($a->getFechaCreacion() ?: '1970-01-01'));
    $misDirectionesRecientes = array_slice($misDirectiones, 0, 5);
    $totalMisDirecciones = count($misDirectiones);
    
    // Información del usuario
    $nombreUsuario = $currentUser->getNombre() ?: 'Usuario';
    $emailUsuario = $currentUser->getEmail();
    $rolUsuario = $currentUser->getRole() ?: 'empleado';
    $fechaRegistro = $currentUser->getFechaCreacion();
    $esActivo = $currentUser->isActivo();
}
?>

<main class="flex-grow-1 p-4 bg-light">
    <div class="container-fluid">
        <?php if ($isAdminUser): ?>
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border cp-hover-float h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-color: rgba(102, 126, 234, 0.3) !important;">
                    <div class="card-body text-white">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h3 class="h6 text-uppercase mb-0 opacity-75">Usuarios Activos</h3>
                            <i class="bi bi-people-fill" style="font-size: 1.5rem; opacity: 0.7;"></i>
                        </div>
                        <div class="display-5 fw-bold mb-3"><?php echo h($usuariosActivos); ?></div>
                        <p class="mb-0 opacity-75">De un total de <strong><?php echo h($usuariosTotales); ?></strong> cuentas registradas.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border cp-hover-float h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-color: rgba(245, 87, 108, 0.3) !important;">
                    <div class="card-body text-white">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h3 class="h6 text-uppercase mb-0 opacity-75">Roles Disponibles</h3>
                            <i class="bi bi-shield-lock-fill" style="font-size: 1.5rem; opacity: 0.7;"></i>
                        </div>
                        <div class="display-5 fw-bold mb-3"><?php echo h($rolesTotales); ?></div>
                        <p class="mb-0 opacity-75">Roles configurados para <strong>controlar accesos</strong>.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border cp-hover-float h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-color: rgba(79, 172, 254, 0.3) !important;">
                    <div class="card-body text-white">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h3 class="h6 text-uppercase mb-0 opacity-75">Direcciones Guardadas</h3>
                            <i class="bi bi-geo-alt-fill" style="font-size: 1.5rem; opacity: 0.7;"></i>
                        </div>
                        <div class="display-5 fw-bold mb-3"><?php echo h($direccionesTotales); ?></div>
                        <p class="mb-0 opacity-75">Propiedades y ubicaciones del equipo.</p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($isAdminUser): ?>
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card shadow-sm border bg-white">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-4">
                            <div>
                                <h2 class=\"h5 fw-bold mb-2\">Accesos Rápidos</h2>
                                <p class="text-muted mb-0">Abre las secciones más usadas con un solo clic.</p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="usuarios.php" class="btn btn-primary shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                    <i class="bi bi-people-fill me-2"></i>Usuarios
                                </a>
                                <a href="direcciones.php" class="btn shadow-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border: none; color: white;">
                                    <i class="bi bi-geo-alt-fill me-2"></i>Direcciones
                                </a>
                                <a href="roles.php" class="btn shadow-sm" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border: none; color: white;">
                                    <i class="bi bi-shield-lock-fill me-2"></i>Roles
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($isAdminUser): ?>
        <div class="row g-4">
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border cp-hover-float h-100">
                    <div class="card-header bg-transparent border-bottom pt-4 pb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h3 class="h6 fw-bold mb-1"><i class="bi bi-person-plus text-primary me-2"></i>Nuevos Usuarios</h3>
                                <p class="small text-muted mb-0">Últimos registros del sistema</p>
                            </div>
                            <span class="badge bg-primary rounded-pill">Top 5</span>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="list-group list-group-flush">
                            <?php if (empty($usuariosRecientes)): ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                    <p class="mt-2 mb-0">No hay nuevos usuarios aún.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($usuariosRecientes as $usuario): ?>
                                    <div class="list-group-item bg-transparent px-0 py-3 border-bottom">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="bi bi-person-circle text-primary"></i>
                                            <div class="fw-semibold text-dark"><?php echo h($usuario->getEmail()); ?></div>
                                        </div>
                                        <div class="small text-muted ps-4">
                                            <span class="badge bg-light text-dark"><?php echo h($usuario->getRole() ?: 'empleado'); ?></span>
                                            <span class="ms-2">📅 <?php echo h($usuario->formatear_fecha($usuario->getFechaCreacion(), 'Y-m-d H:i:s', 'd/m/Y') ?: $usuario->getFechaCreacion()); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border cp-hover-float h-100">
                    <div class="card-header bg-transparent border-bottom pt-4 pb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h3 class="h6 fw-bold mb-1"><i class="bi bi-geo-alt text-info me-2"></i>Direcciones Recientes</h3>
                                <p class="small text-muted mb-0">Propiedades añadidas recientemente</p>
                            </div>
                            <span class="badge bg-info rounded-pill">Top 5</span>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="list-group list-group-flush">
                            <?php if (empty($direccionesRecientes)): ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                    <p class="mt-2 mb-0">No hay direcciones nuevas aún.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($direccionesRecientes as $direccion): ?>
                                    <div class="list-group-item bg-transparent px-0 py-3 border-bottom">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="bi bi-pin-map text-info"></i>
                                            <div class="fw-semibold text-dark"><?php echo h($direccion->getNombre()); ?></div>
                                        </div>
                                        <div class="small text-muted ps-4">
                                            <span class="d-block text-truncate mb-1">📍 <?php echo h($direccion->obtenerDireccionCompleta()); ?></span>
                                            <span>📅 <?php echo h($direccion->formatear_fecha($direccion->getFechaCreacion(), 'Y-m-d H:i:s', 'd/m/Y') ?: $direccion->getFechaCreacion()); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border cp-hover-float h-100">
                    <div class="card-header bg-transparent border-bottom pt-4 pb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h3 class="h6 fw-bold mb-1"><i class="bi bi-shield-lock text-success me-2"></i>Roles Recientes</h3>
                                <p class="small text-muted mb-0">Cambios en permisos y configuración</p>
                            </div>
                            <span class="badge bg-success rounded-pill">Top 5</span>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="list-group list-group-flush">
                            <?php if (empty($rolesRecientes)): ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                    <p class="mt-2 mb-0">No hay roles nuevos aún.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($rolesRecientes as $rol): ?>
                                    <div class="list-group-item bg-transparent px-0 py-3 border-bottom">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="bi bi-shield-check text-success"></i>
                                            <div class="fw-semibold text-dark"><?php echo h($rol->getNombre()); ?></div>
                                        </div>
                                        <div class="small text-muted ps-4">
                                            <span class="d-block text-truncate mb-1">ℹ️ <?php echo h($rol->getDescripcion() ?: 'Sin descripción'); ?></span>
                                            <span>📅 <?php echo h($rol->formatear_fecha($rol->getFechaCreacion(), 'Y-m-d H:i:s', 'd/m/Y') ?: $rol->getFechaCreacion()); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- SECCIÓN PARA USUARIOS NO-ADMIN -->
        <?php if (!$isAdminUser && $currentUser): ?>
        <!-- Bienvenida Personalizada -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card shadow-sm border" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-color: rgba(102, 126, 234, 0.3) !important;">
                    <div class="card-body text-white">
                        <h2 class="h4 fw-bold mb-2">Bienvenido, <?php echo h($nombreUsuario); ?></h2>
                        <p class="mb-0 opacity-85">Tu panel de control personalizado • Rol: <strong><?php echo ucfirst(h($rolUsuario)); ?></strong></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Personales -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border cp-hover-float h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-color: rgba(79, 172, 254, 0.3) !important;">
                    <div class="card-body text-white">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h3 class="h6 text-uppercase mb-0 opacity-75">Mis Direcciones</h3>
                            <i class="bi bi-geo-alt-fill" style="font-size: 1.5rem; opacity: 0.7;"></i>
                        </div>
                        <div class="display-5 fw-bold mb-3"><?php echo h($totalMisDirecciones); ?></div>
                        <p class="mb-0 opacity-75">Propiedades y ubicaciones guardadas.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border cp-hover-float h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-color: rgba(245, 87, 108, 0.3) !important;">
                    <div class="card-body text-white">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h3 class="h6 text-uppercase mb-0 opacity-75">Mi Rol</h3>
                            <i class="bi bi-shield-lock-fill" style="font-size: 1.5rem; opacity: 0.7;"></i>
                        </div>
                        <div class="display-5 fw-bold mb-3" style="font-size: 2rem;"><?php echo ucfirst(h($rolUsuario)); ?></div>
                        <p class="mb-0 opacity-75">Permisos asignados en el sistema.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos Personalizados -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card shadow-sm border bg-white">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-4">
                            <div>
                                <h2 class="h5 fw-bold mb-2">Accesos Rápidos</h2>
                                <p class="text-muted mb-0">Accede rápidamente a tus secciones principales.</p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="direcciones.php" class="btn shadow-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border: none; color: white;">
                                    <i class="bi bi-geo-alt-fill me-2"></i>Mis Direcciones
                                </a>
                                <a href="profile.php" class="btn shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white;">
                                    <i class="bi bi-person-circle me-2"></i>Mi Perfil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mis Direcciones Recientes -->
        <div class="row g-4">
            <div class="col-12">
                <div class="card shadow-sm border">
                    <div class="card-header bg-transparent border-bottom pt-4 pb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h3 class="h6 fw-bold mb-1"><i class="bi bi-pin-map text-info me-2"></i>Mis Direcciones</h3>
                                <p class="small text-muted mb-0">Las últimas propiedades que agregaste</p>
                            </div>
                            <a href="direcciones.php" class="btn btn-sm btn-outline-primary">Ver todas</a>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="list-group list-group-flush">
                            <?php if (empty($misDirectionesRecientes)): ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                    <p class="mt-2 mb-0">No tienes direcciones guardadas aún.</p>
                                    <a href="direcciones.php" class="btn btn-sm btn-primary mt-3">Crear mi primera dirección</a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($misDirectionesRecientes as $direccion): ?>
                                    <div class="list-group-item bg-transparent px-0 py-3 border-bottom">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="bi bi-pin-map text-info"></i>
                                            <div class="fw-semibold text-dark"><?php echo h($direccion->getNombre()); ?></div>
                                        </div>
                                        <div class="small text-muted ps-4">
                                            <span class="d-block text-truncate mb-1">📍 <?php echo h($direccion->obtenerDireccionCompleta()); ?></span>
                                            <span>📅 <?php echo h($currentUser->formatear_fecha($direccion->getFechaCreacion(), 'Y-m-d H:i:s', 'd/m/Y') ?: $direccion->getFechaCreacion()); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- Cerramos con el pie de página -->
<?php require_once("footer.php"); ?>