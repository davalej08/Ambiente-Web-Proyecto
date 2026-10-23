<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Detalle de producto: vitrina pública del emprendimiento con su catálogo
 * (Figma: Cliente · Detalle de Producto)
 * Ruta sugerida: producto/detalle?id=
 *
 * Variables que puede enviar CatalogoController:
 *   $emprendimiento  datos del emprendimiento (ver datos de muestra abajo)
 *   $productos       productos de ese emprendimiento
 *   $resenas         reseñas de compradores
 * Si no llegan, se usan datos de muestra para el Avance 2.
 */

$titulo       = 'Vitrina del emprendimiento';
$estilos      = ['producto'];
$scripts      = ['producto'];
$paginaActiva = 'catalogo';

if (!isset($emprendimiento)) {
    $emprendimiento = [
        'id'          => 1,
        'nombre'      => 'Beneficio La Pastora',
        'provincia'   => 'Puntarenas',
        'ubicacion'   => 'Monteverde, Puntarenas',
        'altitud'     => '1,450 m.s.n.m.',
        'descripcion' => 'Micro-lote de café especial de altura cosechado a mano en el bosque nuboso. Tueste artesanal en leña de guaba y despachos directos a todo el país.',
        'historia_titulo' => 'Cultivado bajo la bruma del bosque nuboso desde 1982',
        'historia'    => 'Somos la familia Valverde Brenes. Por tres generaciones hemos protegido la cuenca biológica de Monteverde cultivando micro-lotes de variedades arábicas en armonía con quetzales y colibríes. Cosechamos solo cerezas en punto óptimo y tostamos semanalmente en pequeñas tandas.',
        'sellos'      => ['Denominación de origen Puntarenas', 'Cosecha agroecológica', 'Comercio justo tico'],
        'calificacion' => 4.9,
        'total_resenas' => 128,
        'telefono'    => '8842-9011',
        'imagen'      => 'productos/cafe-geisha.jpg',
    ];
}

if (!isset($productos)) {
    $productos = [
        ['id' => 1, 'nombre' => 'Café Geisha Reserva Especial', 'nota' => 'Micro-lote de altura con notas a flor de jazmín y miel.', 'categoria' => 'grano',     'etiqueta' => '100% Tico',         'detalle' => 'Cosecha fresca', 'estado' => 'En stock',   'precio' => 9800,  'sinpe' => true,  'imagen' => 'productos/cafe-geisha.jpg'],
        ['id' => 2, 'nombre' => 'Miel de Flor de Cafeto',       'nota' => 'Recolectada por abejas en los cafetales florecidos.',     'categoria' => 'regalo',    'etiqueta' => 'Apiario propio',    'detalle' => '100% pura',      'estado' => 'Disponible', 'precio' => 5400,  'sinpe' => false, 'imagen' => 'productos/miel-cruda.jpg'],
        ['id' => 3, 'nombre' => 'Tabla de Servir de Teca',      'nota' => 'Diseño rústico tradicional en madera recuperada.',         'categoria' => 'accesorios', 'etiqueta' => 'Madera sostenible', 'detalle' => 'Hecho a mano',   'estado' => 'Disponible', 'precio' => 12000, 'sinpe' => false, 'imagen' => 'productos/tabla-teca.jpg'],
        ['id' => 4, 'nombre' => 'Chocolate de Altura 75%',      'nota' => 'Barra artesanal para acompañar el café de la tarde.',     'categoria' => 'regalo',    'etiqueta' => 'Edición especial',  'detalle' => 'Últimas 6',      'estado' => 'Últimas 6',  'precio' => 3800,  'sinpe' => true,  'imagen' => 'productos/chocolate-bribri.jpg'],
        ['id' => 5, 'nombre' => 'Tote Bag de Yute Cafetalero',  'nota' => 'Confeccionado reutilizando sacos reales de exportación.',  'categoria' => 'accesorios', 'etiqueta' => 'Moda circular',     'detalle' => 'Hecho en taller', 'estado' => 'En stock',   'precio' => 14500, 'sinpe' => false, 'imagen' => 'productos/tote-yute.jpg'],
        ['id' => 6, 'nombre' => 'Jabón de Café y Romero',       'nota' => 'Exfoliante natural con borra de café de la finca.',       'categoria' => 'regalo',    'etiqueta' => 'Botánico',          'detalle' => 'Cera de abeja',  'estado' => 'En stock',   'precio' => 6500,  'sinpe' => false, 'imagen' => 'productos/jabon-romero.jpg'],
    ];
}

