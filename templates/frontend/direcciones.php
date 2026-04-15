<?php
/**
 * VISTA: DIRECCIONES
 * ------------------
 * Muestra las direcciones almacenadas, permite crear nuevas y
 * editar o borrar las existentes según los permisos del usuario.
 */
require_once('../backend/procesar_direcciones.php'); // Lógica para guardar, editar o borrar direcciones.
require_once __DIR__ . '/../../clases/CPUser.php';

require_once('head.php');
require_once('sidebar.php');

//Carga códigos postales desde caché JSON en lugar de XLSX
$dirArchivoCodigos = __DIR__ . '/../../archivos/codigos_postales_municipios.xlsx';
$fileJsonCache = __DIR__ . '/../../archivos/codigos_postales_cache.json';

// Si el caché no existe o el XLSX es más nuevo, regenerar el caché
if (!file_exists($fileJsonCache) || filemtime($dirArchivoCodigos) > filemtime($fileJsonCache)) {
    require_once '../clases/CPDesplegableDir.php';
    $codigoPostal = new CPDesplegableDir($dirArchivoCodigos, 'A');
    $mapaCodigos = $codigoPostal->obtenerMapaCpCiudadProvincia('A', 'C', 'B');
    // Guardar en caché
    file_put_contents($fileJsonCache, json_encode($mapaCodigos));
} else {
    // Cargar desde caché
    $mapaCodigos = json_decode(file_get_contents($fileJsonCache), true);
}

// Crear lista de códigos postales ordenados, sin duplicados
$nombrescodigosPostales = array_keys($mapaCodigos);
sort($nombrescodigosPostales, SORT_STRING);

$currentUser = CPUser::buscarPorEmail($_SESSION['email']);
$canAddDirecciones = $currentUser ? $currentUser->canAddDirecciones() : false;
$canEditDirecciones = $currentUser ? $currentUser->canEditDirecciones() : false;
$canDeleteDirecciones = $currentUser ? $currentUser->canDeleteDirecciones() : false;
$canViewAllDirecciones = $currentUser ? $currentUser->canViewAllDirecciones() : false;
$canViewCreator = $currentUser ? $currentUser->canViewCreator() : false;
?>
<main class="flex-grow-1 p-4 bg-light">
    <!-- Título y botón de añadir -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-bold mb-0">Gestión de Direcciones</h2>
        <?php if ($canAddDirecciones): ?>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addDirModal">
                <i class="bi bi-geo-alt me-1"></i>Añadir Dirección
            </button>
        <?php endif; ?>
    </div>

    <!-- Mensajes de estado globales -->
    <?php cp_render_alert_message($message ?? null, $messageType ?? null); ?>

    <!-- TARJETAS DE DIRECCIONES (GRID) -->
    <div class="row g-4">
        <?php
$todasLasDirecciones = CPDir::leerTodo();
foreach ($todasLasDirecciones as $dir):
    if (!$canViewAllDirecciones && $dir->getEmail() !== $_SESSION['email']) {
        continue;
    }
