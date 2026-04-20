    </div> <!-- Aquí cerramos el contenedor lateral abierto en el head.php -->

    <!-- PIE DE PÁGINA (FOOTER) -->
    <footer class="container-fluid py-3 bg-light border-top mt-auto">
        <div class="d-flex align-items-center justify-content-between px-4">
            <span class="text-body-secondary small">© 2026 Sistema Gestor Personal Premium</span>
            <a href="#" class="nav-link px-2 text-body-secondary small mb-0">Centro de Soporte</a>
        </div>
    </footer>

    <?php
    /**
     * CARGA DE ARCHIVOS JAVASCRIPT
     * Detectamos la ruta base para que el archivo de Bootstrap se cargue bien desde cualquier página.
     */
    $rutaBaseScript = str_replace(['/templates/frontend', '/templates/backend'], '', dirname($_SERVER['SCRIPT_NAME']));
    $rutaBaseScript = rtrim($rutaBaseScript, '/\\');
    ?>
    <!-- Esto activa los menús desplegables y animaciones de Bootstrap -->
    <script src="<?php echo h($rutaBaseScript); ?>/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            var btn = document.getElementById('toggleDarkModeBtn');
            var storageKey = 'gestorPersonalDarkMode';

            function setDarkMode(enabled) {
                document.body.classList.toggle('dark-mode', enabled);
                if (btn) {
                    btn.textContent = enabled ? 'Modo claro' : 'Modo noche';
                }
                try {
                    localStorage.setItem(storageKey, enabled ? '1' : '0');
                } catch (e) {
                    // Ignore if localStorage is unavailable.
                }
            }

            if (!btn) return;

            btn.addEventListener('click', function() {
                setDarkMode(!document.body.classList.contains('dark-mode'));
            });

            var saved = null;
            try {
                saved = localStorage.getItem(storageKey);
            } catch (e) {
                saved = null;
            }
            setDarkMode(saved === '1');
        })();
    </script>
</body>
</html>