<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Admin · Reportes Generales 
 * Ruta: admin/reportes
 
 * Brinda las métricas clave del período elegido (7, 30 o 90 días): ventas totales, emprendimientos activos y pedidos. 
 * Igual muestra las barras de categorías más vendidas, impacto local y cobertura territorial.
 * "Exportar resumen" usa la impresión del navegador (Guardar como PDF).
 */

$titulo       = 'Reportes Generales · Administración';
$estilos      = ['admin', 'admin_gestion'];
$scripts      = ['reportes_admin'];
$navbar       = false;
$footer       = false;
$claseBody    = 'body-panel';
$seccionPanel = 'reportes';

// Período elegido en el select (llega por GET). Si no llega o no es válido: 30 días.
$periodo = isset($_GET['periodo']) ? $_GET['periodo'] : '30';

// Los datos de muestra son de 30 días, para los otros períodos se multiplican.
// También cambian algunas palabras de las tarjetas según el período.
switch ($periodo) {
    case '7':
        $factor           = 0.23;
        $textoPeriodo     = 'últimos 7 días';
        $tituloPedidos    = 'Pedidos de la semana';
        $periodoAnterior  = 'vs semana anterior';
        break;
    case '90':
        $factor           = 2.85;
        $textoPeriodo     = 'últimos 90 días';
        $tituloPedidos    = 'Pedidos del trimestre';
        $periodoAnterior  = 'vs trimestre anterior';
        break;
    default:
        $periodo          = '30';
        $factor           = 1;
        $textoPeriodo     = 'últimos 30 días';
        $tituloPedidos    = 'Pedidos del mes';
        $periodoAnterior  = 'vs mes anterior';
        break;
}

if (!isset($indicadores)) {
    $indicadores = [
        'ventas'          => round(28450000 * $factor / 10000) * 10000,
        'variacion'       => 14,
        'emprendimientos' => 320,
        'pedidos'         => round(1420 * $factor),
    ];
}

if (!isset($ventasCategoria)) {
    $ventasCategoria = [
        ['categoria' => 'Comida & Café de Especialidad',  'monto' => 9670000],
        ['categoria' => 'Hogar & Maderas Sostenibles',    'monto' => 6250000],
        ['categoria' => 'Accesorios & Joyería Botánica',  'monto' => 5120000],
        ['categoria' => 'Ropa & Textiles Éticos',         'monto' => 3980000],
        ['categoria' => 'Cerámica & Arte',                'monto' => 2270000],
        ['categoria' => 'Cosmética & Belleza Natural',    'monto' => 1160000],
    ];
    for ($i = 0; $i < count($ventasCategoria); $i++) {
        $ventasCategoria[$i]['monto'] = round($ventasCategoria[$i]['monto'] * $factor / 10000) * 10000;
    }
}

if (!isset($impacto)) {
    // Porcentaje de transacciones que se liquidan por SINPE en menos de 24 horas
    $impacto = ['sinpe' => 91];
}

if (!isset($cobertura)) {
    $cobertura = [
        'lideres'     => 'San José, Alajuela y Heredia',
        'crecimiento' => 'Guanacaste y Puntarenas',
        'porcentaje'  => 23,
    ];
}

// Total vendido por categorías, para sacar el porcentaje de cada una
$totalCategorias = count($ventasCategoria);
$sumaCategorias  = 0;
for ($i = 0; $i < $totalCategorias; $i++) {
    $sumaCategorias = $sumaCategorias + $ventasCategoria[$i]['monto'];
}

date_default_timezone_set('America/Costa_Rica');

require __DIR__ . '/../general/header.php';
?>

