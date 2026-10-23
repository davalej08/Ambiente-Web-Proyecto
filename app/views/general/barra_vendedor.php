<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/**
 * Barra superior del panel del vendedor (etiqueta, notificaciones, ayuda y salir).
 *
 * Variables opcionales:
 *   $pillPanel           string  Texto de la etiqueta de la izquierda
 *   $notificaciones      int     Cantidad de notificaciones sin leer
 */

if (!isset($pillPanel)) {
    $pillPanel = 'Costa Rica · Artesanal & Sostenible';
}
if (!isset($notificaciones)) {
    $notificaciones = 2;
}
?>
<header class="panel__barra">
    <span class="pill"><?php echo icono('hoja'); ?> <?php echo e($pillPanel); ?></span>

    <div class="panel__barra-acciones">
        <a class="panel__boton-icono" href="<?php echo e(ruta('vendedor/pedidos')); ?>" aria-label="Notificaciones (<?php echo (int) $notificaciones; ?> sin leer)">
            <?php echo icono('campana'); ?>
            <?php if ($notificaciones > 0): ?>
                <span class="panel__aviso" aria-hidden="true"></span>
            <?php endif; ?>
        </a>
        <a class="panel__boton-icono" href="<?php echo e(ruta('ayuda')); ?>" aria-label="Ayuda">
            <?php echo icono('ayuda'); ?>
        </a>
        <a class="btn btn--borde btn--sm" href="<?php echo e(ruta('auth/logout')); ?>">
            <?php echo icono('salir'); ?> Salir
        </a>
    </div>
</header>
