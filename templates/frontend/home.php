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
?>

<main class="flex-grow-1 p-4 bg-light">
    <div class="container-fluid">
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border border-secondary border-opacity-15 cp-hover-float h-100">
                    <div class="card-body">
                        <h3 class="h6 text-uppercase text-muted mb-3">Usuarios activos</h3>
                        <div class="display-6 fw-bold mb-2"><?php echo h($usuariosActivos); ?></div>
                        <p class="mb-0 text-muted">De un total de <?php echo h($usuariosTotales); ?> cuentas registradas.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border border-secondary border-opacity-15 cp-hover-float h-100">
                    <div class="card-body">
                        <h3 class="h6 text-uppercase text-muted mb-3">Roles disponibles</h3>
                        <div class="display-6 fw-bold mb-2"><?php echo h($rolesTotales); ?></div>
                        <p class="mb-0 text-muted">Roles configurados para controlar accesos.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border border-secondary border-opacity-15 cp-hover-float h-100">
                    <div class="card-body">
                        <h3 class="h6 text-uppercase text-muted mb-3">Direcciones guardadas</h3>
                        <div class="display-6 fw-bold mb-2"><?php echo h($direccionesTotales); ?></div>
                        <p class="mb-0 text-muted">Propiedades y ubicaciones añadidas por el equipo.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card shadow-sm border border-secondary border-opacity-15">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                            <div>
                                <h2 class="h5 mb-2">Accesos rápidos</h2>
                                <p class="text-muted mb-0">Abre las secciones más usadas con un solo clic.</p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="usuarios.php" class="btn btn-primary btn-sm shadow-sm">
                                    <i class="bi bi-people-fill me-1"></i> Usuarios
                                </a>
                                <a href="direcciones.php" class="btn btn-outline-primary btn-sm shadow-sm">
                                    <i class="bi bi-geo-alt-fill me-1"></i> Direcciones
                                </a>
                                <a href="roles.php" class="btn btn-outline-success btn-sm shadow-sm">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Roles
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border border-secondary border-opacity-15 cp-hover-float h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h3 class="h6 mb-1">Nuevos usuarios</h3>
                                <p class="small text-muted mb-0">Últimos registros</p>
                            </div>
                            <span class="badge bg-primary">Top 5</span>
                        </div>
                        <div class="list-group list-group-flush">
                            <?php if (empty($usuariosRecientes)): ?>
                                <div class="list-group-item bg-transparent text-muted">No hay nuevos usuarios aún.</div>
                            <?php else: ?>
                                <?php foreach ($usuariosRecientes as $usuario): ?>
                                    <div class="list-group-item bg-transparent px-0 py-3 border-0">
                                        <div class="fw-semibold"><?php echo h($usuario->getEmail()); ?></div>
                                        <div class="small text-muted">
                                            <?php echo h($usuario->getRole() ?: 'empleado'); ?> ·
                                            <?php echo h($usuario->formatear_fecha($usuario->getFechaCreacion(), 'Y-m-d H:i:s', 'd/m/Y H:i') ?: $usuario->getFechaCreacion()); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border border-secondary border-opacity-15 cp-hover-float h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h3 class="h6 mb-1">Direcciones recientes</h3>
                                <p class="small text-muted mb-0">Propiedades añadidas</p>
                            </div>
                            <span class="badge bg-primary">Top 5</span>
                        </div>
                        <div class="list-group list-group-flush">
                            <?php if (empty($direccionesRecientes)): ?>
                                <div class="list-group-item bg-transparent text-muted">No hay direcciones nuevas aún.</div>
                            <?php else: ?>
                                <?php foreach ($direccionesRecientes as $direccion): ?>
                                    <div class="list-group-item bg-transparent px-0 py-3 border-0">
                                        <div class="fw-semibold"><?php echo h($direccion->getNombre()); ?></div>
                                        <div class="small text-muted">
                                            <?php echo h($direccion->obtenerDireccionCompleta()); ?> ·
                                            <?php echo h($direccion->formatear_fecha($direccion->getFechaCreacion(), 'Y-m-d H:i:s', 'd/m/Y H:i') ?: $direccion->getFechaCreacion()); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border border-secondary border-opacity-15 cp-hover-float h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h3 class="h6 mb-1">Roles recientes</h3>
                                <p class="small text-muted mb-0">Cambios en permisos</p>
                            </div>
                            <span class="badge bg-primary">Top 5</span>
                        </div>
                        <div class="list-group list-group-flush">
                            <?php if (empty($rolesRecientes)): ?>
                                <div class="list-group-item bg-transparent text-muted">No hay roles nuevos aún.</div>
                            <?php else: ?>
                                <?php foreach ($rolesRecientes as $rol): ?>
                                    <div class="list-group-item bg-transparent px-0 py-3 border-0">
                                        <div class="fw-semibold"><?php echo h($rol->getNombre()); ?></div>
                                        <div class="small text-muted">
                                            <?php echo h($rol->getDescripcion() ?: 'Sin descripción'); ?> ·
                                            <?php echo h($rol->formatear_fecha($rol->getFechaCreacion(), 'Y-m-d H:i:s', 'd/m/Y H:i') ?: $rol->getFechaCreacion()); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Cerramos con el pie de página -->
<?php require_once("footer.php"); ?>