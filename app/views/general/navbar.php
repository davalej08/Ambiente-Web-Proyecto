<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/**
 * Barra de navegación pública (cliente / visitante).
 * Se incluye desde header.php. Los paneles de Vendedor y Admin usan su propio sidebar.
 */

if (!isset($paginaActiva)) {
    $paginaActiva = '';
}
$usuarioSesion   = valor($_SESSION, 'usuario', null);        // ['nombre' => ..., 'rol' => ...]
if (!isset($cantidadCarrito)) {
    $cantidadCarrito = (isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0);
}

// "Mi cuenta" lleva a la pantalla de inicio de cada rol
$rutaCuenta = 'usuario/perfil';
if ($usuarioSesion && $usuarioSesion['rol'] === 'Emprendedor') {
    $rutaCuenta = 'vendedor/dashboard';
} elseif ($usuarioSesion && $usuarioSesion['rol'] === 'Administrador') {
    $rutaCuenta = 'admin/dashboard';
}

$enlaces = [
    'inicio'     => ['texto' => t('nav.inicio'),     'url' => ruta('')],
    'catalogo'   => ['texto' => t('nav.catalogo'),   'url' => ruta('catalogo')],
    'categorias' => ['texto' => t('nav.categorias'), 'url' => ruta('') . '#categorias'],
    'historias'  => ['texto' => t('nav.historias'),  'url' => ruta('historias')],
];

// Selector de idioma (HU-08)
$idiomas = ['es' => 'ES', 'en' => 'EN'];
?>
<header class="navbar">
    <div class="contenedor navbar__contenido">
        <a class="navbar__logo" href="<?php echo e(ruta('')); ?>">
            <img src="<?php echo e(recurso('img/logo-ventica.png')); ?>" alt="Ventica, mercado de emprendimientos costarricenses" width="138" height="48">
        </a>

        <button class="navbar__toggle" type="button" id="navbarToggle" aria-expanded="false" aria-controls="navbarMenu" aria-label="<?php echo e(t('nav.menu')); ?>">
            <?php echo icono('menu'); ?>
        </button>

        <nav class="navbar__menu" id="navbarMenu" aria-label="Navegación principal">
            <ul class="navbar__enlaces">
                <?php foreach ($enlaces as $clave => $enlace): ?>
                    <li>
                        <a class="navbar__enlace <?php echo $paginaActiva === $clave ? 'es-activo' : ''; ?>"
                           href="<?php echo e($enlace['url']); ?>"
                           <?php echo $paginaActiva === $clave ? 'aria-current="page"' : ''; ?>>
                            <?php echo e($enlace['texto']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="navbar__acciones">
                <div class="navbar__idioma" role="group" aria-label="<?php echo e(t('nav.idioma')); ?>">
                    <?php foreach ($idiomas as $codigo => $etiqueta): ?>
                        <a class="<?php echo idioma() === $codigo ? 'es-activo' : ''; ?>" href="<?php echo e(ruta('idioma?l=' . $codigo)); ?>"
                           lang="<?php echo $codigo; ?>" <?php echo idioma() === $codigo ? 'aria-current="true"' : ''; ?>><?php echo $etiqueta; ?></a>
                    <?php endforeach; ?>
                </div>

                <a class="navbar__carrito" href="<?php echo e(ruta('carrito')); ?>" aria-label="<?php echo e(t('nav.carrito')); ?> (<?php echo (int) $cantidadCarrito; ?>)">
                    <?php echo icono('carrito'); ?>
                    <?php if ($cantidadCarrito > 0): ?>
                        <span class="navbar__contador"><?php echo (int) $cantidadCarrito; ?></span>
                    <?php endif; ?>
                </a>

                <?php if ($usuarioSesion): ?>
                    <a class="navbar__enlace" href="<?php echo e(ruta($rutaCuenta)); ?>">
                        <?php echo icono('usuario'); ?> <?php echo e(valor($usuarioSesion, 'nombre', t('nav.cuenta'))); ?>
                    </a>
                    <a class="btn btn--borde btn--sm" href="<?php echo e(ruta('auth/logout')); ?>">
                        <?php echo icono('salir'); ?> <?php echo e(t('nav.salir')); ?>
                    </a>
                <?php else: ?>
                    <a class="navbar__enlace" href="<?php echo e(ruta('auth/login')); ?>"><?php echo e(t('nav.entrar')); ?></a>
                    <a class="btn btn--borde btn--sm" href="<?php echo e(ruta('auth/registro-vendedor')); ?>">
                        <?php echo icono('tienda'); ?> <?php echo e(t('nav.vender')); ?>
                    </a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>