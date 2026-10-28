<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Pantalla: Admin · Emprendimientos Pendientes (RF-03) (Figma: Admin - Emprendimientos Pendientes)
 * Ruta: admin/solicitudes
 *
 * Solicitudes de registro de tiendas en espera de validación. El administrador
 * filtra por pestañas (todas, nuevas o con observaciones), abre el detalle de
 * cada solicitud y descarga el reporte en CSV.
 *
 * Campos de cada solicitud:
 *   tipo     'nueva' o 'observacion' (lo usan las pestañas)
 *   estado   'ok' (documentos completos), 'alerta' (falta un documento)
 *   semaforo color del punto sobre la foto: 'verde', 'teal' o 'rojo'
 */

$titulo       = 'Emprendimientos Pendientes · Administración';
$estilos      = ['admin', 'admin_gestion', 'admin_solicitudes'];
$scripts      = ['solicitudes_admin'];
$navbar       = false;
$footer       = false;
$claseBody    = 'body-panel';
$seccionPanel = 'solicitudes';

if (!isset($solicitudes)) {
    $solicitudes = [
        ['id' => 1, 'tienda' => 'Miel Dorada de Orosi',       'insignia' => 'Nueva solicitud',      'tipo' => 'nueva',       'expediente' => '#CR-2025-081', 'categoria' => 'Comida & Botánica Artesanal', 'icono' => 'hoja',    'ubicacion' => 'Paraíso, Cartago',       'provincia' => 'Cartago',    'fecha' => '14 Mar 2025 (Hace 2h)', 'nota' => 'Cédula y Permisos OK',          'estado' => 'ok',     'semaforo' => 'verde', 'sello' => true,  'imagen' => 'productos/miel-cruda.jpg'],
        ['id' => 2, 'tienda' => 'Cerámica Boruca Ancestral',  'insignia' => 'Sello Cultural',       'tipo' => 'nueva',       'expediente' => '#CR-2025-078', 'categoria' => 'Arte & Cerámica',             'icono' => 'trato',   'ubicacion' => 'Buenos Aires, Puntarenas', 'provincia' => 'Puntarenas', 'fecha' => '13 Mar 2025',            'nota' => 'Certificado Indígena adjunto', 'estado' => 'ok',     'semaforo' => 'teal',  'sello' => true,  'imagen' => 'productos/aretes-precolombinos.jpg'],
        ['id' => 3, 'tienda' => 'Quesos y Lácteos Monteverde', 'insignia' => 'Observación Técnica', 'tipo' => 'observacion', 'expediente' => '#CR-2025-072', 'categoria' => 'Comida Artesanal',           'icono' => 'caja',    'ubicacion' => 'Monteverde, Puntarenas',  'provincia' => 'Puntarenas', 'fecha' => '12 Mar 2025',            'nota' => 'Requiere Carné SENASA',       'estado' => 'alerta', 'semaforo' => 'teal',  'sello' => false, 'imagen' => 'productos/salsa-maracuya.jpg'],
        ['id' => 4, 'tienda' => 'Hilados y Telas de Sarchí',   'insignia' => 'Nueva solicitud',      'tipo' => 'nueva',       'expediente' => '#CR-2025-069', 'categoria' => 'Ropa & Textiles',            'icono' => 'camisa',  'ubicacion' => 'Sarchí, Alajuela',        'provincia' => 'Alajuela',   'fecha' => '11 Mar 2025',            'nota' => 'Taller Artesanal Verificado',  'estado' => 'ok',     'semaforo' => 'verde', 'sello' => true,  'imagen' => 'productos/tote-yute.jpg'],
        ['id' => 5, 'tienda' => 'Botánica de la Península',    'insignia' => 'Nueva solicitud',      'tipo' => 'nueva',       'expediente' => '#CR-2025-064', 'categoria' => 'Belleza Natural',            'icono' => 'hoja',    'ubicacion' => 'Cóbano, Puntarenas',      'provincia' => 'Puntarenas', 'fecha' => '09 Mar 2025',            'nota' => 'Registro Sanitario al día',    'estado' => 'ok',     'semaforo' => 'verde', 'sello' => true,  'imagen' => 'productos/jabon-romero.jpg'],
        ['id' => 6, 'tienda' => 'Joyería de Cacao Talamanca',  'insignia' => 'Observación de Envíos', 'tipo' => 'observacion', 'expediente' => '#CR-2025-058', 'categoria' => 'Accesorios',               'icono' => 'estrella', 'ubicacion' => 'Puerto Viejo, Limón',    'provincia' => 'Limón',      'fecha' => '08 Mar 2025',            'nota' => 'Validar cobertura Correos CR', 'estado' => 'alerta', 'semaforo' => 'rojo',  'sello' => true,  'imagen' => 'productos/chocolate-bribri.jpg'],
    ];
}

