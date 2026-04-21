<?php
/**
 * VISTA: PERFIL DE USUARIO
 * ------------------------
 * Permite a cada usuario actualizar sus datos personales y su avatar.
 */
require_once('../backend/profile_back.php');
require_once('head.php');
require_once('sidebar.php');

$rutaActual = dirname($_SERVER['SCRIPT_NAME']);
$raizWeb = str_replace(['/templates/frontend', '/templates/backend', '/templates'], '', $rutaActual);
$raizWeb = rtrim($raizWeb, '/\\');
$avatarUrl = $usuario->getAvatar() ? ($raizWeb ? $raizWeb . '/' : '/') . ltrim($usuario->getAvatar(), '/') : '';
?>

<main class="flex-grow-1 p-4 bg-light">
    <h2 class="h4 fw-bold mb-3">Mi Perfil</h2>
    <p class="text-muted mb-4">Actualiza tu información personal y sube una foto de perfil.</p>

    <?php cp_render_alert_message($message ?? null, $messageType ?? null); ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border border-secondary border-opacity-15">
                <div class="card-body text-center">
                    <?php if (!empty($avatarUrl)): ?>
                        <img src="<?php echo h($avatarUrl); ?>" alt="Avatar" class="rounded-circle mb-3" style="width: 140px; height: 140px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 140px; height: 140px; font-size: 2.25rem;">
                            <?php echo h(strtoupper(substr($usuario->getUsername(), 0, 1))); ?>
                        </div>
                    <?php endif; ?>

                    <h5 class="fw-bold mb-1"><?php echo h($usuario->getUsername()); ?></h5>
                    <p class="text-muted mb-3"><?php echo h($usuario->getEmail()); ?></p>
                    <p class="mb-0"><span class="badge bg-secondary text-uppercase"><?php echo h($usuario->getRoleNormalized()); ?></span></p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border border-secondary border-opacity-15">
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Nombre</label>
                                <input type="text" id="username" name="username" class="form-control" required value="<?php echo h($usuario->getUsername()); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="lastname" class="form-label">Apellidos</label>
                                <input type="text" id="lastname" name="lastname" class="form-control" required value="<?php echo h($usuario->getApellidos()); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="DNI" class="form-label">DNI</label>
                                <input type="text" id="DNI" name="DNI" class="form-control" value="<?php echo h($usuario->getDNI()); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" id="telefono" name="telefono" class="form-control" value="<?php echo h($usuario->getTelefono()); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" id="email" name="email" class="form-control" required value="<?php echo h($usuario->getEmail()); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="fechaNacimiento" class="form-label">Fecha de nacimiento</label>
                                <input type="date" id="fechaNacimiento" name="fechaNacimiento" class="form-control" value="<?php echo h($usuario->getFechaNacimiento()); ?>">
                            </div>
                            <div class="col-md-12">
                                <label for="avatar" class="form-label">Avatar / Foto de perfil</label>
                                <input type="file" id="avatar" name="avatar" accept="image/*" class="form-control">
                                <div class="form-text">JPG, PNG, GIF o WEBP. Máx. 2MB recomendado.</div>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" name="save_profile" class="btn btn-primary shadow-sm">
                                <i class="bi bi-save me-1"></i> Guardar perfil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once('footer.php'); ?>
