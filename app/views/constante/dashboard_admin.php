<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Pantalla: Admin · Dashboard (Figma: Admin - Dashboard)
 * Ruta: admin/dashboard
 */

$titulo       = 'Panel General · Administración';
$estilos      = ['admin', 'admin_gestion', 'admin_inicio'];
$navbar       = false;
$footer       = false;
$claseBody    = 'body-panel';
$seccionPanel = 'inicio';

if (!isset($resumen)) {
    $resumen = [
        'pendientes'      => 6,
        'activos'         => 320,
        'nuevosMes'       => 18,
        'provincias'      => 7,
        'usuarios'        => 4850,
        'taxonomias'      => 14,
        'selloArtesanal'  => 94,
        'provinciasLista' => ['Alajuela', 'Cartago', 'Guanacaste'],
        'provinciasExtra' => 4,
    ];
}

$totalProvincias = count($resumen['provinciasLista']);

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
                        <span class="ad-sobretitulo ad-sobretitulo--verde"><span class="punto"></span> Sistema operativo • Red nacional</span>
                    </p>
                    <h1 class="ad-titulo">Panel General de Administración</h1>
                    <p class="ad-descripcion">Supervisión del ecosistema de emprendimientos costarricenses, artesanos y productores locales.</p>
                </div>
                <div class="in-corte">
                    <?php echo icono('reloj'); ?>
                    <span>
                        <small>Corte institucional</small>
                        <strong>Hoy, San José UTC-6</strong>
                    </span>
                </div>
            </section>

            <!-- Resumen de la plataforma -->
            <section class="in-resumen" aria-label="Resumen de la plataforma">

                <article class="in-dato">
                    <div class="in-dato__arriba">
                        <span class="in-dato__icono in-dato__icono--teal"><?php echo icono('reloj'); ?></span>
                        <span class="in-dato__etiqueta in-dato__etiqueta--alerta"><span class="punto"></span> Revisión prioritaria</span>
                    </div>
                    <h2>Emprendimientos pendientes</h2>
                    <p class="in-dato__valor">
                        <strong id="totalPendientes"><?php echo (int) $resumen['pendientes']; ?></strong>
                        <span>solicitudes</span>
                        <a class="in-dato__enlace" href="<?php echo e(ruta('admin/solicitudes')); ?>">Requiere acción <?php echo icono('flecha'); ?></a>
                    </p>
                </article>

                <article class="in-dato">
                    <div class="in-dato__arriba">
                        <span class="in-dato__icono in-dato__icono--crema"><?php echo icono('verificado'); ?></span>
                        <span class="in-dato__etiqueta in-dato__etiqueta--exito"><?php echo icono('tendencia'); ?> +<?php echo (int) $resumen['nuevosMes']; ?> este mes</span>
                    </div>
                    <h2>Emprendimientos activos</h2>
                    <p class="in-dato__valor">
                        <strong><?php echo (int) $resumen['activos']; ?></strong>
                        <span>marcas verificadas</span>
                        <em><?php echo (int) $resumen['provincias']; ?> Provincias</em>
                    </p>
                </article>

                <article class="in-dato">
                    <div class="in-dato__arriba">
                        <span class="in-dato__icono in-dato__icono--crema"><?php echo icono('usuario'); ?></span>
                        <span class="in-dato__etiqueta">Comunidad Tica</span>
                    </div>
                    <h2>Usuarios totales</h2>
                    <p class="in-dato__valor">
                        <strong><?php echo number_format($resumen['usuarios'], 0, '.', ','); ?></strong>
                        <span>registrados</span>
                        <em>Compradores y artesanos</em>
                    </p>
                </article>

            </section>

            <!-- Accesos rápidos -->
            <section class="in-acciones" aria-labelledby="tituloAcciones">
                <h2 class="in-subtitulo" id="tituloAcciones">Acciones Rápidas</h2>
                <p class="in-subtitulo__texto">Accesos directos para la operativa y control regular del catálogo.</p>

                <div class="in-acciones__lista">

                    <a class="in-accion" href="<?php echo e(ruta('admin/solicitudes')); ?>">
                        <span class="in-accion__flecha"><?php echo icono('flecha-diagonal'); ?></span>
                        <span class="in-dato__icono in-dato__icono--rojo"><?php echo icono('alerta'); ?></span>
                        <h3>Revisar Solicitudes Prioritarias</h3>
                        <p>Evaluar documentación legal, cédula física/jurídica y muestras artesanales pendientes.</p>
                        <span class="in-accion__nota in-accion__nota--alerta"><?php echo icono('alerta'); ?> <?php echo (int) $resumen['pendientes']; ?> comercios esperando validación</span>
                    </a>

                    <a class="in-accion" href="<?php echo e(ruta('admin/categorias')); ?>">
                        <span class="in-accion__flecha"><?php echo icono('flecha-diagonal'); ?></span>
                        <span class="in-dato__icono in-dato__icono--crema"><?php echo icono('filtros'); ?></span>
                        <h3>Gestión de Categorías</h3>
                        <p>Estructurar rubros: Café gourmet, Cerámica, Cuero, Gastronomía y Bienestar bio.</p>
                        <span class="in-accion__nota"><?php echo icono('filtros'); ?> <?php echo (int) $resumen['taxonomias']; ?> taxonomías activas</span>
                    </a>

                    <a class="in-accion" href="<?php echo e(ruta('admin/usuarios')); ?>">
                        <span class="in-accion__flecha"><?php echo icono('flecha-diagonal'); ?></span>
                        <span class="in-dato__icono in-dato__icono--crema"><?php echo icono('usuario'); ?></span>
                        <h3>Gestión de Usuarios</h3>
                        <p>Control de roles administrativos, resolución de incidencias y perfiles de artesanos.</p>
                        <span class="in-accion__nota"><?php echo icono('escudo'); ?> Auditoría de accesos al día</span>
                    </a>

                </div>
            </section>

            <!-- Banner del ecosistema -->
            <section class="in-ecosistema" aria-label="Ecosistema local">
                <span class="in-ecosistema__icono"><?php echo icono('hoja'); ?></span>
                <div class="in-ecosistema__texto">
                    <h2>Ecosistema 100% Producción Local</h2>
                    <p>El <?php echo (int) $resumen['selloArtesanal']; ?>% de las marcas cuentan con el sello de identidad artesanal y sostenibilidad tica.</p>
                </div>
                <ul class="in-ecosistema__provincias">
                    <?php for ($i = 0; $i < $totalProvincias; $i++): ?>
                        <li><?php echo e($resumen['provinciasLista'][$i]); ?></li>
                    <?php endfor; ?>
                    <li>+<?php echo (int) $resumen['provinciasExtra']; ?> más</li>
                </ul>
            </section>

        </main>
    </div>
</div>

<?php require __DIR__ . '/../general/footer.php'; ?>
