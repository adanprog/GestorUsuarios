<?php
/**
 * VISTA: DIRECCIONES
 * ------------------
 * Muestra las direcciones almacenadas, permite crear nuevas y
 * editar o borrar las existentes según los permisos del usuario.
 */
require_once('../backend/procesar_direcciones.php'); // Lógica para guardar, editar o borrar direcciones.
require_once __DIR__ . '/../../clases/CPUser.php';

// Cargamos las partes comunes de la página: estilos, menú y seguridad.
require_once('head.php');
require_once('sidebar.php');

// Preparación de los códigos postales.
// El sistema usa un archivo Excel grande solo la primera vez,
// luego guarda la información en un archivo JSON más rápido.
$dirArchivoCodigos = __DIR__ . '/../../archivos/codigos_postales_municipios.xlsx';
$fileJsonCache = __DIR__ . '/../../archivos/codigos_postales_cache.json';

// Si no existe el caché o el archivo Excel es más reciente, regeneramos los datos.
if (!file_exists($fileJsonCache) || filemtime($dirArchivoCodigos) > filemtime($fileJsonCache)) {
    require_once '../clases/CPDesplegableDir.php';
    $codigoPostal = new CPDesplegableDir($dirArchivoCodigos, 'A');
    $mapaCodigos = $codigoPostal->obtenerMapaCpCiudadProvincia('A', 'C', 'B');
    // Guardar el resultado en un archivo JSON para usarlo más rápido después.
    file_put_contents($fileJsonCache, json_encode($mapaCodigos));
} else {
    // Si ya hay un archivo JSON guardado, lo leemos directamente.
    $mapaCodigos = json_decode(file_get_contents($fileJsonCache), true);
}

// Creamos una lista ordenada de códigos postales disponibles.
$nombrescodigosPostales = array_keys($mapaCodigos);
sort($nombrescodigosPostales, SORT_STRING);

// Consultamos el usuario que ha iniciado sesión y sus permisos.
$currentUser = CPUser::buscarPorEmail($_SESSION['email']);
$canAddDirecciones = $currentUser ? $currentUser->canAddDirecciones() : false;
$canEditDirecciones = $currentUser ? $currentUser->canEditDirecciones() : false;
$canDeleteDirecciones = $currentUser ? $currentUser->canDeleteDirecciones() : false;
$canViewAllDirecciones = $currentUser ? $currentUser->canViewAllDirecciones() : false;
$canViewCreator = $currentUser ? $currentUser->canViewCreator() : false;
?>
<main class="flex-grow-1 p-4 bg-light">
    <!-- Encabezado de la página: título y botón para añadir una nueva dirección -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-bold mb-0">Gestión de Direcciones</h2>
        <?php if ($canAddDirecciones): ?>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addDirModal">
                <i class="bi bi-geo-alt me-1"></i>Añadir Dirección
            </button>
        <?php endif; ?>
    </div>

    <!-- Aquí se muestra un mensaje cuando se guarda, edita o borra una dirección. -->
    <?php cp_render_alert_message($message ?? null, $messageType ?? null); ?>

    <!-- Buscador de direcciones -->
    <div class="mb-4">
        <div class="input-group">
            <span class="input-group-text bg-white">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" id="searchDirecciones" class="form-control" placeholder="Buscar por nombre, propietario, calle, ciudad...">
        </div>
    </div>

    <!-- Lista de tarjetas con cada dirección visible al usuario -->
    <div class="row g-4" id="direccionesContainer">
        <?php
$todasLasDirecciones = CPDir::leerTodo();
foreach ($todasLasDirecciones as $dir):
    if (!$canViewAllDirecciones && $dir->getEmail() !== $_SESSION['email']) {
        continue;
    }
