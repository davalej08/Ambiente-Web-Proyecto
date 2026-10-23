<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/**
 * Barra lateral del Portal del Emprendedor (panel del vendedor).
 * Se incluye en cada pantalla del vendedor, dentro de <div class="panel">.
 *
 * Variables que puede definir la vista antes de incluirla:
 *   $seccionPanel  string  Enlace activo: inicio | productos | pedidos | reportes | perfil
 *   $tienda        array   ['nombre' => ..., 'ubicacion' => ...] (lo envía VendedorController)
 */

if (!isset($seccionPanel)) {
    $seccionPanel = '';
}
if (!isset($tienda)) {
    $tienda = ['nombre' => 'Café Don Mincho', 'ubicacion' => 'Tarrazú, Costa Rica'];
}

$enlacesPanel = [
    'inicio'    => ['texto' => 'Inicio',       'icono' => 'cuadricula', 'url' => ruta('vendedor/dashboard')],
    'productos' => ['texto' => 'Mis Productos', 'icono' => 'caja',       'url' => ruta('vendedor/productos')],
    'pedidos'   => ['texto' => 'Pedidos',       'icono' => 'camion',     'url' => ruta('vendedor/pedidos')],
    'reportes'  => ['texto' => 'Reportes',      'icono' => 'grafico',    'url' => ruta('vendedor/reportes')],
    'perfil'    => ['texto' => 'Mi Perfil',     'icono' => 'tienda',     'url' => ruta('vendedor/perfil')],
];
?>
<aside class="panel__lateral" aria-label="Menú del portal del emprendedor">
    <a class="panel__marca" href="<?php echo e(ruta('vendedor/dashboard')); ?>">
        <span class="panel__marca-icono"><?php echo icono('tienda'); ?></span>
        <span>
            <strong>Ventica</strong>
            <small>Portal del emprendedor</small>
        </span>
    </a>

    <nav class="panel__menu">
        <ul>
            <?php foreach ($enlacesPanel as $clave => $enlace): ?>
                <li>
                    <a class="panel__enlace <?php echo $seccionPanel === $clave ? 'es-activo' : ''; ?>"
                       href="<?php echo e($enlace['url']); ?>"
                       <?php echo $seccionPanel === $clave ? 'aria-current="page"' : ''; ?>>
                        <?php echo icono($enlace['icono']); ?>
                        <?php echo e($enlace['texto']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="panel__tienda">
        <span class="panel__tienda-icono"><?php echo icono('cafe'); ?></span>
        <span>
            <strong><?php echo e($tienda['nombre']); ?></strong>
            <small><?php echo icono('verificado'); ?> <?php echo e($tienda['ubicacion']); ?></small>
        </span>
    </div>
</aside>
