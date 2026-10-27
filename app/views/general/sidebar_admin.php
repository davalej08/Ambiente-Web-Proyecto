<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/**
 * Barra lateral del Panel de Administración 
 */

if (!isset($seccionPanel)) {
    $seccionPanel = '';
}
if (!isset($solicitudesPendientes)) {
    $solicitudesPendientes = 12;
}
if (!isset($admin)) {
    $admin = ['nombre' => 'Admin Ventica CR', 'correo' => 'admin@ventica.cr', 'iniciales' => 'AD'];
}

$enlacesPanel = [
    'inicio'          => ['texto' => 'Inicio',                     'icono' => 'cuadricula', 'url' => ruta('admin/dashboard')],
    'solicitudes'     => ['texto' => 'Emprendimientos Pendientes', 'icono' => 'reloj',      'url' => ruta('admin/solicitudes')],
    'emprendimientos' => ['texto' => 'Emprendimientos Activos',    'icono' => 'tienda',     'url' => ruta('admin/emprendimientos')],
    'categorias'      => ['texto' => 'Categorías',                 'icono' => 'filtros',    'url' => ruta('admin/categorias')],
    'usuarios'        => ['texto' => 'Usuarios',                   'icono' => 'usuario',    'url' => ruta('admin/usuarios')],
    'reportes'        => ['texto' => 'Reportes Generales',         'icono' => 'grafico',    'url' => ruta('admin/reportes')],
];
?>
<aside class="panel__lateral" aria-label="Menú del panel de administración">
    <a class="panel__marca" href="<?php echo e(ruta('admin/dashboard')); ?>">
        <span class="panel__marca-icono"><?php echo icono('tienda'); ?></span>
        <span>
            <strong>Ventica</strong>
            <small>Admin Portal</small>
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
                        <span><?php echo e($enlace['texto']); ?></span>
                        <?php if ($clave === 'solicitudes' && $solicitudesPendientes > 0): ?>
                            <span class="panel__contador"><?php echo (int) $solicitudesPendientes; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="panel__admin">
        <span class="panel__admin-avatar"><?php echo e($admin['iniciales']); ?></span>
        <span class="panel__admin-datos">
            <strong><?php echo e($admin['nombre']); ?></strong>
            <small><?php echo e($admin['correo']); ?></small>
        </span>
        <a class="panel__boton-icono" href="<?php echo e(ruta('auth/logout')); ?>" aria-label="Cerrar sesión" title="Cerrar sesión">
            <?php echo icono('salir'); ?>
        </a>
    </div>
</aside>
