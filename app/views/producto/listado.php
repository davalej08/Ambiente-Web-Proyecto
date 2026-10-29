<?php
/**
 * Pantalla: Home / Catálogo (Figma: home · nodo 10:2)
 * Ruta sugerida: catalogo
 * Filtros por GET: ?q=&provincia=&categoria[]=&min=&max=&pago=&entrega[]=&orden=&pagina=
 *
 * Variables que puede enviar CatalogoController:
 *   $productos      arreglo de productos (ver datos de muestra abajo para las llaves)
 *   $totalProductos total de resultados (para "8 de 342 productos")
 *   $paginaActual   número de página
 *   $totalPaginas   cantidad de páginas
 * Si no llegan, se usan datos de muestra para el Avance 2.
 */

$titulo       = 'Explorar Directorio';
$descripcion  = 'Descubrí productos hechos por emprendedores de las 7 provincias de Costa Rica.';
$estilos      = ['producto'];
$scripts      = ['producto'];
$paginaActiva = 'catalogo';

$provincias = ['San José', 'Alajuela', 'Cartago', 'Heredia', 'Guanacaste', 'Puntarenas', 'Limón'];

if (!isset($categoriasFiltro)) {
    $categoriasFiltro = [
        ['id' => 1, 'nombre' => 'Café de Especialidad', 'cantidad' => 128],
        ['id' => 2, 'nombre' => 'Artesanías y Maderas', 'cantidad' => 84],
        ['id' => 3, 'nombre' => 'Gourmet & Mieles',     'cantidad' => 62],
        ['id' => 4, 'nombre' => 'Cosmética & Jabones',  'cantidad' => 49],
        ['id' => 5, 'nombre' => 'Joyería Artesanal',    'cantidad' => 73],
    ];
}

$metodosPago = [
    'sinpe'    => 'SINPE Móvil Inmediato',
    'tarjeta'  => 'Tarjeta Débito / Crédito',
    'efectivo' => 'Efectivo contra entrega',
];

$metodosEntrega = [
    'correos'    => 'Correos de Costa Rica',
    'taller'     => 'Retiro en taller',
    'mensajeria' => 'Mensajería GAM (mismo día)',
];

$opcionesOrden = [
    'recomendados' => 'Recomendados por la Comunidad',
    'recientes'    => 'Más recientes',
    'precio_asc'   => 'Precio: menor a mayor',
    'precio_desc'  => 'Precio: mayor a menor',
];

// Productos de muestra (las imágenes están en public/static/img/uploads/productos/)
if (!isset($productos)) {
    $productos = [
        ['id' => 1, 'nombre' => 'Café Geisha Reserva Especial',  'emprendimiento' => 'Beneficio La Pastora',        'ubicacion' => 'Dota, San José',       'precio' => 9500,  'etiqueta' => '100% Tico',         'sinpe' => true,  'imagen' => 'productos/cafe-geisha.jpg'],
        ['id' => 2, 'nombre' => 'Miel Cruda de Bosque Tropical', 'emprendimiento' => 'Apiario Montes del Aguacate', 'ubicacion' => 'San Ramón, Alajuela',  'precio' => 5800,  'etiqueta' => 'Cosecha Silvestre', 'sinpe' => false, 'imagen' => 'productos/miel-cruda.jpg'],
        ['id' => 3, 'nombre' => 'Aretes Precolombinos',          'emprendimiento' => 'Orfebrería Chibcha CR',       'ubicacion' => 'Curridabat, San José', 'precio' => 14500, 'etiqueta' => 'Edición Limitada',  'sinpe' => false, 'imagen' => 'productos/aretes-precolombinos.jpg'],
        ['id' => 4, 'nombre' => 'Tabla de Madera Teca Noble',    'emprendimiento' => 'Taller Baula Artesanos',      'ubicacion' => 'Nicoya, Guanacaste',   'precio' => 18500, 'etiqueta' => 'Madera Sostenible', 'sinpe' => false, 'imagen' => 'productos/tabla-teca.jpg'],
        ['id' => 5, 'nombre' => 'Salsa Maracuyá & Habanero',     'emprendimiento' => 'Chileras Don Chepe',          'ubicacion' => 'Atenas, Alajuela',     'precio' => 4200,  'etiqueta' => 'Picante Casero',    'sinpe' => false, 'imagen' => 'productos/salsa-maracuya.jpg'],
        ['id' => 6, 'nombre' => 'Tote Bag de Yute Cafetalero',   'emprendimiento' => 'ReciclaTico Estudio',         'ubicacion' => 'Heredia Centro',       'precio' => 16000, 'etiqueta' => 'Eco-Circular',      'sinpe' => false, 'imagen' => 'productos/tote-yute.jpg'],
        ['id' => 7, 'nombre' => 'Jabón Romero y Cera de Abeja',  'emprendimiento' => 'Botica Botánica del Valle',   'ubicacion' => 'Orosi, Cartago',       'precio' => 6500,  'etiqueta' => '100% Botánico',     'sinpe' => false, 'imagen' => 'productos/jabon-romero.jpg'],
        ['id' => 8, 'nombre' => 'Chocolate Fino 75% Bribri',     'emprendimiento' => 'Cacao Caribeño Ancestral',    'ubicacion' => 'Talamanca, Limón',     'precio' => 3800,  'etiqueta' => 'Cacao Ancestral',   'sinpe' => true,  'imagen' => 'productos/chocolate-bribri.jpg'],
    ];
}

