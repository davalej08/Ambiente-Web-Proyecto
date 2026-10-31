<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Gestión de pedidos del vendedor (RF-14, RF-15)
 */

$titulo       = 'Pedidos · Portal del emprendedor';
$estilos      = ['vendedor', 'pedidos'];
$scripts      = ['pedidos'];
$navbar       = false;
$footer       = false;
$claseBody    = 'body-panel';
$seccionPanel = 'pedidos';

// Texto, color y grupo (pestaña) de cada estado del pedido
$estadosPedido = [
    'pendiente'  => [
        'texto' => 'Pendiente de despacho',  
        'clase' => 'estado-pedido--teal',   
        'grupo' => 'pendientes'
        ],
    'aprobacion' => [
        'texto' => 'Pendiente de aprobación', 
        'clase' => 'estado-pedido--teal',   
        'grupo' => 'pendientes'
        ],
    'confirmado' => [
        'texto' => 'Confirmado y Pagado',     
        'clase' => 'estado-pedido--oscuro', 
        'grupo' => 'listos'
        ],
    'entregado'  => [
        'texto' => 'Entregado con éxito',     
        'clase' => 'estado-pedido--verde',  
        'grupo' => 'listos'
        ],
];

if (!isset($pedidos)) {
    $pedidos = [

        ['id' => 8491, 
        'cliente' => 'Valeria Jiménez',  
        'ubicacion' => 'Curridabat, San José',  
        'momento' => 'Hoy a las 09:42 am · Entrega estimada hoy',
        'producto' => 'Café Geisha Don Mincho - Tueste Claro', 
        'cantidad' => 'x2 bolsas (340g)', 
        'detalle' => 'Notas de jazmín, miel de abeja de montaña y bergamota · Molido fino para V60',
        'imagen' => 'productos/cafe-geisha.jpg', 
        'total' => 26500, 
        'pago' => 'sinpe', 
        'pago_texto' => 'SINPE Móvil · 8841-****',
        'entrega' => 'gam', 
        'hoy' => true, 
        'estado' => 'pendiente', 
        'nota' => ''],

        ['id' => 8488, 
        'cliente' => 'Esteban Mora',     
        'ubicacion' => 'Heredia Centro, Mercedes', 
        'momento' => 'Hoy a las 08:15 am · En preparación de empaque',
        'producto' => 'Chorreador Artesanal de Cedro + Miel Melipona', 
        'cantidad' => '1 Kit + 1 Miel', 
        'detalle' => 'Grabado conmemorativo "Pura Vida" · Frasco 250g de bosque nuboso Monteverde',
        'imagen' => 'productos/tabla-teca.jpg', 
        'total' => 34200, 
        'pago' => 'tarjeta', 
        'pago_texto' => 'Tarjeta BAC · 4019',
        'entrega' => 'gam', 
        'hoy' => true, 
        'estado' => 'confirmado', 
        'nota' => ''],

        ['id' => 8479, 
        'cliente' => 'Sofía Alfaro',     
        'ubicacion' => 'Barrio Escalante, San José', 
        'momento' => 'Ayer 19:40 pm · Solicitud de empaque especial',
        'producto' => 'Chocolatería Talamanca 72% Cacao Criollo', 
        'cantidad' => 'x3 barras (80g)', 'detalle' => 'Edición limitada con nibs tostados al sol y flor de sal de Salinas de Chomes',
        'imagen' => 'productos/chocolate-bribri.jpg', 
        'total' => 11800, 'pago' => 'sinpe', 
        'pago_texto' => 'SINPE Móvil · 7109-****',
        'entrega' => 'gam', 
        'hoy' => false, 'estado' => 'aprobacion', 
        'nota' => ''],

        ['id' => 8462, 
        'cliente' => 'Carlos Alvarado',  
        'ubicacion' => 'Alajuela, La Garita',   
        'momento' => 'Entregado ayer a las 15:30 pm',
        'producto' => 'Tazas Cerámica Artesanal Volcánica Poás', 
        'cantidad' => 'Set x2 piezas', 
        'detalle' => 'Esmalte mineral turquesa de ceniza volcánica · Hecho en taller de Sarchí',
        'imagen' => 'productos/jabon-romero.jpg', 
        'total' => 18500, 
        'pago' => 'tarjeta', 
        'pago_texto' => 'Tarjeta Promerica · 1102',
        'entrega' => 'correos', 
        'hoy' => false, 
        'estado' => 'entregado', 
        'nota' => 'Notificado al cliente'],

        ['id' => 8455, 
        'cliente' => 'Mariana Solano',   
        'ubicacion' => 'Guachipelín, Escazú',   
        'momento' => 'Entregado ayer a las 11:20 am',
        'producto' => 'Cold Brew Concentrado 750ml + Vainilla Negra', 
        'cantidad' => 'x1 Botella vidrio', 
        'detalle' => 'Extracción lenta en frío por 18 horas · Grano Caturra de Dota',
        'imagen' => 'productos/salsa-maracuya.jpg', 
        'total' => 8900, 
        'pago' => 'sinpe', 
        'pago_texto' => 'SINPE Móvil · 8320-****',
        'entrega' => 'gam', 
        'hoy' => false, 
        'estado' => 'entregado', 
        'nota' => 'Calificado 5.0'],
    ];
}

