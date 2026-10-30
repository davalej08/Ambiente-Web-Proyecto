<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/**
 * Pie de página común. Cierra el documento que abrió header.php.
 * Usa $footer = false en la vista para ocultarlo (login, registro, paneles).
 */

if (!isset($footer)) {
    $footer = true;
}
if (!isset($scripts)) {
    $scripts = [];
}
?>
<?php if ($footer): ?>
    <footer class="footer">
        <div class="contenedor footer__contenido">
            <div class="footer__marca">
                <a href="<?php echo e(ruta('')); ?>">
                    <img src="<?php echo e(recurso('img/logo-ventica.png')); ?>" alt="Ventica" width="92" height="32">
                </a>
                <p>Mercado y directorio de talento costarricense</p>
            </div>

            <nav class="footer__enlaces" aria-label="Enlaces del pie de página">
                <a href="<?php echo e(ruta('catalogo')); ?>">Directorio de Provincias</a>
                <a href="<?php echo e(ruta('ayuda')); ?>">Preguntas Frecuentes</a>
                <a href="<?php echo e(ruta('terminos')); ?>">Términos de Comercio</a>
                <a href="<?php echo e(ruta('contacto')); ?>">Contacto</a>
            </nav>

            <div class="footer__legal">
                <p>&copy; <?php echo date('Y'); ?> Ventica.</p>
                <p>Hecho con orgullo en Costa Rica</p>
            </div>
        </div>
    </footer>
<?php endif; ?>

    <!-- Pantalla de carga: general.js la muestra mientras se abre otra página o se envía un formulario -->
    <div class="carga" id="pantallaCarga" aria-hidden="true">
        <div class="carga__tarjeta" role="status">
            <span class="carga__logo">
                <span class="carga__anillo"></span>
                <img src="<?php echo e(recurso('img/colibri.png')); ?>" alt="" width="64" height="64">
            </span>
            <p class="carga__texto">Cargando<span class="carga__puntos"><span>.</span><span>.</span><span>.</span></span></p>
            <p class="carga__nota">Trayendo lo mejor hecho en Costa Rica</p>
        </div>
    </div>

    <!-- Scripts base compartidos (menú móvil, mensajes, mostrar contraseña, pantalla de carga) -->
    <script src="<?php echo e(recurso('js/general.js')); ?>"></script>

    <!-- Scripts propios de cada página -->
    <?php foreach ($scripts as $script): ?>
        <script src="<?php echo e(recurso('js/' . $script . '.js')); ?>"></script>
    <?php endforeach; ?>
</body>
</html>
