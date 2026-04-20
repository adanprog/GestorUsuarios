<?php
/**
 * VISTA: ROLES
 * ------------
 * Página para crear, editar, eliminar roles y asignarlos a usuarios.
 */
require_once('../backend/procesar_roles.php'); // Lógica de los formularios de roles.
require_once __DIR__ . '/../../clases/CPRole.php';
require_once __DIR__ . '/../../clases/CPUser.php';
require_once('head.php');
require_once('sidebar.php'); // Menú lateral de navegación.

$currentUser = CPUser::buscarPorEmail($_SESSION['email'] ?? '');
$isAdminUser = $currentUser ? $currentUser->esAdministrador() : false;

$roles = CPRole::leerTodo();
$usuarios = CPUser::leerTodo();
$rolePriority = [
    'administrador' => 1,
    'gerente' => 2,
    'empleado' => 3,
    'standard' => 3,
];
usort($usuarios, function($a, $b) use ($rolePriority) {
    $roleA = mb_strtolower(trim($a->getRole() ?: 'empleado'));
    $roleB = mb_strtolower(trim($b->getRole() ?: 'empleado'));
    if ($roleA === 'standard') $roleA = 'empleado';
    if ($roleB === 'standard') $roleB = 'empleado';
    $prioA = $rolePriority[$roleA] ?? 99;
    $prioB = $rolePriority[$roleB] ?? 99;
    if ($prioA !== $prioB) {
        return $prioA <=> $prioB;
    }
    return strcasecmp($a->getEmail(), $b->getEmail());
});
$availableRoles = array_values(array_unique(array_filter(array_merge(
    ['administrador', 'empleado'],
    array_map(fn($r) => trim($r->getNombre()), $roles)
), fn($role) => $role !== '')));

// Contador para mostrar IDs consecutivos 1, 2, 3, 4 en la tabla.
$listaRolesNumerados = array_values($roles);
?>

