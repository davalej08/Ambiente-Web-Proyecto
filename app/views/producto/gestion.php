<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Gestión de productos del vendedor (RF-12) (Figma: Vendedor · Mis Productos)
 * Ruta sugerida: vendedor/productos
 *
 * Variables que puede enviar VendedorController:
 *   $productos      arreglo de productos del vendedor (ver datos de muestra abajo)
 *   $totalProductos total de productos publicados
 *   $paginaActual   número de página
 *   $totalPaginas   cantidad de páginas
 * Si no llegan, se usan datos de muestra para el Avance 2.
 */

$titulo       = 'Mis Productos · Portal del emprendedor';
$estilos      = ['vendedor'];
$scripts      = ['vendedor'];
$navbar       = false;
$footer       = false;
$claseBody    = 'body-panel';
$seccionPanel = 'productos';

if (!isset($productos)) {
    $productos = [
        ['id' => 1, 'nombre' => 'Café Geisha Reserva Especial',  'descripcion' => 'Grano entero de altura, tueste medio',      'categoria' => 'Café',      'precio' => 9500,  'stock' => 24, 'activo' => true,  'imagen' => 'productos/cafe-geisha.jpg'],
        ['id' => 2, 'nombre' => 'Tabla de Madera Teca Noble',    'descripcion' => 'Madera noble certificada sostenible',       'categoria' => 'Artesanía', 'precio' => 18500, 'stock' => 6,  'activo' => true,  'imagen' => 'productos/tabla-teca.jpg'],
        ['id' => 3, 'nombre' => 'Miel Cruda de Bosque Tropical', 'descripcion' => 'Cosecha silvestre de los cafetales',        'categoria' => 'Gourmet',   'precio' => 5800,  'stock' => 15, 'activo' => true,  'imagen' => 'productos/miel-cruda.jpg'],
        ['id' => 4, 'nombre' => 'Tote Bag de Yute Cafetalero',   'descripcion' => 'Sacos de exportación reutilizados',         'categoria' => 'Hogar',     'precio' => 16000, 'stock' => 0,  'activo' => false, 'imagen' => 'productos/tote-yute.jpg'],
        ['id' => 5, 'nombre' => 'Chocolate Fino 75% Bribri',     'descripcion' => 'Cacao ancestral de Talamanca',              'categoria' => 'Gourmet',   'precio' => 3800,  'stock' => 40, 'activo' => true,  'imagen' => 'productos/chocolate-bribri.jpg'],
        ['id' => 6, 'nombre' => 'Jabón Romero y Cera de Abeja',  'descripcion' => 'Ingredientes 100% botánicos',               'categoria' => 'Hogar',     'precio' => 6500,  'stock' => 3,  'activo' => true,  'imagen' => 'productos/jabon-romero.jpg'],
    ];
}
if (!isset($totalProductos)) {
    $totalProductos = 18;
}
if (!isset($paginaActual)) {
    $paginaActual = isset($_GET['pagina']) ? max(1, (int) $_GET['pagina']) : 1;
}
if (!isset($totalPaginas)) {
    $totalPaginas = 3;
}

require __DIR__ . '/../general/header.php';
?>

