<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Panel principal del vendedor (Figma: Vendedor · Dashboard)
 * Ruta sugerida: vendedor/dashboard
 *
 * Variables que puede enviar VendedorController:
 *   $tienda           ['nombre', 'ubicacion', 'provincia']
 *   $resumen          ['ventas_mes', 'variacion', 'meta', 'por_confirmar', 'catalogo', 'stock_bajo']
 *   $pedidosRecientes arreglo de pedidos (ver datos de muestra abajo para las llaves)
 * Si no llegan, se usan datos de muestra para el Avance 2.
 */

$titulo       = 'Inicio · Portal del emprendedor';
$estilos      = ['vendedor'];
$scripts      = ['vendedor'];
$navbar       = false;
$footer       = false;
$claseBody    = 'body-panel';
$seccionPanel = 'inicio';
$pillPanel    = 'Emprendedor Local Verificado';

if (!isset($tienda)) {
    $tienda = ['nombre' => 'Café Don Mincho', 'ubicacion' => 'Tarrazú, Costa Rica', 'provincia' => 'Tarrazú, San José'];
}

if (!isset($resumen)) {
    $resumen = [
        'ventas_mes'    => 485000,
        'variacion'     => 12,
        'meta'          => 600000,
        'por_confirmar' => 4,
        'catalogo'      => 18,
        'stock_bajo'    => 2,
    ];
}

if (!isset($pedidosRecientes)) {
    $pedidosRecientes = [
        ['id' => 8921, 'cliente' => 'Mariana Solís Castro',  'ubicacion' => 'San José, Escazú', 'detalle' => '1× Café Geisha Reserva Especial (340 g)',  'total' => 38500, 'recibido' => 'Hoy, 10:24 am',  'imagen' => 'productos/cafe-geisha.jpg'],
        ['id' => 8919, 'cliente' => 'Carlos Alvarado Montero', 'ubicacion' => 'Heredia, Barva',   'detalle' => '2× Tabla de Madera Teca Noble',            'total' => 45000, 'recibido' => 'Hoy, 08:15 am',  'imagen' => 'productos/tabla-teca.jpg'],
        ['id' => 8904, 'cliente' => 'Kattia Vargas Jiménez',   'ubicacion' => 'Cartago, Paraíso', 'detalle' => '1× Miel Cruda de Bosque Tropical (500 g)', 'total' => 16200, 'recibido' => 'Ayer, 06:40 pm', 'imagen' => 'productos/miel-cruda.jpg'],
    ];
}

// Fecha de hoy en español (ej. Viernes, 23 de octubre de 2026)
$diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$meses      = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
$fechaHoy   = $diasSemana[(int) date('w')] . ', ' . date('j') . ' de ' . $meses[(int) date('n') - 1] . ' de ' . date('Y');

// Avance hacia la meta del mes (0 a 100)
$avanceMeta = 0;
if ($resumen['meta'] > 0) {
    $avanceMeta = min(100, round($resumen['ventas_mes'] * 100 / $resumen['meta']));
}

require __DIR__ . '/../general/header.php';
?>

