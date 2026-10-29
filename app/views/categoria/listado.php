<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 
 * Admin · Gestión de Categorías 
 * Ruta: admin/categorias
 
 * Tarjetas con las categorías del catálogo, sus productos y marcas.
 * El administrador crea, edita y elimina categorías. No se puede eliminar una categoría que tenga productos.
 */

$titulo       = 'Gestión de Categorías · Administración';
$estilos      = ['admin', 'admin_gestion'];
$scripts      = ['validaciones', 'categoria'];
$navbar       = false;
$footer       = false;
$claseBody    = 'body-panel';
$seccionPanel = 'categorias';

if (!isset($categorias)) {
    $categorias = [
        ['nombre' => 'Ropa',       'descripcion' => 'Diseño textil, indumentaria y confección local costarricense.',          'productos' => 342, 'marcas' => 48, 'artesanal' => true],
        ['nombre' => 'Accesorios', 'descripcion' => 'Joyería artesanal, bolsos, marroquinería y detalles de moda.',            'productos' => 415, 'marcas' => 62, 'artesanal' => false],
        ['nombre' => 'Crochet',    'descripcion' => 'Tejido a mano tradicional, amigurumis, tapices e hilados ticos.',         'productos' => 210, 'marcas' => 35, 'artesanal' => true],
        ['nombre' => 'Arte',       'descripcion' => 'Pintura, cerámica, ilustración botánica e impresiones originales.',      'productos' => 290, 'marcas' => 41, 'artesanal' => true],
        ['nombre' => 'Hogar',      'descripcion' => 'Decoración, macetas, velas de cera de soya y artículos de madera.',      'productos' => 380, 'marcas' => 54, 'artesanal' => true],
        ['nombre' => 'Comida',     'descripcion' => 'Café de especialidad, salsas artesanales, miel y repostería criolla.',   'productos' => 520, 'marcas' => 73, 'artesanal' => false],
        ['nombre' => 'Servicios',  'descripcion' => 'Talleres presenciales, personalizaciones y asesorías profesionales.',    'productos' => 115, 'marcas' => 29, 'artesanal' => false],
        ['nombre' => 'Belleza',    'descripcion' => 'Cosmética natural limpia, jabones orgánicos y aceites esenciales.',      'productos' => 265, 'marcas' => 38, 'artesanal' => false],
    ];
}

$totalCategorias = count($categorias);