if (!isset($totalProductos)) {
    $totalProductos = 342;
}
if (!isset($paginaActual)) {
    $paginaActual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
}
if (!isset($totalPaginas)) {
    $totalPaginas = 14;
}

// Lo que la persona escogió en los filtros (para dejarlo marcado)
$busqueda           = isset($_GET['q']) ? $_GET['q'] : '';
$provinciaActual    = isset($_GET['provincia']) ? $_GET['provincia'] : '';
$categoriasMarcadas = isset($_GET['categoria']) ? $_GET['categoria'] : [];
if (!is_array($categoriasMarcadas)) {
    // Desde el inicio llega una sola categoría (?categoria=3)
    $categoriasMarcadas = [$categoriasMarcadas];
}
$precioMinimo       = isset($_GET['min']) ? $_GET['min'] : '';
$precioMaximo       = isset($_GET['max']) ? $_GET['max'] : '';
$pagoActual         = isset($_GET['pago']) ? $_GET['pago'] : '';
$entregasMarcadas   = isset($_GET['entrega']) && is_array($_GET['entrega']) ? $_GET['entrega'] : [];

// Productos que el cliente tiene en favoritos (corazón lleno)
if (!isset($favoritosIds)) {
    $favoritosIds = [];
}
$ordenActual        = isset($_GET['orden']) ? $_GET['orden'] : 'recomendados';

// Parte de la URL que se repite en la paginación (búsqueda + provincia + orden)
$filtrosUrl = 'q=' . urlencode($busqueda) . '&provincia=' . urlencode($provinciaActual) . '&orden=' . urlencode($ordenActual);

require __DIR__ . '/../general/header.php';
?>