// Números de las tarjetas de arriba: se calculan con los mismos pedidos de la lista
$totalPedidos   = count($pedidos);
$cantPendientes = 0;
$cantListos     = 0;
$pagadosMovil   = 0;
$facturadoHoy   = 0;

foreach ($pedidos as $pedido) {
    if ($estadosPedido[$pedido['estado']]['grupo'] === 'pendientes') {
        $cantPendientes++;
    } else {
        $cantListos++;
    }
    if ($pedido['pago'] === 'sinpe') {
        $pagadosMovil++;
    }
    if ($pedido['hoy']) {
        $facturadoHoy += $pedido['total'];
    }
}
if (isset($resumen['facturacion_dia'])) {
    $facturadoHoy = $resumen['facturacion_dia'];
}

require __DIR__ . '/../general/header.php';
?>


<!-- CODIGO HTML -->
<div class="panel">
    <?php require __DIR__ . '/../general/sidebar_vendedor.php'; ?>

    <div class="panel__principal">
        <?php require __DIR__ . '/../general/barra_vendedor.php'; ?>

        <main id="contenido" class="panel__contenido">

            <!-- ===== Cabecera: resumen y comprobantes ===== -->
            <section class="pedidos-cabecera">
                <div class="pedidos-resumen tarjeta">
                    <p class="pedidos-resumen__estado">
                        <span class="pill"><span class="punto"></span> En vivo · San José &amp; Provincias</span>
                        <small>Actualizado hace 2 min</small>
                    </p>

                    <div class="pedidos-resumen__titulo">
                        <h1 class="titulo-pagina">Gestión de Pedidos</h1>
                        <span class="insignia"><?php echo (int) $totalPedidos; ?> pedidos recientes</span>
                    </div>

                    <p class="texto-guia">
                        Supervisá el despacho de café de altura, empaques artesanales y entregas directas
                        a hogares costarricenses con verificación SINPE Móvil al instante.
                    </p>

                    <dl class="pedidos-resumen__datos">
                        <div>
                            <dt>Por despachar</dt>
                            <dd id="datoPorDespachar"><?php echo (int) $cantPendientes; ?> <?php echo $cantPendientes === 1 ? 'envío' : 'envíos'; ?></dd>
                        </div>
                        <div>
                            <dt>Facturación del día</dt>
                            <dd><?php echo colones($facturadoHoy); ?></dd>
                        </div>
                        <div>
                            <dt>Tasa efectiva SINPE</dt>
                            <dd class="pedidos-resumen__ok">100% verif.</dd>
                        </div>
                    </dl>
                </div>

                <aside class="pedidos-sinpe" aria-label="Comprobantes automáticos y filtro por estado">
                    <div class="pedidos-sinpe__cabecera">
                        <span class="pedidos-sinpe__icono"><?php echo icono('efectivo'); ?></span>
                        <span class="pedidos-sinpe__etiqueta">SINPE Directo</span>
                    </div>

                    <p class="pedidos-sinpe__sobretitulo">Comprobantes automáticos</p>
                    <h2><?php echo (int) $pagadosMovil; ?> de <?php echo (int) $totalPedidos; ?> órdenes pagadas vía móvil</h2>
                    <p class="pedidos-sinpe__texto">
                        Los pagos realizados con número de teléfono asociado reflejan liquidación inmediata
                        sin comisión bancaria adicional.
                    </p>

                    <div class="pedidos-pestanas" role="group" aria-label="Filtrar pedidos por estado">
                        <button class="pedidos-pestanas__boton" type="button" data-grupo="todos" aria-pressed="true">
                            Todos <span id="conteoTodos" data-conteo="todos">(<?php echo (int) $totalPedidos; ?>)</span>
                        </button>
                        <button class="pedidos-pestanas__boton" type="button" data-grupo="pendientes" aria-pressed="false">
                            Pendientes <span id="conteoPendientes" data-conteo="pendientes">(<?php echo (int) $cantPendientes; ?>)</span>
                        </button>
                        <button class="pedidos-pestanas__boton" type="button" data-grupo="listos" aria-pressed="false">
                            Listos <span id="conteoListos" data-conteo="listos">(<?php echo (int) $cantListos; ?>)</span>
                        </button>
                    </div>
                </aside>
            </section>

            <!-- ===== Buscador y filtros ===== -->
            <section class="pedidos-barra" aria-label="Buscar y filtrar pedidos">
                <div class="pedidos-buscador">
                    <label class="oculto-visual" for="buscadorPedidos">Buscar por cliente, producto o número de pedido</label>
                    <?php echo icono('buscar'); ?>
                    <input id="buscadorPedidos" type="search" placeholder="Buscar por cliente, cédula o café...">
                </div>

                <div class="pedidos-filtros">
                    <span>Filtrar:</span>
                    <button class="chip pedidos-filtros__chip" type="button" data-filtro="hoy" aria-pressed="false"><?php echo icono('reloj'); ?> Recientes</button>
                    <button class="chip pedidos-filtros__chip" type="button" data-filtro="sinpe" aria-pressed="false"><?php echo icono('celular'); ?> SINPE Móvil</button>
                    <button class="chip pedidos-filtros__chip" type="button" data-filtro="gam" aria-pressed="false"><?php echo icono('ubicacion'); ?> GAM Express</button>
                </div>
            </section>

            <!-- ===== Lista de pedidos ===== -->
            <ul class="lista-pedidos" id="listaPedidos">
                <?php foreach ($pedidos as $pedido): ?>
                    <?php
                    $estado = $estadosPedido[$pedido['estado']];

                    // Iniciales para el círculo del cliente. Ej: "Valeria Jiménez" -> "VJ"
                    $partesNombre = explode(' ', trim($pedido['cliente']));
                    $iniciales    = mb_strtoupper(mb_substr($partesNombre[0], 0, 1));
                    if (count($partesNombre) > 1) {
                        $iniciales .= mb_strtoupper(mb_substr($partesNombre[1], 0, 1));
                    }

                    // Texto en minúscula para el buscador (sin código PHP en el JS)
                    $textoBuscable = mb_strtolower($pedido['cliente'] . ' ' . $pedido['producto'] . ' ' . $pedido['ubicacion'] . ' cr-' . $pedido['id']);
                    ?>
                    <li class="pedido tarjeta" id="pedido-<?php echo (int) $pedido['id']; ?>"
                        data-estado="<?php echo e($pedido['estado']); ?>"
                        data-grupo="<?php echo e($estado['grupo']); ?>"
                        data-pago="<?php echo e($pedido['pago']); ?>"
                        data-entrega="<?php echo e($pedido['entrega']); ?>"
                        data-hoy="<?php echo $pedido['hoy'] ? '1' : '0'; ?>"
                        data-buscar="<?php echo e($textoBuscable); ?>">

                        <!-- Cliente -->
                        <div class="pedido__cliente">
                            <span class="pedido__avatar" aria-hidden="true"><?php echo e($iniciales); ?></span>
                            <div>
                                <div class="pedido__nombre">
                                    <h3><?php echo e($pedido['cliente']); ?></h3>
                                    <span class="pedido__codigo">#CR-<?php echo (int) $pedido['id']; ?></span>
                                </div>
                                <p class="pedido__ubicacion"><?php echo icono('ubicacion'); ?> <?php echo e($pedido['ubicacion']); ?></p>
                                <p class="pedido__momento"><?php echo e($pedido['momento']); ?></p>
                            </div>
                        </div>

                        <!-- Producto -->
                        <div class="pedido__producto">
                            <img src="<?php echo e(recurso('img/uploads/' . $pedido['imagen'])); ?>" alt="" width="48" height="48">
                            <div>
                                <p class="pedido__producto-nombre">
                                    <strong><?php echo e($pedido['producto']); ?></strong>
                                    <span class="pedido__cantidad"><?php echo e($pedido['cantidad']); ?></span>
                                </p>
                                <p class="pedido__detalle"><?php echo e($pedido['detalle']); ?></p>
                            </div>
                        </div>

                        <!-- Monto y forma de pago -->
                        <div class="pedido__monto">
                            <strong><?php echo colones($pedido['total']); ?></strong>
                            <span class="pedido__pago">
                                <?php echo icono($pedido['pago'] === 'sinpe' ? 'celular' : 'tarjeta'); ?>
                                <?php echo e($pedido['pago_texto']); ?>
                            </span>
                        </div>

                        <!-- Estado y acciones -->
                        <div class="pedido__estado-acciones">
                            <span class="estado-pedido <?php echo e($estado['clase']); ?>"><span class="punto"></span> <?php echo e($estado['texto']); ?></span>

                            <div class="pedido__acciones">
                                <?php if ($pedido['estado'] === 'pendiente' || $pedido['estado'] === 'aprobacion'): ?>
                                    <form class="form-pedido" method="post" action="<?php echo e(ruta('vendedor/pedidos/confirmar')); ?>" data-accion="confirmar">
                                        <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                                        <input type="hidden" name="id_pedido" value="<?php echo (int) $pedido['id']; ?>">
                                        <button class="btn btn--oscuro btn--sm" type="submit"><?php echo icono('check'); ?> <span>Confirmar pedido</span></button>
                                    </form>
                                <?php elseif ($pedido['estado'] === 'confirmado'): ?>
                                    <form class="form-pedido" method="post" action="<?php echo e(ruta('vendedor/pedidos/entregar')); ?>" data-accion="entregar">
                                        <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                                        <input type="hidden" name="id_pedido" value="<?php echo (int) $pedido['id']; ?>">
                                        <button class="btn btn--primario btn--sm" type="submit"><?php echo icono('camion'); ?> <span>Marcar como entregado</span></button>
                                    </form>
                                <?php else: ?>
                                    <p class="pedido__nota"><?php echo icono('check'); ?> <?php echo e($pedido['nota']); ?></p>
                                <?php endif; ?>

                                <?php if ($pedido['estado'] !== 'entregado'): ?>
                                    <a class="panel__boton-icono" href="<?php echo e(ruta('vendedor/pedidos/imprimir') . '?id=' . (int) $pedido['id']); ?>" aria-label="Imprimir comprobante del pedido #CR-<?php echo (int) $pedido['id']; ?>">
                                        <?php echo icono($pedido['estado'] === 'aprobacion' ? 'mensaje' : 'recibo'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Pedido que viene marcado desde el inicio del portal (vendedor/pedidos?id=...) -->
            <input type="hidden" id="pedidoResaltado" value="<?php echo (int) valor($_GET, 'id', 0); ?>">

            <div class="tarjeta vacio" id="sinResultados" hidden>
                <?php echo icono('buscar'); ?>
                <p>No hay pedidos que coincidan con la búsqueda o los filtros.</p>
            </div>

        </main>
    </div>
</div>

<?php require __DIR__ . '/../general/footer.php'; ?>