// Totales para las tarjetas de arriba 
$totalProductos = 0;
$totalMarcas    = 0;
for ($i = 0; $i < $totalCategorias; $i++) {
    $totalProductos = $totalProductos + $categorias[$i]['productos'];
    $totalMarcas    = $totalMarcas + $categorias[$i]['marcas'];
}

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
                    <p class="ad-etiquetas"><span class="ad-sobretitulo ad-sobretitulo--verde"><?php echo icono('filtros'); ?> Catálogo maestro</span></p>
                    <h1 class="ad-titulo">Gestión de Categorías</h1>
                    <p class="ad-descripcion">Organización del catálogo comercial de emprendimientos costarricenses. Define taxonomías, supervisa la distribución artesanal y ajusta los sectores clave del comercio local.</p>
                </div>
                <button class="btn ad-boton-verde" type="button" id="botonNuevaCategoria"><?php echo icono('mas'); ?> Nueva categoría</button>
            </section>

            <!-- Indicadores -->
            <ul class="ad-indicadores ad-indicadores--cuatro">
                <li class="ad-indicador">
                    <span class="ad-indicador__icono ad-indicador__icono--azul"><?php echo icono('cuadricula'); ?></span>
                    <span><small>Categorías Activas</small><strong><span id="contadorCategorias"><?php echo $totalCategorias; ?></span> Sectores</strong></span>
                </li>
                <li class="ad-indicador">
                    <span class="ad-indicador__icono ad-indicador__icono--teal"><?php echo icono('bolsa'); ?></span>
                    <span><small>Productos Totales</small><strong><?php echo number_format($totalProductos, 0, '.', ','); ?> Ítems</strong></span>
                </li>
                <li class="ad-indicador">
                    <span class="ad-indicador__icono ad-indicador__icono--verde"><?php echo icono('verificado'); ?></span>
                    <span><small>Marcas Registradas</small><strong><?php echo $totalMarcas; ?> Ticas</strong></span>
                </li>
                <li class="ad-indicador">
                    <span class="ad-indicador__icono"><?php echo icono('tendencia'); ?></span>
                    <span><small>Mayor Crecimiento</small><strong>Comida (+18%)</strong></span>
                </li>
            </ul>

            <!-- Mensaje de confirmación o de error (lo muestra categoria.js) -->
            <div id="mensajeCategoria" class="mensaje mensaje--exito ad-oculto" role="status">
                <p id="textoMensajeCategoria"></p>
            </div>

            <!-- Formulario nueva / editar categoría (aparece al tocar "Nueva categoría" o "Editar") -->
            <section class="tarjeta ad-formulario ad-oculto" id="panelFormulario">
                <h2 class="titulo-seccion" id="tituloFormulario">Nueva categoría</h2>

                <form class="formulario" id="formCategoria" method="post" action="<?php echo e(ruta('admin/categorias/guardar')); ?>" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                    <!-- -1 = categoría nueva; si es un número, es la posición de la categoría que se edita -->
                    <input type="hidden" id="indiceEdicion" name="indice" value="-1">
                    <!-- Id de la categoría en la base de datos (0 = nueva) -->
                    <input type="hidden" id="idCategoriaEdicion" name="id_categoria" value="0">

                    <div class="ad-formulario__campos">
                        <div id="campo-nombreCategoria" class="campo">
                            <label class="campo__etiqueta" for="nombreCategoria">Nombre</label>
                            <input class="campo__entrada" id="nombreCategoria" name="nombre" type="text" maxlength="40" placeholder="Ej. Joyería">
                            <p class="campo__error" id="errorNombreCategoria">Escribí un nombre de al menos 3 letras.</p>
                        </div>
                        <div class="campo">
                            <label class="campo__etiqueta" for="descripcionCategoria">Descripción</label>
                            <input class="campo__entrada" id="descripcionCategoria" name="descripcion" type="text" maxlength="120" placeholder="Qué tipo de productos incluye">
                        </div>
                        <div class="campo">
                            <label class="campo__etiqueta" for="tipoCategoria">Tipo</label>
                            <select class="campo__entrada" id="tipoCategoria" name="artesanal">
                                <option value="1">Artesanal</option>
                                <option value="0">Comercial</option>
                            </select>
                        </div>
                    </div>

                    <div class="ad-formulario__botones">
                        <button class="btn btn--texto" type="button" id="cancelarCategoria">Cancelar</button>
                        <button class="btn ad-boton-verde" type="button" id="guardarCategoria"><span id="textoGuardar">Agregar categoría</span></button>
                    </div>
                </form>
            </section>

            <!-- Filtros por actividad -->
            <div class="ad-barra-filtros solo-pantalla">
                <span class="ad-barra-filtros__titulo"><?php echo icono('filtros'); ?> Filtrar por actividad:</span>
                <button class="ad-chip es-activo" type="button" id="filtroTodas"><span>Todas (<span id="contadorFiltro"><?php echo $totalCategorias; ?></span>)</span></button>
                <button class="ad-chip" type="button" id="filtroMayor">Mayor catálogo</button>
                <button class="ad-chip" type="button" id="filtroArtesanales">Artesanales</button>
                <span class="ad-en-vivo"><span class="punto"></span> Actualización automática en tiempo real</span>
            </div>

            <!-- Tarjetas (las arma PHP con un for) -->
            <input type="hidden" id="totalCategorias" value="<?php echo $totalCategorias; ?>">

            <!-- Formulario escondido para eliminar: categoria.js le pone el id -->
            <form id="formEliminarCategoria" method="post" action="<?php echo e(ruta('admin/categorias/eliminar')); ?>" hidden>
                <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                <input type="hidden" name="id_categoria" id="idCategoriaEliminar" value="">
            </form>

            <div class="ad-categorias" id="listaCategorias">
                <?php for ($i = 0; $i < $totalCategorias; $i++): ?>
                    <?php
                    $categoria = $categorias[$i];

                    // Ícono según la categoría (switch)
                    switch ($categoria['nombre']) {
                        case 'Ropa':
                            $icono = 'camisa';
                            break;
                        case 'Accesorios':
                            $icono = 'bolsa';
                            break;
                        case 'Crochet':
                            $icono = 'tijeras';
                            break;
                        case 'Arte':
                            $icono = 'destellos';
                            break;
                        case 'Hogar':
                            $icono = 'casa';
                            break;
                        case 'Comida':
                            $icono = 'cafe';
                            break;
                        case 'Servicios':
                            $icono = 'trato';
                            break;
                        default:
                            $icono = 'hoja';
                            break;
                    }

                    // Si la categoría trae su propio ícono (de la base de datos), se usa ese
                    if (!empty($categoria['icono'])) {
                        $icono = $categoria['icono'];
                    }

                    // Número de la categoría con dos dígitos: #01, #02...
                    $numero = $i + 1;
                    if ($numero < 10) {
                        $numero = '0' . $numero;
                    }
                    ?>
                    <article class="ad-categoria" id="categoria-<?php echo $i; ?>">
                        <div class="ad-categoria__arriba">
                            <span class="ad-categoria__icono"><?php echo icono($icono); ?></span>
                            <div>
                                <h3><span id="nombre-<?php echo $i; ?>"><?php echo e($categoria['nombre']); ?></span> <span class="ad-chip ad-chip--suave">#<?php echo $numero; ?></span></h3>
                                <p id="descripcion-<?php echo $i; ?>"><?php echo e($categoria['descripcion']); ?></p>
                            </div>
                        </div>

                        <div class="ad-categoria__datos">
                            <span><?php echo icono('caja'); ?> <strong id="productosTexto-<?php echo $i; ?>"><?php echo $categoria['productos']; ?></strong> productos</span>
                            <span><?php echo icono('verificado'); ?> <strong><?php echo $categoria['marcas']; ?></strong> marcas ticas</span>
                        </div>

                        <div class="ad-categoria__pie">
                            <span class="ad-en-vivo"><span class="punto"></span> Visible en vitrina</span>
                            <div class="solo-pantalla">
                                <button class="ad-boton ad-boton--azul" type="button" id="editar-<?php echo $i; ?>"><?php echo icono('lapiz'); ?> Editar</button>
                                <button class="ad-boton ad-boton--peligro" type="button" id="eliminar-<?php echo $i; ?>"><?php echo icono('basura'); ?> Eliminar</button>
                            </div>
                        </div>

                        <input type="hidden" id="productos-<?php echo $i; ?>" value="<?php echo $categoria['productos']; ?>">
                        <input type="hidden" id="artesanal-<?php echo $i; ?>" value="<?php echo $categoria['artesanal'] ? '1' : '0'; ?>">
                        <input type="hidden" id="id-<?php echo $i; ?>" value="<?php echo (int) valor($categoria, 'id', 0); ?>">
                    </article>
                <?php endfor; ?>
            </div>

            <!-- Íconos que categoria.js copia al crear una tarjeta nueva -->
            <span class="ad-oculto" id="iconoNueva"><?php echo icono('caja'); ?></span>
            <span class="ad-oculto" id="iconoProductos"><?php echo icono('caja'); ?></span>
            <span class="ad-oculto" id="iconoMarcas"><?php echo icono('verificado'); ?></span>
            <span class="ad-oculto" id="iconoEditar"><?php echo icono('lapiz'); ?></span>
            <span class="ad-oculto" id="iconoEliminar"><?php echo icono('basura'); ?></span>

        </main>
    </div>
</div>

<?php require __DIR__ . '/../general/footer.php'; ?>