<main id="contenido">

    <!-- ===== Hero con buscador ===== -->
    <section class="catalogo-hero">
        <div class="contenedor catalogo-hero__contenido">
            <span class="pill"><span class="punto"></span> MERCADO NACIONAL AUTÉNTICO <span class="separador">•</span> 1,420+ Emprendedores Verificados</span>
            <h1 class="catalogo-hero__titulo">Descubrí lo mejor hecho con manos ticas</h1>
            <p class="texto-guia">
                Conectá directamente con artesanos, tostadores de café y productores locales de las 7 provincias
                de Costa Rica. Sin intermediarios, con pago directo por SINPE Móvil.
            </p>

            <form class="buscador" method="get" action="<?php echo e(ruta('catalogo')); ?>" role="search">
                <label class="buscador__campo">
                    <span class="oculto-visual">Buscar productos</span>
                    <?php echo icono('buscar'); ?>
                    <input type="search" name="q" value="<?php echo e($busqueda); ?>" placeholder="Buscá por producto, artesano o provincia (ej. Café Tarrazú)">
                </label>
                <label class="buscador__provincia">
                    <span class="oculto-visual">Provincia</span>
                    <?php echo icono('ubicacion'); ?>
                    <select name="provincia">
                        <option value="">Todas las provincias</option>
                        <?php foreach ($provincias as $provincia): ?>
                            <option value="<?php echo e($provincia); ?>" <?php echo $provinciaActual === $provincia ? 'selected' : ''; ?>><?php echo e($provincia); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <button class="btn btn--oscuro" type="submit">Explorar <?php echo icono('flecha'); ?></button>
            </form>

            <nav class="provincias" aria-label="Filtrar por provincia">
                <span class="provincias__titulo">Provincias:</span>
                <a class="chip <?php echo $provinciaActual === '' ? 'es-activo' : ''; ?>" href="<?php echo e(ruta('catalogo')); ?>">Todas</a>
                <?php foreach ($provincias as $provincia): ?>
                    <a class="chip <?php echo $provinciaActual === $provincia ? 'es-activo' : ''; ?>"
                       href="<?php echo e(ruta('catalogo') . '?provincia=' . urlencode($provincia)); ?>"><?php echo e($provincia); ?></a>
                <?php endforeach; ?>
            </nav>
        </div>
    </section>

    <!-- ===== Catálogo ===== -->
    <section class="contenedor catalogo">

        <!-- Filtros -->
        <aside class="catalogo__lateral">
            <button class="btn btn--borde btn--bloque filtros__abrir" type="button" id="abrirFiltros" aria-expanded="false" aria-controls="formFiltros">
                <?php echo icono('filtros'); ?> <span id="textoFiltros">Mostrar filtros</span>
            </button>

            <form class="filtros tarjeta" id="formFiltros" method="get" action="<?php echo e(ruta('catalogo')); ?>">
                <input type="hidden" name="q" value="<?php echo e($busqueda); ?>">
                <input type="hidden" name="provincia" value="<?php echo e($provinciaActual); ?>">

                <div class="filtros__cabecera">
                    <h2><?php echo icono('filtros'); ?> Filtros Ticos</h2>
                    <a href="<?php echo e(ruta('catalogo')); ?>">Limpiar</a>
                </div>

                <fieldset class="filtros__grupo">
                    <legend>Categorías Populares</legend>
                    <?php foreach ($categoriasFiltro as $categoria): ?>
                        <label class="casilla filtros__opcion">
                            <input type="checkbox" name="categoria[]" value="<?php echo (int) $categoria['id']; ?>"
                                <?php echo in_array((string) $categoria['id'], $categoriasMarcadas) ? 'checked' : ''; ?>>
                            <span><?php echo e($categoria['nombre']); ?></span>
                            <small><?php echo (int) $categoria['cantidad']; ?></small>
                        </label>
                    <?php endforeach; ?>
                </fieldset>

                <fieldset class="filtros__grupo">
                    <legend>Rango de Precio (Colones ₡)</legend>
                    <div class="rango">
                        <label>
                            <span>Mínimo</span>
                            <input type="number" name="min" min="0" step="500" placeholder="₡2,500" value="<?php echo e($precioMinimo); ?>">
                        </label>
                        <span class="rango__guion" aria-hidden="true">–</span>
                        <label>
                            <span>Máximo</span>
                            <input type="number" name="max" min="0" step="500" placeholder="₡25,000" value="<?php echo e($precioMaximo); ?>">
                        </label>
                    </div>
                </fieldset>

                <fieldset class="filtros__grupo">
                    <legend>Método de Pago Preferido</legend>
                    <?php foreach ($metodosPago as $clave => $texto): ?>
                        <label class="casilla filtros__opcion <?php echo $pagoActual === $clave ? 'es-marcada' : ''; ?>">
                            <input type="radio" name="pago" value="<?php echo e($clave); ?>" <?php echo $pagoActual === $clave ? 'checked' : ''; ?>>
                            <span>
                                <?php echo e($texto); ?>
                                <?php if ($clave === 'sinpe'): ?>
                                    <small class="filtros__nota">Pago directo al artesano</small>
                                <?php endif; ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </fieldset>

                <fieldset class="filtros__grupo">
                    <legend>Entrega y Envíos</legend>
                    <?php foreach ($metodosEntrega as $clave => $texto): ?>
                        <label class="casilla filtros__opcion">
                            <input type="checkbox" name="entrega[]" value="<?php echo e($clave); ?>"
                                <?php echo in_array($clave, $entregasMarcadas) ? 'checked' : ''; ?>>
                            <span><?php echo e($texto); ?></span>
                        </label>
                    <?php endforeach; ?>
                </fieldset>

                <button class="btn btn--oscuro btn--bloque btn--sm" type="submit">Aplicar filtros</button>
            </form>

            <div class="cta-lateral">
                <?php echo icono('verificado'); ?>
                <h2>¿Tenés un emprendimiento?</h2>
                <p>Sumá tu catálogo artesanal a Ventica y vendé a miles de personas en todo Costa Rica.</p>
                <a class="btn btn--sm btn--blanco" href="<?php echo e(ruta('auth/registro-vendedor')); ?>">Abrir mi Vitrina <?php echo icono('flecha'); ?></a>
            </div>
        </aside>

        <!-- Resultados -->
        <div class="catalogo__resultados">
            <div class="resultados__barra tarjeta">
                <h2>
                    Catálogo Destacado
                    <span class="insignia"><?php echo count($productos); ?> de <?php echo (int) $totalProductos; ?> productos</span>
                </h2>
                <form class="ordenar" id="formOrden" method="get" action="<?php echo e(ruta('catalogo')); ?>">
                    <input type="hidden" name="q" value="<?php echo e($busqueda); ?>">
                    <input type="hidden" name="provincia" value="<?php echo e($provinciaActual); ?>">
                    <label for="orden">Ordenar por:</label>
                    <select id="orden" name="orden">
                        <?php foreach ($opcionesOrden as $clave => $texto): ?>
                            <option value="<?php echo e($clave); ?>" <?php echo $ordenActual === $clave ? 'selected' : ''; ?>><?php echo e($texto); ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <?php if (count($productos) === 0): ?>
                <div class="sin-resultados tarjeta">
                    <?php echo icono('buscar'); ?>
                    <h3>No encontramos productos con esos filtros</h3>
                    <p class="texto-guia">Probá con otra provincia o quitá algunos filtros.</p>
                    <a class="btn btn--borde" href="<?php echo e(ruta('catalogo')); ?>">Ver todo el catálogo</a>
                </div>
            <?php else: ?>
                <ul class="productos">
                    <?php foreach ($productos as $producto): ?>
                        <?php $urlDetalle = ruta('producto/detalle') . '?id=' . (int) $producto['id']; ?>
                        <li>
                            <article class="producto">
                                <div class="producto__media">
                                    <a href="<?php echo e($urlDetalle); ?>" tabindex="-1" aria-hidden="true">
                                        <img src="<?php echo e(recurso('img/uploads/' . $producto['imagen'])); ?>" alt="" width="206" height="206">
                                    </a>
                                    <div class="producto__etiquetas">
                                        <?php if ($producto['etiqueta'] !== ''): ?>
                                            <span class="producto__etiqueta"><?php echo e($producto['etiqueta']); ?></span>
                                        <?php endif; ?>
                                        <?php if ($producto['sinpe']): ?>
                                            <span class="producto__sinpe"><?php echo icono('check'); ?> SINPE</span>
                                        <?php endif; ?>
                                    </div>
                                    <form class="producto__favorito-form" method="post" action="<?php echo e(ruta('favoritos/alternar')); ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                                    <input type="hidden" name="id_producto" value="<?php echo (int) $producto['id']; ?>">
                                    <?php $esFavorito = in_array((int) $producto['id'], $favoritosIds, true); ?>
                                    <button class="producto__favorito <?php echo $esFavorito ? 'es-favorito' : ''; ?>" type="submit" aria-pressed="<?php echo $esFavorito ? 'true' : 'false'; ?>" aria-label="<?php echo $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos'; ?>">
                                            <?php echo icono('corazon'); ?>
                                        </button>
                                    </form>
                                </div>

                                <div class="producto__cuerpo">
                                    <p class="producto__ubicacion"><?php echo icono('ubicacion'); ?> <?php echo e($producto['ubicacion']); ?></p>
                                    <h3 class="producto__nombre">
                                        <a href="<?php echo e($urlDetalle); ?>"><?php echo e($producto['nombre']); ?></a>
                                    </h3>
                                    <p class="producto__emprendimiento"><?php echo e($producto['emprendimiento']); ?></p>

                                    <div class="producto__pie">
                                        <p class="producto__precio"><small>Precio</small> <?php echo colones($producto['precio']); ?></p>
                                        <form method="post" action="<?php echo e(ruta('carrito/agregar')); ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                                            <input type="hidden" name="id_producto" value="<?php echo (int) $producto['id']; ?>">
                                            <input type="hidden" name="cantidad" value="1">
                                            <button class="btn btn--oscuro btn--sm" type="submit" aria-label="Pedir <?php echo e($producto['nombre']); ?>">
                                                Pedir <?php echo icono('rayo'); ?>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Paginación: 1 2 3 … 14  (con un for, como en los ejemplos de clase) -->
                <?php if ($totalPaginas > 1): ?>
                    <nav class="paginacion" aria-label="Paginación del catálogo">
                        <?php if ($paginaActual > 1): ?>
                            <a href="<?php echo e(ruta('catalogo') . '?' . $filtrosUrl . '&pagina=' . ($paginaActual - 1)); ?>" aria-label="Página anterior"><?php echo icono('izquierda'); ?></a>
                        <?php endif; ?>

                        <?php
                        for ($i = 1; $i <= $totalPaginas; $i++) {
                            // Se muestran las 3 primeras, la actual y la última; el resto se resume con "…"
                            if ($i <= 3 || $i === $paginaActual || $i === $totalPaginas) {
                                $clase = ($i === $paginaActual) ? 'es-activo' : '';
                                $url = ruta('catalogo') . '?' . $filtrosUrl . '&pagina=' . $i;
                                echo '<a class="' . $clase . '" href="' . e($url) . '">' . $i . '</a>';
                            } elseif ($i === 4 || $i === $totalPaginas - 1) {
                                echo '<span class="paginacion__puntos">…</span>';
                            }
                        }
                        ?>

                        <?php if ($paginaActual < $totalPaginas): ?>
                            <a href="<?php echo e(ruta('catalogo') . '?' . $filtrosUrl . '&pagina=' . ($paginaActual + 1)); ?>" aria-label="Página siguiente"><?php echo icono('derecha'); ?></a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== CTA comunidad ===== -->
    <section class="contenedor">
        <div class="cta-comunidad">
            <div>
                <p class="sobretitulo sobretitulo--lima">Comunidad artesanal</p>
                <h2>Apoyemos el talento de nuestros pueblos</h2>
                <p>
                    Cada compra en Ventica activa la economía local, preserva tradiciones costarricenses
                    y genera empleo digno para familias campesinas e indígenas.
                </p>
            </div>
            <div class="cta-comunidad__acciones">
                <a class="btn btn--lima" href="<?php echo e(ruta('auth/registro-vendedor')); ?>">Registrar mi Taller Gratis</a>
                <a class="btn btn--transparente" href="<?php echo e(ruta('historias')); ?>">Conocer más historias</a>
            </div>
        </div>
    </section>

</main>

<?php require __DIR__ . '/../general/footer.php'; ?>