<div class="panel">
    <?php require __DIR__ . '/../general/sidebar_vendedor.php'; ?>

    <div class="panel__principal">
        <?php require __DIR__ . '/../general/barra_vendedor.php'; ?>

        <main id="contenido" class="panel__contenido">

            <section class="gestion__cabecera">
                <div class="encabezado-pagina">
                    <p class="fila gestion__estado">
                        <span class="chip">Inventario artesanal</span>
                        <span><span class="punto"></span> Actualizado hace 24 min</span>
                    </p>
                    <h1 class="titulo-pagina">Mis Productos</h1>
                    <p class="texto-guia">Gestioná la visibilidad y disponibilidad de tus piezas en el mercado nacional.</p>
                </div>

                <div class="gestion__acciones">
                    <a class="btn btn--primario" href="<?php echo e(ruta('vendedor/productos/nuevo')); ?>">
                        <?php echo icono('mas'); ?> Agregar producto
                    </a>
                    <div class="contador tarjeta">
                        <span class="indicador__icono indicador__icono--azul"><?php echo icono('caja'); ?></span>
                        <span>
                            <strong><?php echo (int) $totalProductos; ?> productos</strong>
                            <small>Catálogo publicado</small>
                        </span>
                    </div>
                </div>
            </section>

            <section class="tarjeta tabla-productos">
                <?php if (count($productos) === 0): ?>
                    <div class="vacio">
                        <?php echo icono('caja'); ?>
                        <h2 class="titulo-seccion">Todavía no tenés productos</h2>
                        <p class="texto-guia">Publicá tu primera pieza para que aparezca en el catálogo de Ventica.</p>
                        <a class="btn btn--oscuro" href="<?php echo e(ruta('vendedor/productos/nuevo')); ?>"><?php echo icono('mas'); ?> Agregar producto</a>
                    </div>
                <?php else: ?>
                    <div class="tabla-envoltura">
                        <table class="tabla">
                            <thead>
                                <tr>
                                    <th scope="col">Producto</th>
                                    <th scope="col">Categoría</th>
                                    <th scope="col">Stock</th>
                                    <th scope="col" class="tabla__numero">Precio</th>
                                    <th scope="col">Disponibilidad</th>
                                    <th scope="col"><span class="oculto-visual">Acciones</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $producto): ?>
                                    <?php $urlEditar = ruta('vendedor/productos/editar') . '?id=' . (int) $producto['id']; ?>
                                    <tr>
                                        <td>
                                            <div class="producto-celda">
                                                <img src="<?php echo e(recurso('img/uploads/' . $producto['imagen'])); ?>" alt="" width="56" height="56">
                                                <div>
                                                    <a class="producto-celda__nombre" href="<?php echo e($urlEditar); ?>"><?php echo e($producto['nombre']); ?></a>
                                                    <small><?php echo e($producto['descripcion']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="chip"><?php echo e($producto['categoria']); ?></span></td>
                                        <td>
                                            <?php if ($producto['stock'] === 0): ?>
                                                <span class="estado estado--rechazado">Agotado</span>
                                            <?php elseif ($producto['stock'] <= 5): ?>
                                                <span class="estado estado--pendiente"><?php echo (int) $producto['stock']; ?> · Stock bajo</span>
                                            <?php else: ?>
                                                <?php echo (int) $producto['stock']; ?> uds.
                                            <?php endif; ?>
                                        </td>
                                        <td class="tabla__numero tabla__precio"><?php echo colones($producto['precio']); ?></td>
                                        <td>
                                            <form method="post" action="<?php echo e(ruta('vendedor/productos/disponibilidad')); ?>">
                                                <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                                                <input type="hidden" name="id_producto" value="<?php echo (int) $producto['id']; ?>">
                                                <label class="interruptor">
                                                    <input class="interruptor__entrada" type="checkbox" name="activo" value="1"
                                                        <?php echo $producto['activo'] ? 'checked' : ''; ?>
                                                        aria-label="Disponibilidad de <?php echo e($producto['nombre']); ?>">
                                                    <span class="interruptor__texto"><?php echo $producto['activo'] ? 'Activo' : 'Pausado'; ?></span>
                                                    <span class="interruptor__pista" aria-hidden="true"></span>
                                                </label>
                                            </form>
                                        </td>
                                        <td>
                                            <a class="panel__boton-icono" href="<?php echo e($urlEditar); ?>" aria-label="Editar <?php echo e($producto['nombre']); ?>">
                                                <?php echo icono('lapiz'); ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="tabla-productos__pie">
                        <p><?php echo icono('escudo'); ?> <span>Tus productos cumplen con el sello de calidad artesanal <strong>Ventica Pura Vida</strong>.</span></p>
                        <nav class="paginador" aria-label="Paginación de productos">
                            <?php if ($paginaActual > 1): ?>
                                <a href="<?php echo e(ruta('vendedor/productos') . '?pagina=' . ($paginaActual - 1)); ?>" aria-label="Página anterior"><?php echo icono('izquierda'); ?></a>
                            <?php endif; ?>
                            <span><?php echo (int) $paginaActual; ?> de <?php echo (int) $totalPaginas; ?></span>
                            <?php if ($paginaActual < $totalPaginas): ?>
                                <a href="<?php echo e(ruta('vendedor/productos') . '?pagina=' . ($paginaActual + 1)); ?>" aria-label="Página siguiente"><?php echo icono('derecha'); ?></a>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>
            </section>

            <section class="consejo">
                <span class="consejo__icono"><?php echo icono('destellos'); ?></span>
                <div>
                    <h2>Consejo para tus piezas</h2>
                    <p>Los productos con fotografías con luz natural y detalles de confección reciben hasta 3 veces más consultas de compradores.</p>
                </div>
                <a class="btn btn--borde btn--sm" href="<?php echo e(ruta('ayuda')); ?>">Ver guía de fotos</a>
            </section>

        </main>
    </div>
</div>

<?php require __DIR__ . '/../general/footer.php'; ?>