if (!isset($resenas)) {
    $resenas = [
        ['autor' => 'Mariana Solano', 'lugar' => 'Curridabat, San José', 'hace' => 'Hace 2 días',   'estrellas' => 5, 'texto' => 'El Geisha es simplemente extraordinario. Apenas abrí el paquete toda la cocina olió a miel y flores. Pagué por SINPE en 10 segundos y llegó al día siguiente.'],
        ['autor' => 'Carlos Umaña',   'lugar' => 'Barva, Heredia',       'hace' => 'Hace 5 días',   'estrellas' => 5, 'texto' => 'Me encantó el detalle de la nota escrita a mano por la familia. El café tiene un cuerpo perfecto para chorrear de tarde. Orgullo tico.'],
        ['autor' => 'Elena Vargas',   'lugar' => 'Paraíso, Cartago',     'hace' => 'Hace 1 semana', 'estrellas' => 4, 'texto' => 'Compré la tabla de teca junto con el café para un regalo de cumpleaños y quedaron fascinados con el acabado. Recomendados.'],
    ];
}

$filtrosCatalogo = [
    'todos'      => 'Todos los productos',
    'grano'      => 'Grano & molido',
    'regalo'     => 'Ediciones y regalos',
    'accesorios' => 'Accesorios',
];

$telefonoWhatsapp = '506' . str_replace('-', '', $emprendimiento['telefono']);

// Iniciales para el avatar de cada reseña. Ej: "Mariana Solano" -> "MS"
foreach ($resenas as $posicion => $resena) {
    $palabras = explode(' ', trim($resena['autor']));
    $iniciales = mb_strtoupper(mb_substr($palabras[0], 0, 1));
    if (count($palabras) > 1) {
        $iniciales .= mb_strtoupper(mb_substr($palabras[1], 0, 1));
    }
    $resenas[$posicion]['iniciales'] = $iniciales;
}

require __DIR__ . '/../general/header.php';
?>

