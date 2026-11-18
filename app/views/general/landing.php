<?php
/**
 * Pantalla: Landing / Inicio (Figma: Landing · nodo 6:108)
 * Ruta sugerida: ''  (página de inicio)
 *
 * Variables opcionales del controlador:
 *   $categorias  arreglo con ['nombre','descripcion','cantidad','icono','color','id']
 *                Si no llega, se usan datos de muestra para el Avance 2.
 */

$titulo       = t('inicio.titulo');
$descripcion  = 'Ventica: el mercado del talento costarricense. Crochet, stickers, velas, maquillaje, café y más.';
$estilos      = ['inicio'];
$paginaActiva = 'inicio';

// Datos de muestra (reemplazar por los que mande CategoriaController)
if (!isset($categorias)) {
    $categorias = [
        ['id' => 1, 'nombre' => 'Crochet & Tejido',     'descripcion' => 'Amigurumi & prendas',   'cantidad' => '70+ creadoras', 'icono' => 'tijeras',   'color' => 'teal'],
        ['id' => 2, 'nombre' => 'Stickers & Pines',     'descripcion' => 'Ilustración & humor',   'cantidad' => '110+ marcas',   'icono' => 'destellos', 'color' => 'lima'],
        ['id' => 3, 'nombre' => 'Velas & Aromas',       'descripcion' => 'Cera de soya & difusores', 'cantidad' => '40+ talleres', 'icono' => 'llama',  'color' => 'terracota'],
        ['id' => 4, 'nombre' => 'Belleza & Maquillaje', 'descripcion' => 'Skincare & cosmética',  'cantidad' => '55+ marcas',    'icono' => 'sonrisa',   'color' => 'verde'],
        ['id' => 5, 'nombre' => 'Hogar & Deco',         'descripcion' => 'Cerámica & decoración', 'cantidad' => '80+ tiendas',   'icono' => 'casa',      'color' => 'azul'],
        ['id' => 6, 'nombre' => 'Moda & Accesorios',    'descripcion' => 'Joyería & tote bags',   'cantidad' => '90+ marcas',    'icono' => 'camisa',    'color' => 'terracota'],
        ['id' => 7, 'nombre' => 'Papelería & Arte',     'descripcion' => 'Prints, libretas & agendas', 'cantidad' => '70+ creadores', 'icono' => 'lapiz', 'color' => 'azul'],
        ['id' => 8, 'nombre' => 'Café & Delicias',      'descripcion' => 'Granos, salsas & café', 'cantidad' => '35+ fincas',    'icono' => 'cafe',      'color' => 'lima'],
    ];
}

// Los textos vienen de config/idiomas (HU-08)
$origenes = [
    ['texto' => t('origen.crochet'),  'detalle' => t('origen.crochetd')],
    ['texto' => t('origen.stickers'), 'detalle' => t('origen.stickersd')],
    ['texto' => t('origen.velas'),    'detalle' => t('origen.velasd')],
    ['texto' => t('origen.belleza'),  'detalle' => t('origen.bellezad')],
];

require __DIR__ . '/header.php';
?>

