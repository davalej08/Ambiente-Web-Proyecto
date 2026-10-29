<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Pantalla: Admin · Emprendimientos Activos (RF-03) (Figma: Admin - Emprendimientos Activos)
 * Ruta sugerida: admin/emprendimientos
 *
 * Lista de emprendimientos verificados con vitrina activa. El administrador
 * busca, filtra por provincia, abre la vitrina pública o suspende la cuenta.
 * Regla de RF-03: los emprendimientos no se eliminan, solo se suspenden.
 *
 * Variables que puede enviar AdminController:
 *   $emprendimientos  arreglo de emprendimientos (ver datos de muestra abajo)
 *   $totalActivos     total de emprendimientos activos en la plataforma
 * Si no llegan, se usan datos de muestra para el Avance 2.
 */

$titulo       = 'Emprendimientos Activos · Administración';
$estilos      = ['admin', 'admin_gestion'];
$scripts      = ['emprendimientos_admin'];
$navbar       = false;
$footer       = false;
$claseBody    = 'body-panel';
$seccionPanel = 'emprendimientos';

if (!isset($emprendimientos)) {
    $emprendimientos = [
        ['id' => 1, 'tienda' => 'Café Don Mincho',            'ubicacion' => 'Tarrazú, San José',       'provincia' => 'San José',   'descripcion' => 'Maestro tostador: Mincho Castro • Tradición de altura',                  'categoria' => 'Comida (Café)', 'productos' => 18, 'ventas' => 485000, 'variacion' => 14, 'imagen' => 'productos/cafe-geisha.jpg'],
        ['id' => 2, 'tienda' => 'Taller Baula Artesanías',    'ubicacion' => 'Sarchí, Alajuela',        'provincia' => 'Alajuela',   'descripcion' => 'Ebanistas: Familia Alfaro • Maderas certificadas y torno',              'categoria' => 'Hogar & Madera', 'productos' => 24, 'ventas' => 380000, 'variacion' => 0,  'imagen' => 'productos/tabla-teca.jpg'],
        ['id' => 3, 'tienda' => 'Cerámica Boruca Taller',     'ubicacion' => 'Buenos Aires, Puntarenas', 'provincia' => 'Puntarenas', 'descripcion' => 'Colectivo Indígena Brunka • Cerámica de barro y pigmentos naturales',  'categoria' => 'Arte',          'productos' => 12, 'ventas' => 210000, 'variacion' => 8,  'imagen' => 'productos/aretes-precolombinos.jpg'],
        ['id' => 4, 'tienda' => 'Amigurumis Pura Vida',       'ubicacion' => 'Heredia Centro',          'provincia' => 'Heredia',    'descripcion' => 'Tejedora: Mariana Quesada • Fauna costarricense en hilo de algodón',   'categoria' => 'Crochet',       'productos' => 35, 'ventas' => 195000, 'variacion' => 3,  'imagen' => 'productos/tote-yute.jpg'],
        ['id' => 5, 'tienda' => 'Selva Sagrada Botánica',     'ubicacion' => 'Monteverde, Puntarenas',  'provincia' => 'Puntarenas', 'descripcion' => 'Alquimista: Elena Solís • Cosmética botánica regenerativa',            'categoria' => 'Belleza',       'productos' => 16, 'ventas' => 290000, 'variacion' => 22, 'imagen' => 'productos/jabon-romero.jpg'],
        ['id' => 6, 'tienda' => 'Cuero & Raíces Guanacaste',  'ubicacion' => 'Liberia, Guanacaste',     'provincia' => 'Guanacaste', 'descripcion' => 'Marroquinero: Esteban Rivas • Talabartería tradicional pampera',       'categoria' => 'Accesorios',    'productos' => 21, 'ventas' => 315000, 'variacion' => 5,  'imagen' => 'productos/chocolate-bribri.jpg'],
    ];
}
if (!isset($totalActivos)) {
    $totalActivos = 320;
}

