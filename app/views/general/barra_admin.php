<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/**
 * Barra superior del panel de administración
 */

if (!isset($hayNotificaciones)) {
    $hayNotificaciones = true;
}
if (!isset($perfilAdmin)) {
    $perfilAdmin = ['rol' => 'Superadmin', 'ubicacion' => 'San José, CR'];
}
?>
<header class="panel__barra">
    <p class="panel__consola"><?php echo icono('verificado'); ?> Consola de Administración Central • Costa Rica</p>

    <form class="panel__buscador" role="search" method="get" action="<?php echo e(ruta('admin/buscar')); ?>">
        <label class="oculto-visual" for="busquedaGeneral">Buscar en la plataforma</label>
        <?php echo icono('buscar'); ?>
        <input id="busquedaGeneral" name="q" type="search" placeholder="Buscar comercios, solicitudes, cédula jurídica...">
    </form>

    <div class="panel__barra-acciones">
        <a class="panel__boton-icono" href="<?php echo e(ruta('admin/solicitudes')); ?>" aria-label="Notificaciones">
            <?php echo icono('campana'); ?>
            <?php if ($hayNotificaciones): ?>
                <span class="panel__aviso" aria-hidden="true"></span>
            <?php endif; ?>
        </a>
        <div class="panel__perfil">
            <img src="<?php echo e(recurso('img/logo-mini.png')); ?>" alt="" width="34" height="34">
            <span>
                <strong><?php echo e($perfilAdmin['rol']); ?></strong>
                <small><?php echo e($perfilAdmin['ubicacion']); ?></small>
            </span>
        </div>
    </div>
</header>
