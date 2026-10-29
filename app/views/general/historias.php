<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/**
 * Pantalla: Historias de Artesanos
 * Ruta: historias
 *
 * Tarjetas con la historia de cada emprendimiento aprobado: quién está detrás,
 * de dónde es, qué hace y un botón para ver su vitrina. Se filtran por provincia
 * con historias.js (sin recargar la página).
 *
 * Variables que envía CatalogoController:
 *   $historias  ['id', 'nombre', 'dueno', 'historia', 'canton', 'provincia', 'categoria',
 *                'productos', 'calificacion', 'desde', 'imagen']
 */

$titulo       = 'Historias de Artesanos';
$descripcion  = 'Conocé a las personas detrás de cada emprendimiento tico en Ventica.';
$estilos      = ['inicio', 'paginas'];
$scripts      = ['historias'];
$paginaActiva = 'historias';

if (!isset($historias)) {
    $historias = [];
}

// Provincias que tienen al menos una historia (para los botones de filtro)
$provinciasConHistorias = [];
$totalProductos = 0;
foreach ($historias as $historia) {
    if ($historia['provincia'] !== '' && !in_array($historia['provincia'], $provinciasConHistorias, true)) {
        $provinciasConHistorias[] = $historia['provincia'];
    }
    $totalProductos = $totalProductos + $historia['productos'];
}

require __DIR__ . '/header.php';
?>

<main id="contenido">

    <!-- ===== Encabezado ===== -->
    <section class="historias-portada">
        <div class="contenedor historias-portada__contenido">
            <p class="sobretitulo"><?php echo icono('corazon'); ?> Historias de artesanos</p>
            <h1 class="titulo-pagina">Las manos detrás de cada producto</h1>
            <p class="texto-guia">
                Cada compra en Ventica va directo a una familia, un taller o una persona que decidió emprender
                en su comunidad. Estas son sus historias.
            </p>

            <dl class="historias-portada__datos">
                <div>
                    <dt><?php echo count($historias); ?></dt>
                    <dd>emprendimientos verificados</dd>
                </div>
                <div>
                    <dt><?php echo count($provinciasConHistorias); ?></dt>
                    <dd>provincias representadas</dd>
                </div>
                <div>
                    <dt><?php echo (int) $totalProductos; ?></dt>
                    <dd>productos hechos en Costa Rica</dd>
                </div>
            </dl>
        </div>
    </section>

    <section class="contenedor seccion historias" aria-labelledby="tituloHistorias">
        <h2 class="oculto-visual" id="tituloHistorias">Emprendimientos</h2>

        <!-- Filtro por provincia -->
        <div class="historias__filtros" role="group" aria-label="Filtrar por provincia">
            <button class="chip filtro-historia es-activo" type="button" data-provincia="todas" aria-pressed="true">Todas</button>
            <?php foreach ($provinciasConHistorias as $provincia): ?>
                <button class="chip filtro-historia" type="button" data-provincia="<?php echo e($provincia); ?>" aria-pressed="false"><?php echo e($provincia); ?></button>
            <?php endforeach; ?>
        </div>

        <?php if (count($historias) === 0): ?>
            <div class="tarjeta historias__vacio">
                <?php echo icono('tienda'); ?>
                <h3>Pronto vas a ver historias aquí</h3>
                <p class="texto-guia">Los emprendimientos aparecen cuando el equipo de Ventica aprueba su vitrina.</p>
            </div>
        <?php endif; ?>

        <ul class="historias__lista" id="listaHistorias">
            <?php foreach ($historias as $historia): ?>
                <li class="historia-tarjeta" data-provincia="<?php echo e($historia['provincia']); ?>">
                    <article>
                        <div class="historia-tarjeta__foto">
                            <img src="<?php echo e(recurso('img/uploads/' . $historia['imagen'])); ?>" alt="" width="400" height="260" loading="lazy">
                            <span class="insignia historia-tarjeta__categoria"><?php echo e($historia['categoria']); ?></span>
                        </div>

                        <div class="historia-tarjeta__cuerpo">
                            <p class="historia-tarjeta__lugar"><?php echo icono('ubicacion'); ?> <?php echo e($historia['canton']); ?><?php echo $historia['provincia'] !== '' && $historia['provincia'] !== $historia['canton'] ? ', ' . e($historia['provincia']) : ''; ?></p>
                            <h3><?php echo e($historia['nombre']); ?></h3>
                            <p class="historia-tarjeta__autor">
                                <span class="historia-tarjeta__avatar" aria-hidden="true"><?php echo e(iniciales($historia['dueno'])); ?></span>
                                Por <?php echo e($historia['dueno']); ?> · en Ventica desde <?php echo e($historia['desde']); ?>
                            </p>
                            <p class="historia-tarjeta__texto"><?php echo e($historia['historia']); ?></p>

                            <div class="historia-tarjeta__pie">
                                <p class="historia-tarjeta__datos">
                                    <?php if ($historia['calificacion'] > 0): ?>
                                        <span><?php echo icono('estrella', 'es-llena'); ?> <?php echo e(number_format($historia['calificacion'], 1)); ?></span>
                                    <?php endif; ?>
                                    <span><?php echo icono('caja'); ?> <?php echo (int) $historia['productos']; ?> productos</span>
                                </p>
                                <a class="btn btn--oscuro btn--sm" href="<?php echo e(ruta('producto/detalle') . '?emprendimiento=' . (int) $historia['id']); ?>">
                                    Ver su vitrina <?php echo icono('flecha'); ?>
                                </a>
                            </div>
                        </div>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
        <p class="sin-coincidencias" id="sinHistorias" hidden>No hay historias de esa provincia todavía.</p>
    </section>

    <!-- ===== Invitación a emprendedores (mismo diseño del inicio) ===== -->
    <section class="cta-banda" aria-labelledby="tituloCtaHistorias">
        <div class="contenedor cta-banda__contenido">
            <div>
                <p class="sobretitulo sobretitulo--lima">Tu historia también cuenta</p>
                <h2 id="tituloCtaHistorias">¿Querés aparecer aquí?</h2>
                <p>Abrí tu vitrina gratis. Cuando el equipo la apruebe, tu historia se suma a esta página.</p>
            </div>
            <a class="btn btn--lima btn--lg" href="<?php echo e(ruta('auth/registro-vendedor')); ?>">
                Sumar mi emprendimiento <?php echo icono('flecha'); ?>
            </a>
        </div>
    </section>

</main>

<?php require __DIR__ . '/footer.php'; ?>