?>
            <div class="col-md-6 col-lg-4 direccion-card" data-nombre="<?php echo htmlspecialchars($dir->getNombre(), ENT_QUOTES, 'UTF-8'); ?>" data-propietario="<?php echo htmlspecialchars($dir->getNombrePropietario(), ENT_QUOTES, 'UTF-8'); ?>" data-calle="<?php echo htmlspecialchars($dir->getCalle(), ENT_QUOTES, 'UTF-8'); ?>" data-ciudad="<?php echo htmlspecialchars($dir->getCiudad(), ENT_QUOTES, 'UTF-8'); ?>" data-provincia="<?php echo htmlspecialchars($dir->getProvincia(), ENT_QUOTES, 'UTF-8'); ?>" data-email-prop="<?php echo htmlspecialchars($dir->getEmailPropietario(), ENT_QUOTES, 'UTF-8'); ?>" data-tel-prop="<?php echo htmlspecialchars($dir->getTelefonoPropietario(), ENT_QUOTES, 'UTF-8'); ?>">
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
                        
                        <!-- Datos del propietario -->
                        <?php if ($dir->getNombrePropietario() || $dir->getTelefonoPropietario() || $dir->getEmailPropietario()): ?>
                            <hr class="my-2">
                            <div class="small"><strong>Propietario:</strong></div>
                            <?php if ($dir->getNombrePropietario()): ?>
                                <div class="small mb-1">
                                    <i class="bi bi-person me-2 text-secondary"></i><?php echo htmlspecialchars($dir->getNombrePropietario(), ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($dir->getTelefonoPropietario()): ?>
                                <div class="small mb-1">
                                    <i class="bi bi-telephone me-2 text-secondary"></i><a href="tel:<?php echo htmlspecialchars($dir->getTelefonoPropietario(), ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none"><?php echo htmlspecialchars($dir->getTelefonoPropietario(), ENT_QUOTES, 'UTF-8'); ?></a>
                                </div>
                            <?php endif; ?>
                            <?php if ($dir->getEmailPropietario()): ?>
                                <div class="small mb-2">
                                    <i class="bi bi-envelope me-2 text-secondary"></i><a href="mailto:<?php echo htmlspecialchars($dir->getEmailPropietario(), ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none"><?php echo htmlspecialchars($dir->getEmailPropietario(), ENT_QUOTES, 'UTF-8'); ?></a>
                                </div>
                            <?php endif; ?>
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
                        <!-- Botones para editar o borrar la dirección según permisos -->
                        <?php if ($canEditDirecciones): ?>
                            <button type="button" class="btn btn-sm btn-outline-warning edit-dir-btn" data-bs-toggle="modal" data-bs-target="#editDirModal"
                                data-id="<?php echo htmlspecialchars($dir->getId(), ENT_QUOTES, 'UTF-8'); ?>"
                                data-nombre="<?php echo htmlspecialchars($dir->getNombre(), ENT_QUOTES, 'UTF-8'); ?>"
                                data-nombrePropietario="<?php echo htmlspecialchars($dir->getNombrePropietario(), ENT_QUOTES, 'UTF-8'); ?>"
                                data-telefonoPropietario="<?php echo htmlspecialchars($dir->getTelefonoPropietario(), ENT_QUOTES, 'UTF-8'); ?>"
                                data-emailPropietario="<?php echo htmlspecialchars($dir->getEmailPropietario(), ENT_QUOTES, 'UTF-8'); ?>"
                                data-email="<?php echo htmlspecialchars($dir->getEmail(), ENT_QUOTES, 'UTF-8'); ?>"
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
                <div class="col-md-12"><h6 class="fw-bold text-secondary mt-2">Datos del Propietario</h6></div>
                <div class="col-md-6"><label>Nombre del propietario</label><input type="text" name="nombrePropietario" class="form-control" placeholder="Ej: Juan Pérez"></div>
                <div class="col-md-6"><label>Teléfono</label><input type="text" name="telefonoPropietario" class="form-control" placeholder="Ej: 123-456-7890"></div>
                <div class="col-md-6"><label>Email del propietario</label><input type="email" name="emailPropietario" class="form-control" placeholder="propietario@ejemplo.com"></div>
                <div class="col-md-6"><label>Email del creador</label><input type="email" class="form-control" readonly value="<?php echo htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8'); ?>"></div>
                <div class="col-md-12"><h6 class="fw-bold text-secondary mt-2">Ubicación de la Propiedad</h6></div>
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
                <div class="col-md-12"><h6 class="fw-bold text-secondary mt-2">Datos del Propietario</h6></div>
                <div class="col-md-6"><label>Nombre del propietario</label><input type="text" id="editNombrePropietario" name="nombrePropietario" class="form-control" placeholder="Ej: Juan Pérez"></div>
                <div class="col-md-6"><label>Teléfono</label><input type="text" id="editTelefonoPropietario" name="telefonoPropietario" class="form-control" placeholder="Ej: 123-456-7890"></div>
                <div class="col-md-6"><label>Email del propietario</label><input type="email" id="editEmailPropietario" name="emailPropietario" class="form-control" placeholder="propietario@ejemplo.com"></div>
                <div class="col-md-6"><label>Email del creador</label><input type="email" id="editEmail" class="form-control" readonly></div>
                <div class="col-md-12"><h6 class="fw-bold text-secondary mt-2">Ubicación de la Propiedad</h6></div>
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
    // Mapa de códigos postales cargado desde PHP.
    const mapaCodigos = <?= json_encode($mapaCodigos, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
    const todosLosCodigos = Object.keys(mapaCodigos);

    /**
     * Prepara un buscador de código postal que muestra sugerencias al escribir.
     * inputId: campo donde el usuario escribe el código postal.
     * suggestionsId: contenedor donde se muestran las opciones.
     * ciudadId / provinciaId: campos que se rellenan automáticamente.
     */
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
        // Rellena ciudad y provincia según el código postal seleccionado.
        if (codigo && mapaCodigos[codigo]) {
            ciudad.value = mapaCodigos[codigo].ciudad || '';
            provincia.value = mapaCodigos[codigo].provincia || '';
        } else {
            ciudad.value = '';
            provincia.value = '';
        }
    }

    /**
     * Evita que el texto con símbolos rompa el HTML de la lista de sugerencias.
     */
    function htmlEscape(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    inicializarBuscadorPostal('codigoPostal', 'sugerenciasCodigoPostal', 'ciudad', 'provincia');
    inicializarBuscadorPostal('editCodigoPostal', 'sugerenciasCodigoPostalEdit', 'editCiudad', 'editProvincia');

    // Buscador de direcciones
    const searchInput = document.getElementById('searchDirecciones');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const cards = document.querySelectorAll('.direccion-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const nombre = card.dataset.nombre || '';
                const propietario = card.dataset.propietario || '';
                const calle = card.dataset.calle || '';
                const ciudad = card.dataset.ciudad || '';
                const provincia = card.dataset.provincia || '';
                const emailProp = card.dataset.emailProp || '';
                const telProp = card.dataset.telProp || '';
                
                const searchText = (nombre + ' ' + propietario + ' ' + calle + ' ' + ciudad + ' ' + provincia + ' ' + emailProp + ' ' + telProp).toLowerCase();
                
                if (searchText.includes(searchTerm)) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Mostrar mensaje si no hay resultados
            const container = document.getElementById('direccionesContainer');
            let noResults = container.querySelector('.no-results-message');
            if (visibleCount === 0 && searchTerm !== '') {
                if (!noResults) {
                    noResults = document.createElement('div');
                    noResults.className = 'col-12 no-results-message';
                    noResults.innerHTML = '<div class="alert alert-info"><i class="bi bi-search me-2"></i>No se encontraron direcciones que coincidan con "' + htmlEscape(searchTerm) + '"</div>';
                    container.appendChild(noResults);
                } else {
                    noResults.innerHTML = '<div class="alert alert-info"><i class="bi bi-search me-2"></i>No se encontraron direcciones que coincidan con "' + htmlEscape(searchTerm) + '"</div>';
                    noResults.style.display = '';
                }
            } else if (noResults) {
                noResults.style.display = 'none';
            }
        });
    }

    const editDirModal = document.getElementById('editDirModal');
    if (editDirModal) {
        // Cuando se abre el modal de edición, copiamos los datos de la tarjeta a los campos del formulario.
        editDirModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;

            document.getElementById('editDirId').value = button.dataset.id || '';
            document.getElementById('editNombre').value = button.dataset.nombre || '';
            document.getElementById('editNombrePropietario').value = button.dataset.nombrepropietario || '';
            document.getElementById('editTelefonoPropietario').value = button.dataset.telefonopropietario || '';
            document.getElementById('editEmailPropietario').value = button.dataset.emailpropietario || '';
            document.getElementById('editEmail').value = button.dataset.email || '';
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