if (!isset($busquedaInicial)) {
    $busquedaInicial = '';
}
$provincias = ['San José', 'Alajuela', 'Cartago', 'Heredia', 'Guanacaste', 'Puntarenas', 'Limón'];
$totalFilas = count($emprendimientos);

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
                        <span class="ad-pastilla ad-pastilla--verde"><?php echo icono('check'); ?> Vitrinas Verificadas</span>
                        <span class="ad-sobretitulo">Directorio oficial</span>
                    </p>
                    <h1 class="ad-titulo">Emprendimientos Activos</h1>
                    <p class="ad-descripcion">Marcas y artesanos verificados con vitrina activa en el catálogo nacional.</p>
                </div>
                <div class="ad-cabecera__acciones">
                    <div class="ad-cobertura">
                        <span class="ad-cobertura__icono"><?php echo icono('ubicacion'); ?></span>
                        <span>
                            <small>Cobertura país</small>
                            <strong><span id="contadorActivos"><?php echo $totalActivos; ?></span> activos</strong> <em>en 7 provincias</em>
                        </span>
                    </div>
                    <button class="btn btn--oscuro solo-pantalla" type="button" id="botonExportar"><?php echo icono('recibo'); ?> Exportar Reporte</button>
                </div>
            </section>

            <!-- Mensaje de confirmación (lo muestra emprendimientos_admin.js) -->
            <div id="mensajeEmprendimientos" class="mensaje mensaje--exito ad-oculto" role="status">
                <p id="textoMensajeEmprendimientos"></p>
            </div>

            <!-- Buscador y filtro por provincia -->
            <div class="ad-buscador solo-pantalla">
                <?php echo icono('buscar'); ?>
                <label class="oculto-visual" for="buscarEmprendimiento">Buscar emprendimiento</label>
                <input id="buscarEmprendimiento" type="search" value="<?php echo e($busquedaInicial); ?>" placeholder="Buscar por nombre comercial, artesano o provincia...">
                <button class="ad-chip" type="button" id="botonFiltros"><?php echo icono('filtros'); ?> Filtros</button>
            </div>

            <div class="ad-filtros ad-oculto" id="panelFiltros">
                <label class="campo__etiqueta" for="filtroProvincia">Provincia</label>
                <select class="campo__entrada" id="filtroProvincia">
                    <option value="todas">Todas las provincias</option>
                    <?php for ($i = 0; $i < count($provincias); $i++): ?>
                        <option value="<?php echo e($provincias[$i]); ?>"><?php echo e($provincias[$i]); ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Lista de emprendimientos (la arma PHP con un for) -->
            <input type="hidden" id="totalEmprendimientos" value="<?php echo $totalFilas; ?>">

            <ul class="ad-lista">
                <?php for ($i = 0; $i < $totalFilas; $i++): ?>
                    <?php
                    $emprendimiento = $emprendimientos[$i];
                    // Los datos de muestra no traen 'activo': se toman como activos
                    $estaActivo = valor($emprendimiento, 'activo', true);
                    ?>
                    <li class="ad-fila <?php echo $estaActivo ? '' : 'ad-fila--suspendida'; ?>" id="emprendimiento-<?php echo $i; ?>">
                        <div class="ad-fila__foto">
                            <img src="<?php echo e(recurso('img/uploads/' . $emprendimiento['imagen'])); ?>" alt="" width="64" height="64">
                            <span class="ad-fila__sello <?php echo $estaActivo ? '' : 'ad-oculto'; ?>" id="sello-<?php echo $i; ?>"><?php echo icono('check'); ?></span>
                        </div>

                        <div class="ad-fila__info">
                            <h2>
                                <span id="tienda-<?php echo $i; ?>"><?php echo e($emprendimiento['tienda']); ?></span>
                                <span class="ad-chip ad-chip--suave"><?php echo e($emprendimiento['ubicacion']); ?></span>
                                <span class="estado estado--rechazado <?php echo $estaActivo ? 'ad-oculto' : ''; ?>" id="suspendido-<?php echo $i; ?>">Suspendido</span>
                            </h2>
                            <p><?php echo e($emprendimiento['descripcion']); ?></p>
                            <p class="ad-fila__etiquetas">
                                <span class="ad-chip ad-chip--suave"><?php echo e($emprendimiento['categoria']); ?></span>
                                <span><?php echo icono('caja'); ?> <?php echo $emprendimiento['productos']; ?> productos activos</span>
                            </p>
                        </div>

                        <div class="ad-fila__ventas">
                            <small>Ventas promedio</small>
                            <strong><?php echo colones($emprendimiento['ventas']); ?><span>/mes</span></strong>
                            <?php if ($emprendimiento['variacion'] > 0): ?>
                                <em class="ad-sube"><?php echo icono('tendencia'); ?> +<?php echo $emprendimiento['variacion']; ?>% vs mes anterior</em>
                            <?php else: ?>
                                <em>— Estable</em>
                            <?php endif; ?>
                        </div>

                        <div class="ad-fila__acciones solo-pantalla">
                            <a class="ad-boton ad-boton--claro" href="<?php echo e(ruta('producto/detalle') . '?emprendimiento=' . (int) $emprendimiento['id']); ?>"><?php echo icono('ojo'); ?> Vitrina</a>
                            <button class="ad-boton <?php echo $estaActivo ? 'ad-boton--peligro' : 'ad-boton--exito'; ?>" type="button" id="estado-<?php echo $i; ?>"><?php echo $estaActivo ? 'Suspender' : 'Reactivar'; ?></button>
                        </div>

                        <!-- Datos que usa el buscador -->
                        <input type="hidden" id="provincia-<?php echo $i; ?>" value="<?php echo e($emprendimiento['provincia']); ?>">
                        <input type="hidden" id="busqueda-<?php echo $i; ?>" value="<?php echo e($emprendimiento['tienda'] . ' ' . $emprendimiento['descripcion'] . ' ' . $emprendimiento['ubicacion']); ?>">
                        <input type="hidden" id="activo-<?php echo $i; ?>" value="<?php echo $estaActivo ? '1' : '0'; ?>">
                        <input type="hidden" id="id-<?php echo $i; ?>" value="<?php echo (int) $emprendimiento['id']; ?>">
                    </li>
                <?php endfor; ?>
            </ul>

            <p class="ad-vacio ad-oculto" id="sinResultados">No hay emprendimientos que coincidan con la búsqueda.</p>

            <!-- Formulario escondido para suspender o reactivar: emprendimientos_admin.js le pone el id -->
            <form id="formEstadoEmprendimiento" method="post" action="<?php echo e(ruta('admin/emprendimientos/estado')); ?>" hidden>
                <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                <input type="hidden" name="id_emprendimiento" id="idEmprendimientoEstado" value="">
            </form>

            <!-- Pie: cantidad y paginación -->
            <div class="ad-pie">
                <p id="contadorFilas">Mostrando 1-<?php echo $totalFilas; ?> de <?php echo $totalActivos; ?> emprendimientos</p>
                <nav class="ad-paginacion solo-pantalla" aria-label="Paginación">
                    <a href="#" aria-label="Página anterior">‹</a>
                    <a href="#" class="es-actual" aria-current="page">1</a>
                    <a href="#">2</a>
                    <a href="#">3</a>
                    <span>…</span>
                    <a href="#">54</a>
                    <a href="#" aria-label="Página siguiente">›</a>
                </nav>
            </div>

        </main>
    </div>
</div>

<?php require __DIR__ . '/../general/footer.php'; ?>