<main class="flex-grow-1 p-4 bg-light">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Gestión de roles</h1>
            <p class="text-muted mb-0">Crea, edita y organiza los roles disponibles en el sistema.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="roles.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-clockwise me-1"></i>Actualizar</a>
        </div>
    </div>

    <?php cp_render_alert_message($message ?? null, $messageType ?? null); ?>

    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h2 class="h5 mb-0">Crear nuevo rol</h2>
                </div>
                <div class="card-body cp-users-card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre del rol</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                            <div class="form-text">Descripción opcional para el rol.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Permisos del rol</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="perm_view_direcciones" name="perm_view_direcciones" value="1">
                                <label class="form-check-label" for="perm_view_direcciones">Ver direcciones</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="perm_view_creator" name="perm_view_creator" value="1">
                                <label class="form-check-label" for="perm_view_creator">Ver qué usuario ha creado la dirección</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="perm_add_direcciones" name="perm_add_direcciones" value="1">
                                <label class="form-check-label" for="perm_add_direcciones">Crear dirección</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="perm_edit" name="perm_edit" value="1">
                                <label class="form-check-label" for="perm_edit">Editar</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="perm_delete" name="perm_delete" value="1">
                                <label class="form-check-label" for="perm_delete">Eliminar</label>
                            </div>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="active" name="active" checked>
                            <label class="form-check-label" for="active">Activo</label>
                        </div>
                        <button type="submit" name="add_role" class="btn btn-success w-100">
                            <i class="bi bi-plus-circle me-1"></i> Añadir rol
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h2 class="h5 mb-1">Listado de roles</h2>
                        <p class="mb-0 text-white-50">Aquí verás todos los roles guardados en el sistema.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label for="rolesSearch" class="text-white-50 mb-0 small">Buscar</label>
                        <input id="rolesSearch" type="text" class="form-control form-control-sm" placeholder="Nombre o descripción">
                    </div>
                </div>
                <div class="card-body p-0 cp-users-card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle cp-users-table">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Usuarios</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="rolesTableBody">
                                <?php if (empty($roles) && empty($userRoleTypes)): ?>
                                    <tr><td colspan="6" class="text-center py-4 text-muted">No hay roles creados todavía.</td></tr>
                                <?php elseif (empty($roles) && !empty($userRoleTypes)): ?>
                                    <?php foreach ($userRoleTypes as $roleName): ?>
                                        <?php
                                            $count = 0;
                                            foreach ($usuarios as $usuario) {
                                                $usuarioRole = mb_strtolower(trim($usuario->getRole() ?: 'empleado'));
                                                if ($usuarioRole === 'standard') {
                                                    $usuarioRole = 'empleado';
                                                }
                                                if ($usuario && $usuarioRole === mb_strtolower($roleName)) {
                                                    $count++;
                                                }
                                            }
                                        ?>
                                        <tr>
                                            <td>-</td>
                                            <td><?php echo h($roleName); ?></td>
                                            <td>Rol detectado en usuarios</td>
                                            <td><span class="badge bg-success">Activo</span></td>
                                            <td><span class="badge bg-info text-dark"><?php echo h($count); ?> usuarios</span></td>
                                            <td class="text-end">&mdash;</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php foreach ($listaRolesNumerados as $index => $rol): ?>
                                        <tr data-nombre="<?php echo h(mb_strtolower($rol->getNombre())); ?>" data-descripcion="<?php echo h(mb_strtolower($rol->getDescripcion())); ?>">
                                            <td><?php echo h($index + 1); ?></td>
                                            <td><?php echo h($rol->getNombre()); ?></td>
                                            <td><?php echo h($rol->getDescripcion()); ?></td>
                                            <td>
                                                <?php if ($rol->isActivo()): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                    $count = 0;
                                                    foreach ($usuarios as $usuario) {
                                                        $usuarioRole = mb_strtolower(trim($usuario->getRole() ?: 'empleado'));
                                                        if ($usuarioRole === 'standard') {
                                                            $usuarioRole = 'empleado';
                                                        }
                                                        if ($usuario && $usuarioRole === trim(mb_strtolower($rol->getNombre()))) {
                                                            $count++;
                                                        }
                                                    }
                                                ?>
                                                <span class="badge bg-info text-dark"><?php echo $count; ?> usuarios</span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2 align-items-center flex-wrap">
                                                    <button type="button" class="btn btn-sm btn-outline-primary edit-role-btn" data-bs-toggle="modal" data-bs-target="#editRoleModal" data-id="<?php echo h($rol->getId()); ?>" data-name="<?php echo h($rol->getNombre()); ?>" data-description="<?php echo h($rol->getDescripcion()); ?>" data-active="<?php echo $rol->isActivo() ? '1' : '0'; ?>" data-perm-view="<?php echo $rol->hasPermission('view_direcciones') ? '1' : '0'; ?>" data-perm-view-creator="<?php echo $rol->hasPermission('view_creator') ? '1' : '0'; ?>" data-perm-add="<?php echo $rol->hasPermission('add_direcciones') ? '1' : '0'; ?>" data-perm-edit="<?php echo $rol->hasPermission('edit_direcciones') ? '1' : '0'; ?>" data-perm-delete="<?php echo $rol->hasPermission('delete_direcciones') ? '1' : '0'; ?>">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </button>
                                                    <form method="post" class="m-0" onsubmit="return confirm('¿Eliminar este rol? Esta acción es irreversible y afectará a todos los usuarios con este rol.');">
                                                        <input type="hidden" name="role_id" value="<?php echo h($rol->getId()); ?>">
                                                        <button type="submit" name="delete_role" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                    <div>
                        <h2 class="h5 mb-1">Usuarios cargados</h2>
                        <p class="mb-0 text-white-50">Usuarios cargados desde la base de datos con su rol actual.</p>
                    </div>
                    <span class="badge bg-light text-dark"><?php echo count($usuarios); ?> usuarios</span>
                </div>
                <div class="card-body p-0 cp-users-card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle cp-users-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Email</th>
                                    <th>Rol actual</th>
                                    <th class="text-end">Asignar rol</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($usuarios)): ?>
                                    <tr><td colspan="3" class="text-center py-4 text-muted">No hay usuarios registrados.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td><?php echo h($usuario->getEmail()); ?></td>
                                            <td>
                                                <?php $displayRole = $usuario->getRole() ?: 'empleado'; if ($displayRole === 'standard') { $displayRole = 'empleado'; } ?>
                                                <span class="badge bg-secondary text-uppercase"><?php echo h($displayRole); ?></span>
                                            </td>
                                            <td class="text-end">
                                                <?php if ($isAdminUser): ?>
                                                    <form method="post" class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
                                                        <input type="hidden" name="user_id" value="<?php echo h($usuario->getId()); ?>">
                                                        <select name="role_name" class="form-select form-select-sm w-auto">
                                                            <?php
                                                                $currentRole = mb_strtolower($usuario->getRole() ?: 'empleado');
                                                                if ($currentRole === 'standard' || $currentRole === 'empleado'): ?>
                                                                <option value="empleado" selected hidden>Empleado</option>
                                                            <?php endif; ?>
                                                            <?php foreach ($availableRoles as $optionRole): ?>
                                                                <option value="<?php echo h($optionRole); ?>" <?php echo $optionRole === $usuario->getRole() ? 'selected' : ''; ?>><?php echo h($optionRole); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <button type="submit" name="assign_user_role" class="btn btn-sm btn-outline-primary">Guardar</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-muted">Sin permiso</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoleModalLabel">Editar rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" id="edit_role_id" name="role_id">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nombre</label>
                            <input type="text" id="edit_name" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Descripción</label>
                            <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Permisos del rol</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_perm_view_direcciones" name="perm_view_direcciones" value="1">
                                <label class="form-check-label" for="edit_perm_view_direcciones">Ver direcciones</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_perm_view_creator" name="perm_view_creator" value="1">
                                <label class="form-check-label" for="edit_perm_view_creator">Ver qué usuario ha creado la dirección</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_perm_add_direcciones" name="perm_add_direcciones" value="1">
                                <label class="form-check-label" for="edit_perm_add_direcciones">Crear dirección</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_perm_edit" name="perm_edit" value="1">
                                <label class="form-check-label" for="edit_perm_edit">Editar</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_perm_delete" name="perm_delete" value="1">
                                <label class="form-check-label" for="edit_perm_delete">Eliminar</label>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_active" name="active">
                            <label class="form-check-label" for="edit_active">Activo</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="edit_role" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require_once('footer.php'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('rolesSearch');
        const tableBody = document.getElementById('rolesTableBody');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                const rows = tableBody.querySelectorAll('tr');

                rows.forEach(row => {
                    const nombre = row.getAttribute('data-nombre') || '';
                    const descripcion = row.getAttribute('data-descripcion') || '';
                    const visible = nombre.includes(query) || descripcion.includes(query);
                    row.style.display = visible ? '' : 'none';
                });
            });
        }

        const editButtons = document.querySelectorAll('.edit-role-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const roleId = this.dataset.id;
                const roleName = this.dataset.name;
                const roleDescription = this.dataset.description;
                const roleActive = this.dataset.active === '1';
                const permView = this.dataset.permView === '1';
                const permViewCreator = this.dataset.permViewCreator === '1';
                const permAdd = this.dataset.permAdd === '1';
                const permEdit = this.dataset.permEdit === '1';
                const permDelete = this.dataset.permDelete === '1';

                document.getElementById('edit_role_id').value = roleId;
                document.getElementById('edit_name').value = roleName;
                document.getElementById('edit_description').value = roleDescription;
                document.getElementById('edit_active').checked = roleActive;
                document.getElementById('edit_perm_view_direcciones').checked = permView;
                document.getElementById('edit_perm_view_creator').checked = permViewCreator;
                document.getElementById('edit_perm_add_direcciones').checked = permAdd;
                document.getElementById('edit_perm_edit').checked = permEdit;
                document.getElementById('edit_perm_delete').checked = permDelete;
            });
        });
    });
</script>
