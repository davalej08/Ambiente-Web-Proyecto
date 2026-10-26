<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Vista de Reportes de ventas del vendedor (RF-13)
 */


$titulo       = 'Reportes · Portal del emprendedor';
$estilos      = ['vendedor', 'reportes'];
$scripts      = [];
$navbar       = false;
$footer       = false;
$claseBody    = 'body-panel';
$seccionPanel = 'reportes';
$pillPanel    = 'Emprendedor Local Verificado';

// Períodos que se pueden escoger (la llave viaja en la URL: ?periodo=mes)
$periodos = [
    'semana' => 'Esta semana',
    '30dias' => 'Últimos 30 días',
    'mes'    => 'Este mes',
    'anio'   => 'Año ' . date('Y'),
];

if (!isset($periodo)) {
    $periodo = isset($_GET['periodo']) ? $_GET['periodo'] : '30dias';
}
if (!isset($periodos[$periodo])) {
    $periodo = '30dias';
}

// Datos de muestra: uno por período
$muestras = [
    'semana' => [
        'ventas' => 112500, 'variacion' => 8, 'pedidos' => 7, 'entregados' => 100, 'producto' => 'Café Geisha Don Mincho', 'unidades' => 5,
        'subtitulo' => 'Evolución de ventas en colones (CRC) durante los últimos 7 días',
        'puntos' => [
            ['etiqueta' => 'Lun', 'rango' => 'Lunes',     'monto' => 12000],
            ['etiqueta' => 'Mar', 'rango' => 'Martes',    'monto' => 18500],
            ['etiqueta' => 'Mié', 'rango' => 'Miércoles', 'monto' => 9000],
            ['etiqueta' => 'Jue', 'rango' => 'Jueves',    'monto' => 22000],
            ['etiqueta' => 'Vie', 'rango' => 'Viernes',   'monto' => 15500],
            ['etiqueta' => 'Sáb', 'rango' => 'Sábado',    'monto' => 20000],
            ['etiqueta' => 'Dom', 'rango' => 'Domingo',   'monto' => 15500],
        ],
    ],
    '30dias' => [
        'ventas' => 485000, 'variacion' => 14, 'pedidos' => 28, 'entregados' => 92, 'producto' => 'Café Geisha Don Mincho', 'unidades' => 14,
        'subtitulo' => 'Evolución de ventas en colones (CRC) durante las últimas 4 semanas',
        'puntos' => [
            ['etiqueta' => 'Semana 1', 'rango' => 'Sem 1 (1 - 7 Oct)',   'monto' => 95000],
            ['etiqueta' => 'Semana 2', 'rango' => 'Sem 2 (8 - 14 Oct)',  'monto' => 120000],
            ['etiqueta' => 'Semana 3', 'rango' => 'Sem 3 (15 - 21 Oct)', 'monto' => 110000],
            ['etiqueta' => 'Semana 4', 'rango' => 'Sem 4 (22 - 30 Oct)', 'monto' => 160000],
        ],
    ],
    'mes' => [
        'ventas' => 372000, 'variacion' => 9, 'pedidos' => 21, 'entregados' => 90, 'producto' => 'Miel Cruda de Bosque Tropical', 'unidades' => 11,
        'subtitulo' => 'Evolución de ventas en colones (CRC) durante las semanas del mes',
        'puntos' => [
            ['etiqueta' => 'Semana 1', 'rango' => 'Sem 1 (1 - 7 Oct)',   'monto' => 80000],
            ['etiqueta' => 'Semana 2', 'rango' => 'Sem 2 (8 - 14 Oct)',  'monto' => 92000],
            ['etiqueta' => 'Semana 3', 'rango' => 'Sem 3 (15 - 21 Oct)', 'monto' => 88000],
            ['etiqueta' => 'Semana 4', 'rango' => 'Sem 4 (22 - 31 Oct)', 'monto' => 112000],
        ],
    ],
    'anio' => [
        'ventas' => 3700000, 'variacion' => 21, 'pedidos' => 262, 'entregados' => 94, 'producto' => 'Café Geisha Don Mincho', 'unidades' => 118,
        'subtitulo' => 'Evolución de ventas en colones (CRC) mes a mes durante el año',
        'puntos' => [
            ['etiqueta' => 'Ene', 'rango' => 'Enero',      'monto' => 250000],
            ['etiqueta' => 'Feb', 'rango' => 'Febrero',    'monto' => 270000],
            ['etiqueta' => 'Mar', 'rango' => 'Marzo',      'monto' => 290000],
            ['etiqueta' => 'Abr', 'rango' => 'Abril',      'monto' => 330000],
            ['etiqueta' => 'May', 'rango' => 'Mayo',       'monto' => 310000],
            ['etiqueta' => 'Jun', 'rango' => 'Junio',      'monto' => 380000],
            ['etiqueta' => 'Jul', 'rango' => 'Julio',      'monto' => 420000],
            ['etiqueta' => 'Ago', 'rango' => 'Agosto',     'monto' => 450000],
            ['etiqueta' => 'Sep', 'rango' => 'Septiembre', 'monto' => 485000],
            ['etiqueta' => 'Oct', 'rango' => 'Octubre',    'monto' => 515000],
        ],
    ],
];

