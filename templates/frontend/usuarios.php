<?php
/**
 * VISTA: GESTIÓN DE USUARIOS
 * -------------------------
 * Archivo que muestra la lista de usuarios y permite crear, editar,
 * activar/desactivar o borrar cuentas según los permisos.
 */
require_once('../backend/procesar_usuarios.php'); // Lógica de botones (guardar/borrar)
require_once __DIR__ . '/../../clases/CPRole.php';
require_once('head.php');    // Cabecera común con estilos y scripts.
require_once('sidebar.php'); // Menú lateral de navegación.

$currentUser = CPUser::buscarPorEmail($_SESSION['email'] ?? '');
$isAdminUser = $currentUser ? $currentUser->esAdministrador() : false;
?>

<main class="flex-grow-1 p-4 bg-light">
    <!-- Título y botón para abrir la ventana de añadir -->
    <h2 class="h4 fw-bold mb-0">Gestión de Usuarios con Acceso</h2>
    <div class="mb-4 text-end">
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-plus-lg me-1"></i>Nuevo Usuario
        </button>
    </div>

    <!-- Mensajes de estado globales -->
    <?php cp_render_alert_message($message ?? null, $messageType ?? null); ?>

    <!-- TABLA donde se listan los usuarios reales -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0 cp-users-card-body">
            <table class="table table-hover mb-0 cp-users-table">
                <thead class="table-light text-uppercase smaller fw-bold">
                    <tr>
                        <th class="ps-3">Correo Electrónico</th>
                        <th>Rol / Permiso</th>
                        <th>Estado</th>
                        <th class="text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Usamos nuestra clase CPUser para leer los usuarios desde la base de datos
                    $listaUsuarios = CPUser::leerTodo();
                    $roles = CPRole::leerTodo();
                    $roleOptions = array_unique(array_merge(
                        ['empleado', 'administrador'],
                        array_map(fn($r) => $r->getNombre(), $roles)
                    ));
                    foreach ($listaUsuarios as $u):
                        if ($u === null) continue;
                        $email = h($u->getEmail());
                        $rol = h($u->getRole() ?: 'empleado');
                        $activo = $u->isActivo();
                        $estadoBadge = $activo ? 'bg-success' : 'bg-danger';
                        $estadoTexto = $activo ? 'Activo' : 'Inactivo';
                    ?>
                        <tr class="align-middle <?php echo $activo ? '' : 'table-secondary text-muted'; ?>">
                            <td class="ps-3">
                                <a href="edituser.php?id=<?php echo urlencode($u->getId()); ?>" class="link-dark text-decoration-none">
                                    <?php echo $email; ?>
                                </a>
                            </td>
                            <td><span class="badge bg-secondary"><?php echo $rol; ?></span></td>
                            <td><span class="badge <?php echo $estadoBadge; ?>"><?php echo $estadoTexto; ?></span></td>
                            <td class="text-end pe-3">
                                <div class="d-flex justify-content-end gap-2 flex-wrap align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-warning edit-user-btn" 
                                            data-email="<?php echo $email; ?>" 
                                            data-role="<?php echo $rol; ?>"
                                            data-active="<?php echo $activo ? '1' : '0'; ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="post" class="m-0">
                                        <input type="hidden" name="email" value="<?php echo $email; ?>">
                                        <input type="hidden" name="active" value="<?php echo $activo ? '0' : '1'; ?>">
                                        <button type="submit" name="toggle_user_active" class="btn btn-sm btn-outline-<?php echo $activo ? 'danger' : 'success'; ?> shadow-sm" title="<?php echo $activo ? 'Desactivar usuario' : 'Activar usuario'; ?>">
                                            <i class="bi <?php echo $activo ? 'bi-person-x' : 'bi-person-check'; ?>"></i>
                                        </button>
                                    </form>
                                    <form method="post" class="m-0" onsubmit="return confirm('¿Eliminar este usuario? Esta acción quitará el acceso al sistema y no se puede deshacer.');">
                                        <input type="hidden" name="email" value="<?php echo $email; ?>">
                                        <button type="submit" name="delete_user" class="btn btn-sm btn-outline-danger shadow-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- VENTANA FLOTANTE (MODAL): PARA AÑADIR NUEVO -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header"><h5>Añadir Nuevo Usuario</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label>Correo</label><input type="email" name="email" class="form-control" placeholder="ejemplo@email.com" required></div>
                    <div class="mb-3">
                        <label>Contraseña</label>
                        <div class="input-group">
                            <input type="password" name="password" id="add_password" class="form-control" required>
                            <button class="btn btn-outline-secondary toggle-password-btn" type="button" data-target="add_password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Confirmar Contraseña</label>
                        <div class="input-group">
                            <input type="password" name="password_confirm" id="add_password_confirm" class="form-control" required>
                            <button class="btn btn-outline-secondary toggle-password-btn" type="button" data-target="add_password_confirm">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="text-danger d-none" id="add_password_error">Las contraseñas no coinciden</small>
                    </div>
                    <div class="mb-3">
                        <label>Rol</label>
                        <?php if ($isAdminUser): ?>
                            <select name="role" class="form-select">
                                <?php foreach ($roleOptions as $optionRole): ?>
                                    <option value="<?php echo h($optionRole); ?>"><?php echo h(ucfirst($optionRole)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="hidden" name="role" value="empleado">
                            <div class="form-control-plaintext">Empleado</div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" name="add_user" class="btn btn-primary">Registrar Usuario</button></div>
            </form>
        </div>
    </div>
</div>

<!-- VENTANA FLOTANTE (MODAL): PARA EDITAR EXISTENTE -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold">Modificar Datos de Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Este campo oculto guarda el email viejo para saber a quién editar -->
                <input type="hidden" name="original_email" id="edit_original_email">
                <div class="mb-3">
                    <label class="form-label">Correo Nuevo</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña Nueva</label>
                    <div class="input-group">
                        <input type="password" name="password" id="edit_password" class="form-control" placeholder="Escribe para cambiar...">
                        <button class="btn btn-outline-secondary toggle-password-btn" type="button" data-target="edit_password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmar Contraseña</label>
                    <div class="input-group">
                        <input type="password" name="password_confirm" id="edit_password_confirm" class="form-control" placeholder="Repite la contraseña...">
                        <button class="btn btn-outline-secondary toggle-password-btn" type="button" data-target="edit_password_confirm">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <small class="text-danger d-none" id="password_error">Las contraseñas no coinciden</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nuevo Rol</label>
                    <?php if ($isAdminUser): ?>
                        <select name="role" id="edit_role" class="form-select">
                            <?php foreach ($roleOptions as $optionRole): ?>
                                <option value="<?php echo h($optionRole); ?>"><?php echo h(ucfirst($optionRole)); ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="hidden" name="role" id="edit_role" value="empleado">
                        <div class="form-control-plaintext" id="edit_role_display">Empleado</div>
                    <?php endif; ?>
                </div>
                <div class="form-check mb-3">
                    <input type="hidden" name="active" value="0">
                    <input class="form-check-input" type="checkbox" id="edit_active" name="active" value="1">
                    <label class="form-check-label" for="edit_active">Cuenta activa</label>
                </div>
            </div>
            <div class="modal-footer pb-3 pt-0 border-0">
                <button type="submit" name="edit_user" class="btn btn-warning px-4 shadow-sm fw-bold">Actualizar Datos</button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPT para rellenar el modal de edición al hacer clic -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    var botonesEditar = document.querySelectorAll('.edit-user-btn');
    var modalEditar = new bootstrap.Modal(document.getElementById('editUserModal'));

    botonesEditar.forEach(function (btn) {
        btn.addEventListener('click', function () {
            // Pasamos los datos del botón (dataset) a los inputs del modal
            document.getElementById('edit_original_email').value = this.dataset.email;
            document.getElementById('edit_email').value = this.dataset.email;
            document.getElementById('edit_password').value = '';
            document.getElementById('edit_password_confirm').value = '';
            document.getElementById('edit_role').value = this.dataset.role;
            document.getElementById('edit_active').checked = this.dataset.active === '1';
            document.getElementById('password_error').classList.add('d-none');
            modalEditar.show();
        });
    });

    // Toggle mostrar/ocultar contraseña
    document.querySelectorAll('.toggle-password-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    });

    // Validar que las contraseñas coincidan en modal de edición
    const passwordInput = document.getElementById('edit_password');
    const passwordConfirm = document.getElementById('edit_password_confirm');
    const passwordError = document.getElementById('password_error');
    const editUserForm = document.querySelector('#editUserModal form');

    function validateEditPasswords() {
        if (passwordInput.value || passwordConfirm.value) {
            if (passwordInput.value !== passwordConfirm.value) {
                passwordError.classList.remove('d-none');
                return false;
            } else {
                passwordError.classList.add('d-none');
                return true;
            }
        }
        passwordError.classList.add('d-none');
        return true;
    }

    if (passwordInput && passwordConfirm) {
        [passwordInput, passwordConfirm].forEach(input => {
            input.addEventListener('input', validateEditPasswords);
        });

        if (editUserForm) {
            editUserForm.addEventListener('submit', function(e) {
                if (!validateEditPasswords()) {
                    e.preventDefault();
                }
            });
        }
    }

    // Validar que las contraseñas coincidan en modal de añadir
    const addPasswordInput = document.getElementById('add_password');
    const addPasswordConfirm = document.getElementById('add_password_confirm');
    const addPasswordError = document.getElementById('add_password_error');
    const addUserForm = document.querySelector('#addUserModal form');

    function validateAddPasswords() {
        if (addPasswordInput.value !== addPasswordConfirm.value) {
            addPasswordError.classList.remove('d-none');
            return false;
        } else {
            addPasswordError.classList.add('d-none');
            return true;
        }
    }

    if (addPasswordInput && addPasswordConfirm) {
        [addPasswordInput, addPasswordConfirm].forEach(input => {
            input.addEventListener('input', validateAddPasswords);
        });

        if (addUserForm) {
            addUserForm.addEventListener('submit', function(e) {
                if (!validateAddPasswords()) {
                    e.preventDefault();
                }
            });
        }
    }
});
</script>

<?php require_once('footer.php'); ?>