<div class="panel">
    <?php require __DIR__ . '/../general/sidebar_admin.php'; ?>

    <div class="panel__principal">
        <?php require __DIR__ . '/../general/barra_admin.php'; ?>

        <main id="contenido" class="panel__contenido">

            <!-- Encabezado -->
            <section class="ad-cabecera">
                <div>
                    <p class="ad-etiquetas">
                        <span class="ad-pastilla ad-pastilla--lima">Métricas clave</span>
                        <span class="ad-sobretitulo ad-sobretitulo--suave">• Actualizado hoy, <?php echo date('h:i A'); ?></span>
                    </p>
                    <h1 class="ad-titulo">Reportes Generales de la Plataforma</h1>
                    <p class="ad-descripcion">Rendimiento y volumen transaccional de emprendimientos en Costa Rica</p>
                </div>

                <div class="ad-cabecera__acciones solo-pantalla">
                    <!-- Al cambiar el período se envía el formulario y PHP recalcula -->
                    <form class="ad-periodo" method="get" action="<?php echo e(ruta('admin/reportes')); ?>" id="formPeriodo">
                        <?php echo icono('reloj'); ?>
                        <label class="oculto-visual" for="periodo">Período</label>
                        <select id="periodo" name="periodo">
                            <option value="7" <?php echo $periodo === '7' ? 'selected' : ''; ?>>Últimos 7 días</option>
                            <option value="30" <?php echo $periodo === '30' ? 'selected' : ''; ?>>Últimos 30 días</option>
                            <option value="90" <?php echo $periodo === '90' ? 'selected' : ''; ?>>Últimos 90 días</option>
                        </select>
                    </form>
                    <button class="btn btn--oscuro" type="button" id="botonExportar"><?php echo icono('recibo'); ?> Exportar resumen</button>
                </div>
            </section>

            <p class="ad-solo-impresion">Período del reporte: <?php echo e($textoPeriodo); ?> · Generado el <?php echo date('d/m/Y'); ?></p>

            <!-- Indicadores -->
            <ul class="ad-indicadores ad-indicadores--tres">
                <li class="ad-kpi">
                    <span class="ad-kpi__cabecera">
                        <small>Ventas totales en la plataforma</small>
                        <span class="ad-indicador__icono ad-indicador__icono--gris"><?php echo icono('efectivo'); ?></span>
                    </span>
                    <strong><?php echo colones($indicadores['ventas']); ?></strong>
                    <p><span class="ad-sube ad-sube--chip"><?php echo icono('tendencia'); ?> +<?php echo $indicadores['variacion']; ?>%</span> <?php echo e($periodoAnterior); ?></p>
                </li>
                <li class="ad-kpi">
                    <span class="ad-kpi__cabecera">
                        <small>Emprendimientos activos</small>
                        <span class="ad-indicador__icono ad-indicador__icono--teal"><?php echo icono('tienda'); ?></span>
                    </span>
                    <strong><?php echo $indicadores['emprendimientos']; ?></strong>
                    <p><?php echo icono('verificado'); ?> marcas locales verificadas en las 7 provincias</p>
                </li>
                <li class="ad-kpi">
                    <span class="ad-kpi__cabecera">
                        <small><?php echo e($tituloPedidos); ?></small>
                        <span class="ad-indicador__icono ad-indicador__icono--verde"><?php echo icono('camion'); ?></span>
                    </span>
                    <strong><?php echo number_format($indicadores['pedidos'], 0, '.', ','); ?></strong>
                    <p><?php echo icono('celular'); ?> despachos coordinados (SINPE Móvil &amp; envíos)</p>
                </li>
            </ul>

            <!-- Categorías más vendidas: el ancho de cada barra lo pone reportes_admin.js -->
            <section class="tarjeta ad-tarjeta">
                <div class="ad-tarjeta__cabecera">
                    <div>
                        <h2>Categorías Más Vendidas</h2>
                        <p>Distribución del volumen total generado por artesanos y productores locales</p>
                    </div>
                    <p class="ad-leyenda">
                        <span><span class="ad-leyenda__color"></span> Mayor volumen</span>
                        <span><span class="ad-leyenda__color ad-leyenda__color--claro"></span> Nuevos nichos</span>
                    </p>
                </div>

                <input type="hidden" id="totalBarras" value="<?php echo $totalCategorias; ?>">

                <ul class="ad-barras">
                    <?php for ($i = 0; $i < $totalCategorias; $i++): ?>
                        <?php
                        $porcentaje = 0;
                        if ($sumaCategorias > 0) {
                            $porcentaje = round($ventasCategoria[$i]['monto'] * 100 / $sumaCategorias);
                        }
                        // Cada barra tiene su color: ad-barra--1, ad-barra--2, ... (ver admin_gestion.css)
                        $color = ($i % 6) + 1;
                        ?>
                        <li class="ad-barra ad-barra--<?php echo $color; ?>">
                            <div class="ad-barra__texto">
                                <span><span class="ad-barra__punto"></span> <?php echo e($ventasCategoria[$i]['categoria']); ?></span>
                                <span class="ad-barra__monto"><?php echo colones($ventasCategoria[$i]['monto']); ?> <strong><?php echo $porcentaje; ?>%</strong></span>
                            </div>
                            <div class="ad-barra__fondo">
                                <span class="ad-barra__relleno" id="barra-<?php echo $i; ?>"></span>
                            </div>
                            <input type="hidden" id="porcentaje-<?php echo $i; ?>" value="<?php echo $porcentaje; ?>">
                        </li>
                    <?php endfor; ?>
                </ul>
            </section>

            <!-- Impacto y cobertura -->
            <div class="ad-dos-columnas">
                <section class="tarjeta ad-tarjeta ad-tarjeta--fila">
                    <span class="ad-indicador__icono ad-indicador__icono--lima"><?php echo icono('hoja'); ?></span>
                    <div>
                        <h2>Impacto Local Positivo</h2>
                        <p>El <?php echo $impacto['sinpe']; ?>% de las transacciones son liquidadas directamente a cuentas de artesanos vía SINPE en menos de 24 horas.</p>
                    </div>
                </section>

                <section class="tarjeta ad-tarjeta ad-tarjeta--fila">
                    <span class="ad-indicador__icono ad-indicador__icono--celeste"><?php echo icono('ubicacion'); ?></span>
                    <div>
                        <h2>Cobertura Territorial</h2>
                        <p><?php echo e($cobertura['lideres']); ?> lideran la oferta; <?php echo e($cobertura['crecimiento']); ?> muestran el crecimiento intermensual más acelerado (+<?php echo $cobertura['porcentaje']; ?>%).</p>
                    </div>
                </section>
            </div>

        </main>
    </div>
</div>

<?php require __DIR__ . '/../general/footer.php'; ?>