<div class="panel">
    <?php require __DIR__ . '/../general/sidebar_vendedor.php'; ?>

    <div class="panel__principal">
        <?php require __DIR__ . '/../general/barra_vendedor.php'; ?>

        <main id="contenido" class="panel__contenido">

            <!-- ===== Saludo ===== -->
            <section class="dashboard__cabecera">
                <div class="encabezado-pagina">
                    <span class="pill"><span class="punto"></span> Tienda activa · <?php echo e($tienda['provincia']); ?></span>
                    <h1 class="titulo-pagina">¡Pura vida, <?php echo e($tienda['nombre']); ?>!</h1>
                    <p class="dashboard__fecha">
                        <?php echo icono('reloj'); ?> <?php echo e($fechaHoy); ?>
                        <span class="separador">•</span> Resumen de actividad comercial
                    </p>
                </div>

                <div class="fila">
                    <a class="btn btn--borde" href="<?php echo e(ruta('producto/detalle')); ?>">
                        <?php echo icono('compartir'); ?> Ver mi tienda pública
                    </a>
                    <a class="btn btn--oscuro" href="<?php echo e(ruta('vendedor/productos/nuevo')); ?>">
                        <?php echo icono('mas'); ?> Nuevo producto
                    </a>
                </div>
            </section>

            <!-- ===== Indicadores ===== -->
            <section class="indicadores" aria-label="Resumen del mes">
                <article class="indicador tarjeta">
                    <div class="indicador__cabecera">
                        <h2>Ventas del mes</h2>
                        <span class="indicador__icono indicador__icono--azul"><?php echo icono('efectivo'); ?></span>
                    </div>
                    <p class="indicador__valor"><?php echo colones($resumen['ventas_mes']); ?></p>
                    <div class="indicador__barra" role="progressbar" aria-valuenow="<?php echo (int) $avanceMeta; ?>" aria-valuemin="0" aria-valuemax="100" aria-label="Avance hacia la meta">
                        <span id="barraMeta" data-avance="<?php echo (int) $avanceMeta; ?>"></span>
                    </div>
                    <div class="indicador__pie">
                        <span class="insignia"><?php echo icono('tendencia'); ?> +<?php echo (int) $resumen['variacion']; ?>% vs mes anterior</span>
                        <span>Meta: <?php echo colones($resumen['meta']); ?></span>
                    </div>
                </article>

                <article class="indicador tarjeta">
                    <div class="indicador__cabecera">
                        <h2>Por confirmar</h2>
                        <span class="indicador__icono indicador__icono--teal"><?php echo icono('reloj'); ?></span>
                    </div>
                    <p class="indicador__valor"><?php echo (int) $resumen['por_confirmar']; ?> <small>pedidos</small></p>
                    <div class="indicador__pie">
                        <span class="indicador__alerta"><span class="punto"></span> Atención requerida hoy</span>
                        <a href="<?php echo e(ruta('vendedor/pedidos')); ?>">Revisar lista <?php echo icono('flecha'); ?></a>
                    </div>
                </article>

                <article class="indicador tarjeta">
                    <div class="indicador__cabecera">
                        <h2>Catálogo activo</h2>
                        <span class="indicador__icono"><?php echo icono('caja'); ?></span>
                    </div>
                    <p class="indicador__valor"><?php echo (int) $resumen['catalogo']; ?> <small>productos</small></p>
                    <div class="indicador__pie">
                        <span><?php echo (int) $resumen['stock_bajo']; ?> piezas con stock bajo</span>
                        <span class="indicador__ok"><?php echo icono('check'); ?> 100% hecho a mano</span>
                    </div>
                </article>
            </section>

            <!-- ===== Distintivo ===== -->
            <section class="distintivo">
                <span class="distintivo__icono"><?php echo icono('hoja'); ?></span>
                <div>
                    <h2>Distintivo Artesanal Verificado de Costa Rica</h2>
                    <p>Tus productos cumplen los estándares de origen local y producción sostenible del sello Ventica Pura Vida.</p>
                </div>
                <span class="pill">Sello vigente <?php echo date('Y'); ?></span>
            </section>

            <!-- ===== Pedidos recientes ===== -->
            <section class="pedidos-recientes">
                <div class="seccion-panel__cabecera">
                    <div>
                        <h2 class="titulo-seccion">Pedidos recientes que requieren acción</h2>
                        <p class="texto-guia">Confirmá la preparación y empaque para coordinar el envío con Correos de Costa Rica.</p>
                    </div>
                    <span class="insignia" id="contadorPendientes"><?php echo count($pedidosRecientes); ?> por procesar</span>
                </div>

                <?php if (count($pedidosRecientes) === 0): ?>
                    <div class="tarjeta vacio">
                        <?php echo icono('check'); ?>
                        <p>No tenés pedidos pendientes. ¡Todo al día!</p>
                    </div>
                <?php else: ?>
                    <ul class="lista-pedidos">
                        <?php foreach ($pedidosRecientes as $pedido): ?>
                            <li class="pedido-fila tarjeta">
                                <img class="pedido-fila__imagen" src="<?php echo e(recurso('img/uploads/' . $pedido['imagen'])); ?>" alt="" width="56" height="56">

                                <div class="pedido-fila__info">
                                    <p class="pedido-fila__meta">
                                        <strong>#VEN-<?php echo (int) $pedido['id']; ?></strong>
                                        <span class="separador">•</span> <?php echo e($pedido['ubicacion']); ?>
                                    </p>
                                    <h3><?php echo e($pedido['cliente']); ?></h3>
                                    <p class="pedido-fila__detalle"><?php echo e($pedido['detalle']); ?></p>
                                </div>

                                <div class="pedido-fila__dato">
                                    <small>Monto total</small>
                                    <strong class="pedido-fila__monto"><?php echo colones($pedido['total']); ?></strong>
                                </div>

                                <div class="pedido-fila__dato">
                                    <small>Recibido</small>
                                    <strong><?php echo e($pedido['recibido']); ?></strong>
                                </div>

                                <div class="pedido-fila__acciones">
                                    <form class="form-confirmar" method="post" action="<?php echo e(ruta('vendedor/pedidos/confirmar')); ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                                        <input type="hidden" name="id_pedido" value="<?php echo (int) $pedido['id']; ?>">
                                        <button class="btn btn--primario btn--sm" type="submit">
                                            <?php echo icono('check'); ?> Confirmar
                                        </button>
                                    </form>
                                    <a class="panel__boton-icono" href="<?php echo e(ruta('vendedor/pedidos') . '?id=' . (int) $pedido['id']); ?>" aria-label="Ver pedido #VEN-<?php echo (int) $pedido['id']; ?>">
                                        <?php echo icono('ojo'); ?>
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

        </main>
    </div>
</div>

<?php require __DIR__ . '/../general/footer.php'; ?>
