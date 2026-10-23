<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Agregar / editar producto (RF-12) (Figma: Vendedor · Agregar Producto)
 * Rutas sugeridas: vendedor/productos/nuevo  y  vendedor/productos/editar?id=
 *
 * Variables que puede enviar VendedorController:
 *   $producto    datos del producto a editar (si no llega, el formulario es para uno nuevo)
 *   $categorias  arreglo de categorías ['id' => ..., 'nombre' => ...]
 *   $errores     errores de validación del servidor ['campo' => 'mensaje']
 */

$titulo       = 'Agregar producto · Portal del emprendedor';
$estilos      = ['vendedor'];
$scripts      = ['validaciones', 'vendedor'];
$navbar       = false;
$footer       = false;
$claseBody    = 'body-panel';
$seccionPanel = 'productos';

if (!isset($categorias)) {
    $categorias = [
        ['id' => 1, 'nombre' => 'Café de Especialidad'],
        ['id' => 2, 'nombre' => 'Artesanías y Maderas'],
        ['id' => 3, 'nombre' => 'Gourmet & Mieles'],
        ['id' => 4, 'nombre' => 'Cosmética & Jabones'],
        ['id' => 5, 'nombre' => 'Joyería Artesanal'],
        ['id' => 6, 'nombre' => 'Hogar y Textiles'],
    ];
}
if (!isset($producto)) {
    $producto = [];
}
if (!isset($errores)) {
    $errores = [];
}

$esEdicion = isset($producto['id']);
if ($esEdicion) {
    $titulo = 'Editar producto · Portal del emprendedor';
}

$maxDescripcion = 280;

require __DIR__ . '/../general/header.php';
?>