<main id="contenido" class="contenedor vitrina-publica">

    <nav class="migas" aria-label="Ruta de navegación">
        <a href="<?php echo e(ruta('catalogo')); ?>">Ventica</a>
        <?php echo icono('derecha'); ?>
        <a href="<?php echo e(ruta('catalogo') . '?provincia=' . urlencode($emprendimiento['provincia'])); ?>">Emprendimientos <?php echo e($emprendimiento['provincia']); ?></a>
        <?php echo icono('derecha'); ?>
        <span aria-current="page"><?php echo e($emprendimiento['nombre']); ?></span>
    </nav>

    <!-- ===== Portada ===== -->
    <section class="portada tarjeta">
        <div class="portada__banner">
            <span class="portada__altitud"><?php echo icono('montana'); ?> <?php echo e($emprendimiento['altitud']); ?> • <?php echo e(explode(',', $emprendimiento['ubicacion'])[0]); ?></span>
            <?php echo icono('cafe', 'portada__marca-agua'); ?>
        </div>

        <div class="portada__cuerpo">
            <div class="portada__logo">
                <img src="<?php echo e(recurso('img/uploads/' . $emprendimiento['imagen'])); ?>" alt="" width="128" height="128">
                <span class="portada__verificado" title="Emprendimiento verificado"><?php echo icono('check'); ?></span>
            </div>

            <div class="portada__info">
                <h1><?php echo e($emprendimiento['nombre']); ?></h1>
                <span class="pill"><?php echo icono('ubicacion'); ?> <?php echo e($emprendimiento['ubicacion']); ?></span>
                <p><?php echo e($emprendimiento['descripcion']); ?></p>
            </div>

            <div class="portada__acciones">
                <a class="btn btn--primario" href="https://wa.me/<?php echo e($telefonoWhatsapp); ?>" target="_blank" rel="noopener">
                    <?php echo icono('mensaje'); ?> Contactar por WhatsApp
                </a>
                <button class="btn btn--borde" type="button" id="compartirVitrina"><?php echo icono('compartir'); ?> Compartir</button>
            </div>
        </div>

        <ul class="datos-vitrina">
            <li>
                <span class="datos-vitrina__icono"><?php echo icono('estrella'); ?></span>
                <span><strong><?php echo e(number_format($emprendimiento['calificacion'], 1)); ?> / 5.0</strong> <small>(<?php echo (int) $emprendimiento['total_resenas']; ?> reseñas) Reputación Pura Vida</small></span>
            </li>
            <li>
                <span class="datos-vitrina__icono"><?php echo icono('camion'); ?></span>
                <span><strong>Correos de Costa Rica</strong> <small>24 a 48 h a cualquier cantón</small></span>
            </li>
            <li>
                <span class="datos-vitrina__icono"><?php echo icono('celular'); ?></span>
                <span><strong>SINPE al +506 <?php echo e($emprendimiento['telefono']); ?></strong> <small>A nombre de <?php echo e($emprendimiento['nombre']); ?></small></span>
            </li>
            <li>
                <span class="datos-vitrina__icono"><?php echo icono('trato'); ?></span>
                <span><strong>0% comisiones abusivas</strong> <small>100% de tu pago va al productor</small></span>
            </li>
        </ul>
    </section>

    <!-- ===== Historia ===== -->
    <section class="historia tarjeta">
        <p class="sobretitulo"><?php echo icono('casa'); ?> Nuestra historia familiar</p>
        <h2><?php echo e($emprendimiento['historia_titulo']); ?></h2>
        <p class="historia__texto"><?php echo e($emprendimiento['historia']); ?></p>
        <ul class="historia__sellos">
            <?php foreach ($emprendimiento['sellos'] as $sello): ?>
                <li class="pill"><?php echo icono('check'); ?> <?php echo e($sello); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <!-- ===== Catálogo del emprendimiento ===== -->
    <section class="catalogo-vitrina" aria-labelledby="tituloCatalogo">
        <div class="catalogo-vitrina__cabecera">
            <div>
                <p class="sobretitulo"><span class="punto"></span> Cosechas y ediciones limitadas</p>
                <h2 class="titulo-seccion" id="tituloCatalogo">Catálogo de la finca</h2>
            </div>
            <div class="filtros-vitrina" role="group" aria-label="Filtrar productos">
                <?php foreach ($filtrosCatalogo as $clave => $texto): ?>
                    <button class="chip chip-filtro <?php echo $clave === 'todos' ? 'es-activo' : ''; ?>" type="button" data-filtro="<?php echo e($clave); ?>">
                        <?php echo e($texto); ?>
                        <?php if ($clave === 'todos'): ?>(<?php echo count($productos); ?>)<?php endif; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <ul class="productos productos--vitrina">
            <?php foreach ($productos as $producto): ?>
                <li class="producto-vitrina" data-categoria="<?php echo e($producto['categoria']); ?>">
                    <article class="producto">
                        <div class="producto__media">
                            <img src="<?php echo e(recurso('img/uploads/' . $producto['imagen'])); ?>" alt="<?php echo e($producto['nombre']); ?>" width="260" height="260">
                            <div class="producto__etiquetas">
                                <span class="producto__etiqueta"><?php echo e($producto['etiqueta']); ?></span>
                                <?php if ($producto['sinpe']): ?>
                                    <span class="producto__sinpe"><?php echo icono('check'); ?> SINPE inmediato</span>
                                <?php endif; ?>
                            </div>
                            <button class="producto__favorito" type="button" aria-pressed="false" aria-label="Agregar a favoritos">
                                <?php echo icono('corazon'); ?>
                            </button>
                        </div>

                        <div class="producto__cuerpo">
                            <p class="producto__ubicacion producto__ubicacion--doble">
                                <span><?php echo icono('destellos'); ?> <?php echo e($producto['detalle']); ?></span>
                                <span class="producto__estado"><?php echo e($producto['estado']); ?></span>
                            </p>
                            <h3 class="producto__nombre"><?php echo e($producto['nombre']); ?></h3>
                            <p class="producto__emprendimiento"><?php echo e($producto['nota']); ?></p>

                            <div class="producto__pie">
                                <p class="producto__precio"><small>Precio</small> <?php echo colones($producto['precio']); ?></p>
                                <form method="post" action="<?php echo e(ruta('carrito/agregar')); ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                                    <input type="hidden" name="id_producto" value="<?php echo (int) $producto['id']; ?>">
                                    <input type="hidden" name="cantidad" value="1">
                                    <button class="btn btn--oscuro btn--sm" type="submit" aria-label="Pedir <?php echo e($producto['nombre']); ?>">
                                        <?php echo icono('bolsa'); ?> Pedir
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
        <p class="sin-coincidencias" id="sinCoincidencias" hidden>No hay productos en esta categoría por ahora.</p>
    </section>

    <!-- ===== Condiciones ===== -->
    <section class="condiciones tarjeta" aria-labelledby="tituloCondiciones">
        <header class="condiciones__cabecera">
            <span class="condiciones__icono"><?php echo icono('escudo'); ?></span>
            <div>
                <h2 id="tituloCondiciones">Condiciones claras de pago y entrega nacional</h2>
                <p>Comprás con la garantía comunitaria de Ventica y recibís directo en tu casa.</p>
            </div>
        </header>

        <div class="condiciones__columnas">
            <div class="condiciones__bloque">
                <h3><?php echo icono('tarjeta'); ?> Formas de pago aceptadas</h3>
                <ul>
                    <li><?php echo icono('celular'); ?> <span><strong>SINPE Móvil inmediato (recomendado)</strong> Transferencia directa sin recargos ni comisiones intermediarias.</span></li>
                    <li><?php echo icono('tarjeta'); ?> <span><strong>Tarjetas de débito / crédito</strong> Visa y Mastercard emitidas por bancos costarricenses.</span></li>
                    <li><?php echo icono('efectivo'); ?> <span><strong>Efectivo contra entrega (GAM)</strong> Para entregas por mensajero en San José, Cartago, Alajuela y Heredia.</span></li>
                </ul>
            </div>
            <div class="condiciones__bloque">
                <h3><?php echo icono('camion'); ?> Cobertura de entrega y tiempos</h3>
                <ul>
                    <li><?php echo icono('caja'); ?> <span><strong>Correos de Costa Rica (todo el país)</strong> Tarifa plana de ₡2,400. Entregas en 24 a 48 horas hábiles.</span></li>
                    <li><?php echo icono('rayo'); ?> <span><strong>Mensajería express GAM</strong> Entregas el mismo día o al día siguiente coordinadas por WhatsApp.</span></li>
                    <li><?php echo icono('hoja'); ?> <span><strong>Empaque 100% biodegradable</strong> Bolsas compostables con válvula y sello de frescura.</span></li>
                </ul>
            </div>
        </div>
    </section>

    <!-- ===== Reseñas ===== -->
    <section class="resenas" aria-labelledby="tituloResenas">
        <div class="catalogo-vitrina__cabecera">
            <div>
                <p class="sobretitulo"><?php echo icono('mensaje'); ?> Experiencias Pura Vida</p>
                <h2 class="titulo-seccion" id="tituloResenas">Lo que dicen compradores ticos</h2>
            </div>
            <p class="resenas__promedio">
                <span class="estrellas" aria-hidden="true"><?php echo str_repeat(icono('estrella'), 5); ?></span>
                <strong><?php echo e(number_format($emprendimiento['calificacion'], 1)); ?> promedio</strong>
                <small>• <?php echo (int) $emprendimiento['total_resenas']; ?> valoraciones</small>
            </p>
        </div>

        <ul class="resenas__lista">
            <?php foreach ($resenas as $resena): ?>
                <li class="resena tarjeta">
                    <div class="resena__cabecera">
                        <span class="estrellas" role="img" aria-label="<?php echo (int) $resena['estrellas']; ?> de 5 estrellas">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php echo icono('estrella', $i <= $resena['estrellas'] ? 'es-llena' : ''); ?>
                            <?php endfor; ?>
                        </span>
                        <small><?php echo e($resena['hace']); ?></small>
                    </div>
                    <blockquote>"<?php echo e($resena['texto']); ?>"</blockquote>
                    <div class="resena__autor">
                        <span class="resena__avatar"><?php echo e($resena['iniciales']); ?></span>
                        <span>
                            <strong><?php echo e($resena['autor']); ?></strong>
                            <small><?php echo e($resena['lugar']); ?> • Comprador verificado</small>
                        </span>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

</main>

<?php require __DIR__ . '/../general/footer.php'; ?>