// Totales: se calculan a partir de la lista
$totalFilas    = count($solicitudes);
$totalNuevas   = 0;
$totalObserv   = 0;
$conSello      = 0;
$provinciasUsadas = [];

for ($i = 0; $i < $totalFilas; $i++) {
    if ($solicitudes[$i]['tipo'] === 'nueva') {
        $totalNuevas++;
    } else {
        $totalObserv++;
    }
    if ($solicitudes[$i]['sello']) {
        $conSello++;
    }
    if (!in_array($solicitudes[$i]['provincia'], $provinciasUsadas)) {
        $provinciasUsadas[] = $solicitudes[$i]['provincia'];
    }
}

$porcentajeSello = $totalFilas > 0 ? round($conSello * 100 / $totalFilas) : 0;

require __DIR__ . '/../general/header.php';
?>

<div class="panel">
    <?php require __DIR__ . '/../general/sidebar_admin.php'; ?>

    <div class="panel__principal">
        <?php require __DIR__ . '/../general/barra_admin.php'; ?>

        <main id="contenido" class="panel__contenido">

            <!-- Encabezado y pestañas -->
            <section class="ad-cabecera">
                <div>
                    <p class="ad-etiquetas">
                        <span class="ad-pastilla ad-pastilla--verde"><span class="punto"></span> Revisión Activa</span>
                        <span class="ad-sobretitulo ad-sobretitulo--suave">Ciclo Q1 • Costa Rica</span>
                    </p>
                    <h1 class="ad-titulo">Emprendimientos Pendientes</h1>
                    <p class="ad-descripcion">Solicitudes de ingreso a la plataforma en espera de validación técnica y artesanal.</p>
                </div>

                <div class="so-pestanas solo-pantalla" role="tablist" aria-label="Filtrar solicitudes">
                    <button class="so-pestana es-activo" type="button" role="tab" aria-selected="true" id="pestanaTodas">Todas <span><?php echo $totalFilas; ?></span></button>
                    <button class="so-pestana" type="button" role="tab" aria-selected="false" id="pestanaNuevas">Nuevas <span class="so-pestana__azul"><?php echo $totalNuevas; ?></span></button>
                    <button class="so-pestana" type="button" role="tab" aria-selected="false" id="pestanaObservaciones">Con observaciones <span><?php echo $totalObserv; ?></span></button>
                </div>
            </section>

            <!-- Mensaje (lo muestra solicitudes_admin.js) -->
            <div id="mensajeSolicitudes" class="mensaje mensaje--info ad-oculto" role="status">
                <p id="textoMensajeSolicitudes"></p>
            </div>

            <!-- Indicadores -->
            <ul class="so-indicadores">
                <li class="so-indicador">
                    <div>
                        <small>Tiempo Promedio de Dictamen</small>
                        <strong>26.4 hrs</strong>
                        <p class="so-indicador__nota so-indicador__nota--exito"><?php echo icono('tendencia'); ?> 4h más rápido que el mes pasado</p>
                    </div>
                    <span class="so-indicador__icono so-indicador__icono--crema"><?php echo icono('reloj'); ?></span>
                </li>
                <li class="so-indicador">
                    <div>
                        <small>Sello Artesanal Tico</small>
                        <strong><?php echo $porcentajeSello; ?>%</strong>
                        <p class="so-indicador__nota"><?php echo $conSello; ?> de <?php echo $totalFilas; ?> adjuntaron Denominación</p>
                    </div>
                    <span class="so-indicador__icono so-indicador__icono--verde"><?php echo icono('verificado'); ?></span>
                </li>
                <li class="so-indicador">
                    <div>
                        <small>Distribución Geográfica</small>
                        <strong><?php echo count($provinciasUsadas); ?> Provincias</strong>
                        <p class="so-indicador__nota"><?php echo e(implode(', ', $provinciasUsadas)); ?></p>
                    </div>
                    <span class="so-indicador__icono so-indicador__icono--teal"><?php echo icono('ubicacion'); ?></span>
                </li>
            </ul>

            <!-- Lista de solicitudes (la arma PHP con un for) -->
            <input type="hidden" id="totalSolicitudes" value="<?php echo $totalFilas; ?>">

            <ul class="so-lista">
                <?php for ($i = 0; $i < $totalFilas; $i++): ?>
                    <?php
                    $solicitud   = $solicitudes[$i];
                    $enlace      = ruta('admin/solicitudes/detalle') . '?id=' . $solicitud['id'];
                    $claseBoton  = $solicitud['estado'] === 'ok' ? 'ad-boton--azul' : 'ad-boton--claro';
                    $claseInsig  = $solicitud['tipo'] === 'nueva' ? 'so-insignia--nueva' : 'so-insignia--obs';
                    if ($solicitud['insignia'] === 'Sello Cultural') {
                        $claseInsig = 'so-insignia--sello';
                    }
                    ?>
                    <li class="so-fila" id="solicitud-<?php echo $i; ?>">
                        <div class="so-fila__foto">
                            <img src="<?php echo e(recurso('img/uploads/' . $solicitud['imagen'])); ?>" alt="" width="72" height="72">
                            <span class="so-fila__semaforo so-fila__semaforo--<?php echo e($solicitud['semaforo']); ?>"></span>
                        </div>

                        <div class="so-fila__info">
                            <h2>
                                <a href="<?php echo e($enlace); ?>" id="tienda-<?php echo $i; ?>"><?php echo e($solicitud['tienda']); ?></a>
                                <span class="so-insignia <?php echo $claseInsig; ?>"><?php echo e($solicitud['insignia']); ?></span>
                                <span class="so-insignia so-insignia--expediente" id="expediente-<?php echo $i; ?>">Expediente <?php echo e($solicitud['expediente']); ?></span>
                            </h2>
                            <p class="so-fila__datos">
                                <span class="so-fila__categoria"><?php echo icono($solicitud['icono']); ?> <?php echo e($solicitud['categoria']); ?></span>
                                <span><?php echo icono('ubicacion'); ?> <?php echo e($solicitud['ubicacion']); ?></span>
                                <span><?php echo icono('reloj'); ?> <?php echo e($solicitud['fecha']); ?></span>
                            </p>
                        </div>

                        <p class="so-fila__nota so-fila__nota--<?php echo e($solicitud['estado']); ?>">
                            <span class="punto"></span> <span id="nota-<?php echo $i; ?>"><?php echo e($solicitud['nota']); ?></span>
                        </p>

                        <a class="ad-boton <?php echo $claseBoton; ?> so-fila__boton" href="<?php echo e($enlace); ?>">Ver detalle <?php echo icono('flecha'); ?></a>

                        <!-- Datos que usan las pestañas, la búsqueda y el CSV -->
                        <input type="hidden" id="tipo-<?php echo $i; ?>" value="<?php echo e($solicitud['tipo']); ?>">
                        <input type="hidden" id="ubicacion-<?php echo $i; ?>" value="<?php echo e($solicitud['ubicacion']); ?>">
                        <input type="hidden" id="categoria-<?php echo $i; ?>" value="<?php echo e($solicitud['categoria']); ?>">
                        <input type="hidden" id="fecha-<?php echo $i; ?>" value="<?php echo e($solicitud['fecha']); ?>">
                    </li>
                <?php endfor; ?>
            </ul>

            <p class="ad-vacio ad-oculto" id="sinResultados">No hay solicitudes en esta pestaña.</p>

            <!-- Pie -->
            <div class="so-pie">
                <p><span class="punto"></span> <span id="contadorFilas">Mostrando <?php echo $totalFilas; ?> solicitudes pendientes para dictamen del comité de calidad</span></p>
                <button class="ad-boton ad-boton--claro solo-pantalla" type="button" id="botonCsv"><?php echo icono('recibo'); ?> Descargar reporte CSV</button>
            </div>

        </main>
    </div>
</div>

<?php require __DIR__ . '/../general/footer.php'; ?>