<div class="panel">
    <?php require __DIR__ . '/../general/sidebar_vendedor.php'; ?>

    <div class="panel__principal">
        <?php require __DIR__ . '/../general/barra_vendedor.php'; ?>

        <main id="contenido" class="panel__contenido panel__contenido--angosto">

            <div class="formulario-producto__barra">
                <a class="chip" href="<?php echo e(ruta('vendedor/productos')); ?>"><?php echo icono('izquierda'); ?> Volver a Mis Productos</a>
            </div>

            <div class="encabezado-pagina">
                <p class="sobretitulo sobretitulo--linea">Taller artesanal</p>
                <h1 class="titulo-pagina"><?php echo $esEdicion ? 'Editar producto' : 'Agregar nuevo producto'; ?></h1>
                <p class="texto-guia">Publicá tus creaciones en el catálogo de Ventica.</p>
            </div>

            <form class="tarjeta formulario formulario-producto" id="formProducto" method="post" enctype="multipart/form-data"
                  action="<?php echo e(ruta($esEdicion ? 'vendedor/productos/actualizar' : 'vendedor/productos/guardar')); ?>" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                <?php if ($esEdicion): ?>
                    <input type="hidden" name="id_producto" value="<?php echo (int) $producto['id']; ?>">
                <?php endif; ?>

                <!-- Foto -->
                <div class="campo <?php echo claseError($errores, 'imagen'); ?>" id="campo-imagen">
                    <div class="campo__cabecera">
                        <span class="campo__etiqueta" id="etiquetaImagen">Foto principal del producto</span>
                        <span class="campo__nota">JPG, PNG o WEBP (máx. 5 MB)</span>
                    </div>
                    <label class="subir-foto" id="zonaFoto" for="imagen">
                        <input class="oculto-visual" type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp" aria-labelledby="etiquetaImagen">
                        <img class="subir-foto__vista" id="vistaFoto" alt="Vista previa de la foto"
                             <?php echo !empty($producto['imagen']) ? 'src="' . e(recurso('img/uploads/' . $producto['imagen'])) . '"' : 'hidden'; ?>>
                        <span class="subir-foto__guia" id="guiaFoto" <?php echo !empty($producto['imagen']) ? 'hidden' : ''; ?>>
                            <span class="subir-foto__icono"><?php echo icono('imagen'); ?></span>
                            <strong>Arrastrá o seleccioná la foto del producto</strong>
                            <small>Recomendamos luz natural y fondo neutro para resaltar tu arte tico</small>
                        </span>
                    </label>
                    <p class="campo__error" id="errorImagen"><?php echo e(valor($errores, 'imagen', 'Subí una foto JPG, PNG o WEBP de máximo 5 MB.')); ?></p>
                </div>

                <!-- Nombre -->
                <div class="campo <?php echo claseError($errores, 'nombre'); ?>" id="campo-nombre">
                    <label class="campo__etiqueta" for="nombre">Nombre del producto</label>
                    <input class="campo__entrada" type="text" id="nombre" name="nombre" maxlength="120"
                           value="<?php echo e(valor($producto, 'nombre', '')); ?>"
                           placeholder="Ej: Chorreador tradicional de madera de cedro">
                    <p class="campo__nota">Utilizá un nombre descriptivo y evocador para tus clientes.</p>
                    <p class="campo__error"><?php echo e(valor($errores, 'nombre', 'Escribí un nombre de al menos 3 letras.')); ?></p>
                </div>

                <!-- Categoría -->
                <div class="campo <?php echo claseError($errores, 'categoria'); ?>" id="campo-categoria">
                    <label class="campo__etiqueta" for="categoria">Categoría</label>
                    <select class="campo__entrada" id="categoria" name="id_categoria">
                        <option value="">Seleccioná una categoría</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo (int) $categoria['id']; ?>"
                                <?php echo (int) valor($producto, 'id_categoria', 0) === (int) $categoria['id'] ? 'selected' : ''; ?>>
                                <?php echo e($categoria['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="campo__error"><?php echo e(valor($errores, 'categoria', 'Seleccioná una categoría.')); ?></p>
                </div>

                <!-- Precio y stock -->
                <div class="rejilla-2">
                    <div class="campo <?php echo claseError($errores, 'precio'); ?>" id="campo-precio">
                        <label class="campo__etiqueta" for="precio">Precio en colones</label>
                        <div class="campo__grupo">
                            <span class="campo__prefijo">₡</span>
                            <input class="campo__entrada" type="number" id="precio" name="precio" min="100" step="50" inputmode="numeric"
                                   value="<?php echo e(valor($producto, 'precio', '')); ?>" placeholder="15000">
                        </div>
                        <p class="campo__nota">Precio final para el comprador</p>
                        <p class="campo__error"><?php echo e(valor($errores, 'precio', 'El precio debe ser de al menos ₡100.')); ?></p>
                    </div>

                    <div class="campo <?php echo claseError($errores, 'stock'); ?>" id="campo-stock">
                        <label class="campo__etiqueta" for="stock">Cantidad en stock</label>
                        <div class="campo__grupo">
                            <input class="campo__entrada" type="number" id="stock" name="stock" min="0" step="1" inputmode="numeric"
                                   value="<?php echo e(valor($producto, 'stock', '')); ?>" placeholder="10">
                            <span class="campo__prefijo campo__sufijo">unidades</span>
                        </div>
                        <p class="campo__nota">Disponibilidad para entrega inmediata</p>
                        <p class="campo__error"><?php echo e(valor($errores, 'stock', 'Indicá una cantidad entera de 0 o más.')); ?></p>
                    </div>
                </div>

                <!-- Descripción -->
                <div class="campo <?php echo claseError($errores, 'descripcion'); ?>" id="campo-descripcion">
                    <div class="campo__cabecera">
                        <label class="campo__etiqueta" for="descripcion">Descripción breve</label>
                        <span class="campo__nota" id="contadorDescripcion">0 / <?php echo (int) $maxDescripcion; ?></span>
                    </div>
                    <textarea class="campo__entrada" id="descripcion" name="descripcion" rows="4" maxlength="<?php echo (int) $maxDescripcion; ?>"
                              placeholder="Compartí la historia detrás de la pieza: materiales de origen local, técnica artesanal, inspiración o sugerencias de cuidado…"><?php echo e(valor($producto, 'descripcion', '')); ?></textarea>
                    <p class="campo__error"><?php echo e(valor($errores, 'descripcion', 'Escribí una descripción de al menos 20 caracteres.')); ?></p>
                </div>

                <div class="aviso-compromiso">
                    <span class="aviso-compromiso__icono"><?php echo icono('hoja'); ?></span>
                    <p><strong>Compromiso nacional</strong> Al publicar, confirmás que este artículo cumple las normas de origen local y manufactura ética tica.</p>
                </div>

                <div class="formulario-producto__acciones">
                    <a class="btn btn--texto" href="<?php echo e(ruta('vendedor/productos')); ?>">Cancelar</a>
                    <button class="btn btn--primario" type="submit"><?php echo icono('check'); ?> Guardar producto</button>
                </div>
            </form>

            <p class="formulario-producto__ayuda">
                ¿Dudas sobre empaque o envíos dentro del país?
                <a href="<?php echo e(ruta('ayuda')); ?>">Guía para emprendedores</a>
            </p>

        </main>
    </div>
</div>

<?php require __DIR__ . '/../general/footer.php'; ?>