<main id="contenido">

    <!-- ===== Hero ===== -->
    <section class="hero">
        <div class="contenedor hero__contenido">
            <div class="hero__texto">
                <span class="pill"><?php echo icono('corazon'); ?> <?php echo e(t('hero.pill')); ?> <span class="separador">•</span> <span class="pill__azul"><?php echo e(t('hero.pill2')); ?></span></span>

                <h1 class="hero__titulo">
                    <?php echo e(t('hero.titulo1')); ?> <span class="subrayado"><?php echo e(t('hero.titulo2')); ?></span> <?php echo e(t('hero.titulo3')); ?>
                </h1>

                <p class="hero__descripcion">
                    <?php echo e(t('hero.descripcion')); ?>
                </p>

                <div class="hero__acciones">
                    <a class="btn btn--primario btn--lg" href="<?php echo e(ruta('catalogo')); ?>">
                        <?php echo e(t('hero.explorar')); ?> <?php echo icono('flecha'); ?>
                    </a>
                    <a class="btn btn--texto" href="<?php echo e(ruta('historias')); ?>">
                        <?php echo e(t('hero.productores')); ?> <?php echo icono('flecha-diagonal'); ?>
                    </a>
                </div>

                <dl class="hero__datos">
                    <div>
                        <dt>+650</dt>
                        <dd><?php echo e(t('hero.dato1')); ?></dd>
                    </div>
                    <div>
                        <dt class="verde"><?php echo e(t('hero.dato2t')); ?></dt>
                        <dd><?php echo e(t('hero.dato2')); ?></dd>
                    </div>
                    <div>
                        <dt>SINPE Móvil</dt>
                        <dd><?php echo e(t('hero.dato3')); ?></dd>
                    </div>
                </dl>
            </div>

            <figure class="hero__visual">
                <img src="<?php echo e(recurso('img/colibri.png')); ?>" alt="Sticker de colibrí, símbolo de Ventica" width="405" height="412">
                <span class="etiqueta-flotante etiqueta-flotante--terracota"><?php echo e(t('hero.mano')); ?></span>
                <span class="etiqueta-flotante etiqueta-flotante--teal"><?php echo icono('corazon'); ?> PURA VIDA</span>
                <span class="etiqueta-flotante etiqueta-flotante--azul">VENTICA <small>CR</small></span>
                <span class="etiqueta-flotante etiqueta-flotante--lima"><?php echo e(t('hero.tico')); ?></span>
            </figure>
        </div>
    </section>

    <!-- ===== Franja de orígenes ===== -->
    <section class="origenes" aria-label="Lo que encontrás en Ventica">
        <div class="contenedor origenes__contenido">
            <p class="origenes__titulo"><?php echo icono('verificado'); ?> <?php echo e(t('origen.titulo')); ?></p>
            <ul class="origenes__lista">
                <?php foreach ($origenes as $origen): ?>
                    <li><span class="punto"></span> <?php echo e($origen['texto']); ?> <small>(<?php echo e($origen['detalle']); ?>)</small></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <!-- ===== Categorías ===== -->
    <section class="seccion" id="categorias" aria-labelledby="tituloCategorias">
        <div class="contenedor">
            <div class="cabecera-seccion">
                <div>
                    <p class="sobretitulo"><?php echo e(t('cat.sobretitulo')); ?></p>
                    <h2 class="titulo-grande" id="tituloCategorias"><?php echo e(t('cat.titulo')); ?></h2>
                </div>
                <p class="texto-guia">
                    <?php echo e(t('cat.texto')); ?>
                </p>
            </div>

            <ul class="categorias">
                <?php foreach ($categorias as $categoria): ?>
                    <li>
                        <a class="categoria categoria--<?php echo e($categoria['color']); ?>" href="<?php echo e(ruta('catalogo?categoria=' . (int) $categoria['id'])); ?>">
                            <span class="categoria__icono"><?php echo icono($categoria['icono']); ?></span>
                            <strong><?php echo e($categoria['nombre']); ?></strong>
                            <small><?php echo e($categoria['descripcion']); ?></small>
                            <span class="insignia"><?php echo e($categoria['cantidad']); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <!-- ===== Por qué Ventica ===== -->
    <section class="seccion porque" aria-labelledby="tituloPorque">
        <div class="contenedor">
            <header class="porque__encabezado">
                <p class="sobretitulo"><?php echo e(t('porque.sobretitulo')); ?></p>
                <h2 class="titulo-grande" id="tituloPorque"><?php echo e(t('porque.titulo')); ?></h2>
                <p class="texto-guia">
                    <?php echo e(t('porque.texto')); ?>
                </p>
            </header>

            <div class="beneficios">
                <article class="beneficio">
                    <span class="beneficio__icono beneficio__icono--verde"><?php echo icono('trato'); ?></span>
                    <h3><?php echo e(t('porque.b1t')); ?></h3>
                    <p><?php echo e(t('porque.b1')); ?></p>
                </article>
                <article class="beneficio">
                    <span class="beneficio__icono beneficio__icono--azul"><?php echo icono('celular'); ?></span>
                    <h3><?php echo e(t('porque.b2t')); ?></h3>
                    <p><?php echo e(t('porque.b2')); ?></p>
                </article>
                <article class="beneficio">
                    <span class="beneficio__icono beneficio__icono--terracota"><?php echo icono('camion'); ?></span>
                    <h3><?php echo e(t('porque.b3t')); ?></h3>
                    <p><?php echo e(t('porque.b3')); ?></p>
                </article>
            </div>
        </div>
    </section>

    <!-- ===== CTA emprendedores ===== -->
    <section class="cta-banda" id="comunidad" aria-labelledby="tituloCta">
        <div class="contenedor cta-banda__contenido">
            <div>
                <p class="sobretitulo sobretitulo--lima"><?php echo e(t('cta.sobretitulo')); ?></p>
                <h2 id="tituloCta"><?php echo e(t('cta.titulo')); ?></h2>
                <p><?php echo e(t('cta.texto')); ?></p>
            </div>
            <a class="btn btn--lima btn--lg" href="<?php echo e(ruta('auth/registro-vendedor')); ?>">
                <?php echo e(t('cta.boton')); ?> <?php echo icono('flecha'); ?>
            </a>
        </div>
    </section>

</main>

<?php require __DIR__ . '/footer.php'; ?>