if (!isset($reporte)) {
    $reporte = $muestras[$periodo];
}
$puntos = $reporte['puntos'];

// ---------- Datos para dibujar la gráfica (SVG) ----------
// Área de dibujo dentro del SVG: ancho 640, alto 280
$izq = 64;
$der = 624;
$arriba = 24;
$abajo = 232;
$anchoGrafica = $der - $izq;
$altoGrafica = $abajo - $arriba;

// Escala vertical: 3 intervalos redondeados a miles. Ej: máximo 160000 -> 0, 60k, 120k, 180k
$maximo = 0;
foreach ($puntos as $punto) {
    if ($punto['monto'] > $maximo) {
        $maximo = $punto['monto'];
    }
}
$paso = ceil($maximo / 3 / 10000) * 10000;
$tope = $paso * 3;

// Coordenadas de cada punto
$coordenadas = [];
$cantidad = count($puntos);
for ($i = 0; $i < $cantidad; $i++) {
    $coordenadas[] = [
        'x' => $izq + ($i + 0.5) * $anchoGrafica / $cantidad,
        'y' => $abajo - $puntos[$i]['monto'] / $tope * $altoGrafica,
    ];
}

// Línea suave que pasa por todos los puntos (curva de Catmull-Rom convertida a Bézier)
$linea = 'M' . round($coordenadas[0]['x'], 1) . ' ' . round($coordenadas[0]['y'], 1);
for ($i = 0; $i < $cantidad - 1; $i++) {
    $anterior  = $coordenadas[max($i - 1, 0)];
    $actual    = $coordenadas[$i];
    $siguiente = $coordenadas[$i + 1];
    $despues   = $coordenadas[min($i + 2, $cantidad - 1)];

    $c1x = $actual['x'] + ($siguiente['x'] - $anterior['x']) / 6;
    $c1y = $actual['y'] + ($siguiente['y'] - $anterior['y']) / 6;
    $c2x = $siguiente['x'] - ($despues['x'] - $actual['x']) / 6;
    $c2y = $siguiente['y'] - ($despues['y'] - $actual['y']) / 6;

    // Que la curva nunca se salga del área de dibujo
    $c1y = min(max($c1y, $arriba), $abajo);
    $c2y = min(max($c2y, $arriba), $abajo);

    $linea .= ' C' . round($c1x, 1) . ' ' . round($c1y, 1) . ' ' . round($c2x, 1) . ' ' . round($c2y, 1)
        . ' ' . round($siguiente['x'], 1) . ' ' . round($siguiente['y'], 1);
}

