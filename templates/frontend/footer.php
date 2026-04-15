    </div> <!-- Aquí cerramos el contenedor lateral abierto en el head.php -->

    <!-- PIE DE PÁGINA (FOOTER) -->
    <footer class="container-fluid py-3 bg-light border-top">
        <div class="d-flex justify-content-between align-items-center px-4">
            <span class="text-body-secondary small">© 2026 Sistema Gestor Personal Premium</span>
            <ul class="nav">
                <li class="nav-item small">
                    <a href="#" class="nav-link px-2 text-body-secondary">Centro de Soporte</a>
                </li>
            </ul>
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
</body>
</html>