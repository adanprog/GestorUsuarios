
<?php
/**
 * VISTA: EDITAR USUARIO
 * ---------------------
 * Muestra el formulario para modificar los datos de un usuario.
 */
require_once('../backend/edituser_back.php'); // Lógica y datos del usuario a editar
require_once('head.php');    // Cabecera común con estilos y scripts.
require_once('sidebar.php'); // Menú lateral de navegación.

$currentUser = CPUser::buscarPorEmail($_SESSION['email'] ?? '');
$isAdminUser = $currentUser ? $currentUser->esAdministrador() : false;
?>

<main class="flex-grow-1 p-4 bg-light">
    <h2 class="h4 fw-bold mb-4">Editar Usuario:</h2>

    <!-- Mensajes de estado globales -->
    <?php cp_render_alert_message($message ?? null, $messageType ?? null); ?>

    <!-- Formulario para editar el usuario -->
    <form method="post" action="">
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="name" name="username" value="<?php echo htmlspecialchars($usuario->getUsername()); ?>" required>
        </div>
        <div class="mb-3">
            <label for="lastname" class="form-label">Apellidos</label>
            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($usuario->getApellidos()); ?>" required>
        </div>
        <div class="mb-3">
            <label for="DNI" class="form-label">DNI</label>
            <input type="text" class="form-control" id="DNI" name="DNI" value="<?php echo htmlspecialchars($usuario->getDNI()); ?>" required>
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario->getTelefono()); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario->getEmail()); ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Nueva Contraseña </label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Rol</label>
            <?php if ($isAdminUser): ?>
                <select class="form-control" id="role" name="role" required>
                    <option value="empleado" <?php echo $usuario->getRole() === 'empleado' || $usuario->getRole() === 'standard' ? 'selected' : ''; ?>>Empleado</option>
                    <option value="administrador" <?php echo $usuario->getRole() === 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                </select>
            <?php else: ?>
                <input type="hidden" name="role" value="<?php echo htmlspecialchars($usuario->getRole()); ?>">
                <div class="form-control-plaintext"><?php echo htmlspecialchars(ucfirst($usuario->getRole())); ?></div>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento</label>
            <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" value="<?php echo htmlspecialchars($usuario->getFechaNacimiento()); ?>">
        </div>
        <input type="hidden" name="original_email" value="<?php echo htmlspecialchars($usuario->getEmail()); ?>">
        <button type="submit" name="edit_user" class="btn btn-success shadow-sm">
            <i class="bi bi-check-lg me-1"></i> Guardar Cambios
        </button>

<?php require_once('../frontend/footer.php');    // Pie de página común?>