?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border border-secondary border-opacity-25">
                    <div class="card-body">
                        <!-- Título de la dirección -->
                        <h5 class="card-title fw-bold text-primary mb-1"><?php echo htmlspecialchars($dir->getNombre() ?? '', ENT_QUOTES, 'UTF-8'); ?></h5>
                        <!-- Mostrar usuario creador si el rol tiene permiso -->
                        <?php if ($canViewCreator): ?>
                            <div class="small mb-2">
                                <i class="bi bi-person-circle me-2 text-info"></i><strong>Creado por:</strong> <?php echo htmlspecialchars($dir->getEmail() ?? '', ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Icono de ubicación y dirección real -->
                        <div class="small mb-2">
                            <i class="bi bi-pin-map-fill me-2 text-danger"></i><?php echo htmlspecialchars($dir->obtenerDireccionCompleta() ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="small mb-3">
                            <a href="<?php echo htmlspecialchars($dir->obtenerGoogleMapsUrl(), ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                <i class="bi bi-globe2 me-1"></i>Ver en Google Maps
                            </a>
                        </div>

                    </div>
                    
                    <!-- Botones de acción en la tarjeta -->
                    <div class="card-footer bg-transparent border-0 d-flex justify-content-end gap-2 pb-3">
                        <?php if ($canEditDirecciones): ?>
                            <button type="button" class="btn btn-sm btn-outline-warning edit-dir-btn" data-bs-toggle="modal" data-bs-target="#editDirModal"
                                data-id="<?php echo htmlspecialchars($dir->getId(), ENT_QUOTES, 'UTF-8'); ?>"
                                data-nombre="<?php echo htmlspecialchars($dir->getNombre(), ENT_QUOTES, 'UTF-8'); ?>"
                                data-calle="<?php echo htmlspecialchars($dir->getCalle(), ENT_QUOTES, 'UTF-8'); ?>"
                                data-numero="<?php echo htmlspecialchars($dir->getNumero(), ENT_QUOTES, 'UTF-8'); ?>"
                                data-puerta="<?php echo htmlspecialchars($dir->getPuerta(), ENT_QUOTES, 'UTF-8'); ?>"
                                data-codigoPostal="<?php echo htmlspecialchars($dir->getCodigoPostal(), ENT_QUOTES, 'UTF-8'); ?>"
                                data-ciudad="<?php echo htmlspecialchars($dir->getCiudad(), ENT_QUOTES, 'UTF-8'); ?>"
                                data-provincia="<?php echo htmlspecialchars($dir->getProvincia(), ENT_QUOTES, 'UTF-8'); ?>"
                            >
                                <i class="bi bi-pencil"></i>
                            </button>
                        <?php endif; ?>
                        <?php if ($canDeleteDirecciones): ?>
                            <form method="post" onsubmit="return confirm('¿Eliminar esta dirección? Esta acción borrará la entrada permanentemente.');">
                                <input type="hidden" name="id" value="<?php echo h($dir->getId()); ?>">
                                <button type="submit" name="delete_dir" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php
endforeach; ?>
    </div>
</main>

<!-- VENTANA FLOTANTE (MODAL): AÑADIR DIRECCIÓN -->
<div class="modal fade" id="addDirModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="post" class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title">Registrar Nueva Propiedad/Almacén</h5></div>
            <div class="modal-body row g-3">
                <div class="col-md-6"><label>Nombre de referencia</label><input type="text" name="nombre" class="form-control" placeholder="Ej: Mi Casa" required></div>
                    <div class="col-md-12"><!-- Los campos de descripción y ruta ya no se muestran --></div>
                <div class="col-md-6"><label>Calle / Plaza / Avenida</label><input type="text" name="calle" class="form-control"></div>
                <div class="col-md-3"><label>Número</label><input type="text" name="numero" class="form-control"></div>
                <div class="col-md-3"><label>Puerta</label><input type="text" name="puerta" class="form-control"></div>
                <div class="col-md-4">
                    <label>Código postal</label>
                    <?php if (!empty($nombrescodigosPostales)): ?>  
                        <div class="position-relative">
                            <input type="text" id="codigoPostal" name="codigoPostal" class="form-control" placeholder="Escribe el código..." autocomplete="off" required>
                            <div id="sugerenciasCodigoPostal" class="position-absolute w-100 mt-1 bg-white border rounded shadow-sm" style="display: none; max-height: 250px; overflow-y: auto; z-index: 1000;"></div>
                        </div>
                    <?php else: ?>
                        <p>No hay códigos postales disponibles.</p>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label>Ciudad</label>
                    <input id="ciudad" type="text" name="ciudad" class="form-control" readonly required>
                </div>

                <div class="col-md-4">
                    <label>Provincia</label>
                    <input id="provincia" type="text" name="provincia" class="form-control" readonly required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="add_dir" class="btn btn-primary px-4">Guardar Propiedad</button>
            </div>
        </form>
    </div>
</div>

<!-- VENTANA FLOTANTE (MODAL): EDITAR DIRECCIÓN -->
<div class="modal fade" id="editDirModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="post" class="modal-content">
            <input type="hidden" name="id" id="editDirId">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title">Editar Propiedad/Almacén</h5></div>
            <div class="modal-body row g-3">
                <div class="col-md-6"><label>Nombre de referencia</label><input type="text" id="editNombre" name="nombre" class="form-control" placeholder="Ej: Mi Casa" required></div>
                <div class="col-md-12"><!-- Los campos de descripción y ruta ya no se muestran --></div>
                <div class="col-md-6"><label>Calle / Plaza / Avenida</label><input type="text" id="editCalle" name="calle" class="form-control"></div>
                <div class="col-md-3"><label>Número</label><input type="text" id="editNumero" name="numero" class="form-control"></div>
                <div class="col-md-3"><label>Puerta</label><input type="text" id="editPuerta" name="puerta" class="form-control"></div>
                <div class="col-md-4">
                    <label>Código postal</label>
                    <?php if (!empty($nombrescodigosPostales)): ?>  
                        <div class="position-relative">
                            <input type="text" id="editCodigoPostal" name="codigoPostal" class="form-control" placeholder="Escribe el código..." autocomplete="off" required>
                            <div id="sugerenciasCodigoPostalEdit" class="position-absolute w-100 mt-1 bg-white border rounded shadow-sm" style="display: none; max-height: 250px; overflow-y: auto; z-index: 1000;"></div>
                        </div>
                    <?php else: ?>
                        <p>No hay códigos postales disponibles.</p>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label>Ciudad</label>
                    <input id="editCiudad" type="text" name="ciudad" class="form-control" readonly required>
                </div>

                <div class="col-md-4">
                    <label>Provincia</label>
                    <input id="editProvincia" type="text" name="provincia" class="form-control" readonly required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="edit_dir" class="btn btn-primary px-4">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
    const mapaCodigos = <?= json_encode($mapaCodigos, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
    const todosLosCodigos = Object.keys(mapaCodigos);

    function inicializarBuscadorPostal(inputId, suggestionsId, ciudadId, provinciaId) {
        const input = document.getElementById(inputId);
        const suggestions = document.getElementById(suggestionsId);
        const ciudad = document.getElementById(ciudadId);
        const provincia = document.getElementById(provinciaId);

        if (!input || !suggestions || !ciudad || !provincia) return;

        input.addEventListener('input', function() {
            const valor = this.value.trim().toLowerCase();
            if (valor.length === 0) {
                suggestions.style.display = 'none';
                return;
            }

            const coincidencias = todosLosCodigos.filter(codigo => codigo.toLowerCase().includes(valor)).slice(0, 10);
            if (coincidencias.length === 0) {
                suggestions.style.display = 'none';
                return;
            }

            suggestions.innerHTML = coincidencias.map(codigo =>
                `<div class="p-2 border-bottom cursor-pointer" style="cursor: pointer;" data-codigo="${htmlEscape(codigo)}">
                    ${htmlEscape(codigo)} - ${htmlEscape(mapaCodigos[codigo].ciudad)} (${htmlEscape(mapaCodigos[codigo].provincia)})
                </div>`
            ).join('');
            suggestions.style.display = 'block';

            suggestions.querySelectorAll('div[data-codigo]').forEach(opcion => {
                opcion.addEventListener('click', function() {
                    const codigoSeleccionado = this.dataset.codigo;
                    input.value = codigoSeleccionado;
                    suggestions.style.display = 'none';
                    actualizarCiudadProvincia(codigoSeleccionado, ciudad, provincia);
                });
                opcion.addEventListener('mouseover', function() {
                    this.style.backgroundColor = '#f0f0f0';
                });
                opcion.addEventListener('mouseout', function() {
                    this.style.backgroundColor = 'transparent';
                });
            });
        });

        document.addEventListener('click', function(e) {
            if (e.target !== input) {
                suggestions.style.display = 'none';
            }
        });

        input.addEventListener('change', function() {
            actualizarCiudadProvincia(this.value.trim(), ciudad, provincia);
        });
    }

    function actualizarCiudadProvincia(codigo, ciudad, provincia) {
        if (codigo && mapaCodigos[codigo]) {
            ciudad.value = mapaCodigos[codigo].ciudad || '';
            provincia.value = mapaCodigos[codigo].provincia || '';
        } else {
            ciudad.value = '';
            provincia.value = '';
        }
    }

    function htmlEscape(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    inicializarBuscadorPostal('codigoPostal', 'sugerenciasCodigoPostal', 'ciudad', 'provincia');
    inicializarBuscadorPostal('editCodigoPostal', 'sugerenciasCodigoPostalEdit', 'editCiudad', 'editProvincia');

    const editDirModal = document.getElementById('editDirModal');
    if (editDirModal) {
        editDirModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;

            document.getElementById('editDirId').value = button.dataset.id || '';
            document.getElementById('editNombre').value = button.dataset.nombre || '';
            document.getElementById('editCalle').value = button.dataset.calle || '';
            document.getElementById('editNumero').value = button.dataset.numero || '';
            document.getElementById('editPuerta').value = button.dataset.puerta || '';
            document.getElementById('editCodigoPostal').value = button.dataset.codigopostal || '';
            document.getElementById('editCiudad').value = button.dataset.ciudad || '';
            document.getElementById('editProvincia').value = button.dataset.provincia || '';
        });
    }
</script>

<?php require_once('footer.php'); ?>
