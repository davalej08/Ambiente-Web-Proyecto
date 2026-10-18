<?php
/**
 * Pantalla: Landing / Inicio (Figma: Landing · nodo 6:108)
 * Ruta sugerida: ''  (página de inicio)
 *
 * Variables opcionales del controlador:
 *   $categorias  arreglo con ['nombre','descripcion','cantidad','icono','color','id']
 *                Si no llega, se usan datos de muestra para el Avance 2.
 */

$titulo       = 'Inicio';
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

$origenes = [
    ['texto' => 'Crochet & Tejidos',   'detalle' => 'Hecho a mano'],
    ['texto' => 'Stickers & Pines',    'detalle' => 'Ilustración'],
    ['texto' => 'Velas & Aromas',      'detalle' => 'Cera de soya'],
    ['texto' => 'Maquillaje & Cuidado', 'detalle' => 'Indie'],
];

require __DIR__ . '/header.php';
?>

<main id="contenido">

    <!-- ===== Hero ===== -->
    <section class="hero">
        <div class="contenedor hero__contenido">
            <div class="hero__texto">
                <span class="pill"><?php echo icono('corazon'); ?> Mercado Oficial del Emprendimiento Tico <span class="separador">•</span> <span class="pill__azul">Todo lo Hecho en CR</span></span>

                <h1 class="hero__titulo">
                    El talento y la <span class="subrayado">calidez</span> de Costa Rica en tus manos
                </h1>

                <p class="hero__descripcion">
                    De stickers y pines hasta crochet, velas aromáticas, maquillaje, papelería y diseño local.
                    Conectamos directamente a marcas emergentes y creadores ticos con quienes apoyan
                    el talento hecho en Costa Rica.
                </p>

                <div class="hero__acciones">
                    <a class="btn btn--primario btn--lg" href="<?php echo e(ruta('catalogo')); ?>">
                        Explorar Emprendimientos <?php echo icono('flecha'); ?>
                    </a>
                    <a class="btn btn--texto" href="#comunidad">
                        Conocer a los productores <?php echo icono('flecha-diagonal'); ?>
                    </a>
                </div>

                <dl class="hero__datos">
                    <div>
                        <dt>+650</dt>
                        <dd>Emprendimientos ticos</dd>
                    </div>
                    <div>
                        <dt class="verde">Variedad</dt>
                        <dd>Stickers, crochet, belleza y más</dd>
                    </div>
                    <div>
                        <dt>SINPE Móvil</dt>
                        <dd>Pago directo y seguro</dd>
                    </div>
                </dl>
            </div>

            <figure class="hero__visual">
                <img src="<?php echo e(recurso('img/colibri.png')); ?>" alt="Sticker de colibrí, símbolo de Ventica" width="405" height="412">
                <span class="etiqueta-flotante etiqueta-flotante--terracota">Hecho a Mano</span>
                <span class="etiqueta-flotante etiqueta-flotante--teal"><?php echo icono('corazon'); ?> PURA VIDA</span>
                <span class="etiqueta-flotante etiqueta-flotante--azul">VENTICA <small>CR</small></span>
                <span class="etiqueta-flotante etiqueta-flotante--lima">100% Tico</span>
            </figure>
        </div>
    </section>

    <!-- ===== Franja de orígenes ===== -->
    <section class="origenes" aria-label="Lo que encontrás en Ventica">
        <div class="contenedor origenes__contenido">
            <p class="origenes__titulo"><?php echo icono('verificado'); ?> Orígenes con orgullo nacional:</p>
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
                    <p class="sobretitulo">¿Buscás algo único?</p>
                    <h2 class="titulo-grande" id="tituloCategorias">Todo tipo de emprendimientos locales</h2>
                </div>
                <p class="texto-guia">
                    Comprá directamente a creadores ticos: desde accesorios, stickers y crochet
                    hasta velas, cosmética y decoración para el hogar.
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
                <p class="sobretitulo">Comercio justo y cercano</p>
                <h2 class="titulo-grande" id="tituloPorque">¿Por qué elegir Ventica?</h2>
                <p class="texto-guia">
                    Una plataforma pensada para impulsar a quien crea con pasión
                    y permitirte comprar con absoluta confianza y cercanía.
                </p>
            </header>

            <div class="beneficios">
                <article class="beneficio">
                    <span class="beneficio__icono beneficio__icono--verde"><?php echo icono('trato'); ?></span>
                    <h3>Trato Directo con el Emprendedor</h3>
                    <p>
                        Sin intermediarios abusivos que encarezcan el producto artesanal.
                        El 100% de tu apoyo impulsa directamente a familias y creadores del país.
                    </p>
                </article>
                <article class="beneficio">
                    <span class="beneficio__icono beneficio__icono--azul"><?php echo icono('celular'); ?></span>
                    <h3>SINPE Móvil y Tarjeta</h3>
                    <p>
                        Comprá con la facilidad y rapidez del día a día costarricense.
                        Validaciones automáticas y confirmación inmediata con el artesano.
                    </p>
                </article>
                <article class="beneficio">
                    <span class="beneficio__icono beneficio__icono--terracota"><?php echo icono('camion'); ?></span>
                    <h3>Envíos a Todo el País</h3>
                    <p>
                        Alianzas seguras de distribución y Correos de Costa Rica que llegan
                        hasta el último rincón de las siete provincias, garantizando empaque responsable.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <!-- ===== CTA emprendedores ===== -->
    <section class="cta-banda" id="comunidad" aria-labelledby="tituloCta">
        <div class="contenedor cta-banda__contenido">
            <div>
                <p class="sobretitulo sobretitulo--lima">Comunidad creativa tica</p>
                <h2 id="tituloCta">¿Tenés un emprendimiento en Costa Rica?</h2>
                <p>
                    Vendé tus stickers, velas, prendas de crochet, maquillaje, arte o productos con nosotros.
                    Tu vitrina digital sin intermediarios ni costos ocultos.
                </p>
            </div>
            <a class="btn btn--lima btn--lg" href="<?php echo e(ruta('auth/registro-vendedor')); ?>">
                Sumar mi Emprendimiento <?php echo icono('flecha'); ?>
            </a>
        </div>
    </section>

</main>

<?php require __DIR__ . '/footer.php'; ?>