// El área de color es la misma línea cerrada hacia abajo
$ultimo = $coordenadas[$cantidad - 1];
$area = $linea . ' L' . round($ultimo['x'], 1) . ' ' . $abajo . ' L' . round($coordenadas[0]['x'], 1) . ' ' . $abajo . ' Z';

// Texto del eje vertical. Ej: 60000 -> ₡60k
if (!function_exists('etiquetaEje')) {
    function etiquetaEje($monto)
    {
        if ($monto == 0) {
            return '₡0';
        }
        return '₡' . round($monto / 1000) . 'k';
    }
}

require __DIR__ . '/../general/header.php';
?>

<div class="panel">
    <?php require __DIR__ . '/../general/sidebar_vendedor.php'; ?>

    <div class="panel__principal">
        <?php require __DIR__ . '/../general/barra_vendedor.php'; ?>

        <main id="contenido" class="panel__contenido">

            <!-- ===== Título y selector de período ===== -->
            <section class="reportes-cabecera">
                <div class="encabezado-pagina">
                    <p class="sobretitulo reportes-cabecera__sobretitulo"><?php echo icono('grafico'); ?> Métricas de rendimiento</p>
                    <h1 class="titulo-pagina">Reportes de Ventas</h1>
                    <p class="texto-guia">Resumen y rendimiento de tu vitrina artesanal</p>
                </div>

                <div class="reportes-cabecera__controles">
                    <nav class="segmentado" aria-label="Período del reporte">
                        <?php foreach ($periodos as $clave => $texto): ?>
                            <a class="segmentado__opcion <?php echo $periodo === $clave ? 'es-activo' : ''; ?>"
                               href="<?php echo e(ruta('vendedor/reportes') . '?periodo=' . $clave); ?>"
                               <?php echo $periodo === $clave ? 'aria-current="true"' : ''; ?>>
                                <?php echo e($texto); ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>

                    <a class="btn btn--borde btn--sm" href="<?php echo e(ruta('vendedor/reportes/pdf') . '?periodo=' . $periodo); ?>">
                        <?php echo icono('recibo'); ?> Descargar PDF
                    </a>
                </div>
            </section>

            <!-- ===== Indicadores ===== -->
            <section class="indicadores" aria-label="Resumen del período">
                <article class="kpi kpi--azul tarjeta">
                    <div class="kpi__cabecera">
                        <h2>Ventas totales</h2>
                        <span class="indicador__icono"><?php echo icono('efectivo'); ?></span>
                    </div>
                    <p class="kpi__valor"><?php echo colones($reporte['ventas']); ?></p>
                    <p class="kpi__pie">
                        <span class="insignia"><?php echo icono('tendencia'); ?> +<?php echo (int) $reporte['variacion']; ?>%</span>
                        vs período anterior
                    </p>
                </article>

                <article class="kpi kpi--crema tarjeta">
                    <div class="kpi__cabecera">
                        <h2>Número de pedidos</h2>
                        <span class="indicador__icono"><?php echo icono('bolsa'); ?></span>
                    </div>
                    <p class="kpi__valor"><?php echo (int) $reporte['pedidos']; ?> <small>pedidos</small></p>
                    <p class="kpi__pie">
                        <span class="insignia"><?php echo icono('check'); ?> <?php echo (int) $reporte['entregados']; ?>% entregados vía SINPE</span>
                    </p>
                </article>

                <article class="kpi kpi--verde tarjeta">
                    <div class="kpi__cabecera">
                        <h2>Producto más vendido</h2>
                        <span class="indicador__icono"><?php echo icono('cafe'); ?></span>
                    </div>
                    <p class="kpi__producto"><?php echo e($reporte['producto']); ?></p>
                    <p class="kpi__pie">
                        <span class="insignia"><?php echo icono('estrella'); ?> <?php echo (int) $reporte['unidades']; ?> unidades vendidas</span>
                        este período
                    </p>
                </article>
            </section>

            <!-- ===== Gráfica ===== -->
            <section class="grafica tarjeta" aria-labelledby="tituloGrafica">
                <div class="grafica__cabecera">
                    <div>
                        <h2 class="titulo-seccion" id="tituloGrafica">Comportamiento de ingresos</h2>
                        <p class="texto-guia"><?php echo e($reporte['subtitulo']); ?></p>
                    </div>
                    <p class="grafica__leyenda">
                        <span class="punto"></span> Venta bruta
                        <strong>Total: <?php echo colones($reporte['ventas']); ?></strong>
                    </p>
                </div>

                <svg class="grafica__svg" viewBox="0 0 640 280" role="img" aria-labelledby="tituloSvg descSvg">
                    <title id="tituloSvg">Ingresos del período: <?php echo e($periodos[$periodo]); ?></title>
                    <desc id="descSvg">
                        <?php foreach ($puntos as $punto): ?>
                            <?php echo e($punto['rango'] . ': ' . colones($punto['monto']) . '. '); ?>
                        <?php endforeach; ?>
                    </desc>

                    <defs>
                        <!-- Los colores de los degradados están en reportes.css (variables de la paleta) -->
                        <linearGradient id="degradadoArea" x1="0" y1="0" x2="0" y2="1">
                            <stop class="grafica__parada-area-1" offset="0"/>
                            <stop class="grafica__parada-area-2" offset="1"/>
                        </linearGradient>
                        <linearGradient id="degradadoLinea" x1="0" y1="0" x2="1" y2="0">
                            <stop class="grafica__parada-linea-1" offset="0"/>
                            <stop class="grafica__parada-linea-2" offset="1"/>
                        </linearGradient>
                    </defs>

                    <!-- Líneas horizontales y texto del eje vertical -->
                    <?php for ($i = 0; $i <= 3; $i++): ?>
                        <?php $yEje = $abajo - $i * $altoGrafica / 3; ?>
                        <line class="grafica__guia" x1="<?php echo $izq; ?>" x2="<?php echo $der; ?>" y1="<?php echo round($yEje, 1); ?>" y2="<?php echo round($yEje, 1); ?>"/>
                        <text class="grafica__eje" x="<?php echo $izq - 10; ?>" y="<?php echo round($yEje + 4, 1); ?>" text-anchor="end"><?php echo e(etiquetaEje($i * $paso)); ?></text>
                    <?php endfor; ?>

                    <!-- Área, línea y puntos -->
                    <path class="grafica__area" d="<?php echo e($area); ?>"/>
                    <path class="grafica__linea" d="<?php echo e($linea); ?>"/>

                    <?php for ($i = 0; $i < $cantidad; $i++): ?>
                        <circle class="grafica__punto" cx="<?php echo round($coordenadas[$i]['x'], 1); ?>" cy="<?php echo round($coordenadas[$i]['y'], 1); ?>" r="5">
                            <title><?php echo e($puntos[$i]['rango'] . ': ' . colones($puntos[$i]['monto'])); ?></title>
                        </circle>
                        <text class="grafica__eje grafica__eje--x" x="<?php echo round($coordenadas[$i]['x'], 1); ?>" y="<?php echo $abajo + 26; ?>" text-anchor="middle"><?php echo e($puntos[$i]['etiqueta']); ?></text>
                    <?php endfor; ?>
                </svg>

                <!-- Monto de cada punto de la gráfica -->
                <ul class="grafica__resumen">
                    <?php for ($i = 0; $i < $cantidad; $i++): ?>
                        <li class="<?php echo $i === $cantidad - 1 ? 'es-actual' : ''; ?>">
                            <small><?php echo e($puntos[$i]['rango']); ?></small>
                            <strong><?php echo colones($puntos[$i]['monto']); ?></strong>
                        </li>
                    <?php endfor; ?>
                </ul>
            </section>

        </main>
    </div>
</div>

<?php require __DIR__ . '/../general/footer.php'; ?>
