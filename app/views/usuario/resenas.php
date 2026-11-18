<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/**
 * Pantalla: Calificá tu compra (HU-04)
 * Ruta: usuario/resenas?pedido=<id>   (botón "Calificar" en Mi perfil > Mis pedidos)
 *
 * Variables que manda ClienteController::resenas():
 *   $idPedido   número del pedido
 *   $productos  productos ENTREGADOS del pedido:
 *               ['id', 'nombre', 'imagen', 'emprendimiento', 'calificacion' (0 = sin reseña), 'comentario']
 *
 * Las estrellas son 5 radio buttons (de 5 a 1). Con CSS se pintan las estrellas
 * hasta la que se marcó, sin JavaScript.
 */

$titulo       = 'Calificá tu compra';
$descripcion  = 'Contale a otras personas cómo te fue con tu compra.';
$estilos      = ['usuario'];
$paginaActiva = 'perfil';

$textoEstrellas = [1 => 'Malo', 2 => 'Regular', 3 => 'Bueno', 4 => 'Muy bueno', 5 => 'Excelente'];

require __DIR__ . '/../general/header.php';
?>

<main id="contenido" class="contenedor seccion resenas-cliente">
    <header class="encabezado-pagina">
        <a class="enlace-volver" href="<?php echo e(ruta('usuario/perfil') . '#pedidos'); ?>"><?php echo icono('izquierda'); ?> Mis pedidos</a>
        <p class="sobretitulo"><?php echo icono('estrella'); ?> Pedido #<?php echo (int) $idPedido; ?></p>
        <h1 class="titulo-pagina">Calificá tu compra</h1>
        <p class="texto-guia">Tu opinión aparece en la vitrina del emprendimiento y ayuda a otras personas a comprar local.</p>
    </header>

    <ul class="resenas-cliente__lista">
        <?php foreach ($productos as $producto): ?>
            <li class="tarjeta resena-producto">
                <img class="resena-producto__foto" src="<?php echo e(recurso('img/uploads/' . $producto['imagen'])); ?>" alt="" width="96" height="96">

                <form class="resena-producto__formulario" method="post" action="<?php echo e(ruta('usuario/resenas/guardar')); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                    <input type="hidden" name="id_pedido" value="<?php echo (int) $idPedido; ?>">
                    <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">

                    <p class="campo__nota"><?php echo e($producto['emprendimiento']); ?></p>
                    <h2 class="resena-producto__nombre"><?php echo e($producto['nombre']); ?></h2>

                    <fieldset class="calificar">
                        <legend class="campo__etiqueta">
                            <?php echo $producto['calificacion'] > 0 ? 'Tu calificación' : '¿Cuántas estrellas le das?'; ?>
                        </legend>
                        <div class="calificar__estrellas">
                            <?php for ($n = 5; $n >= 1; $n--): ?>
                                <input type="radio" id="estrella-<?php echo $producto['id'] . '-' . $n; ?>" name="calificacion" value="<?php echo $n; ?>"
                                       <?php echo $producto['calificacion'] === $n ? 'checked' : ''; ?> required>
                                <label for="estrella-<?php echo $producto['id'] . '-' . $n; ?>" title="<?php echo e($textoEstrellas[$n]); ?>">
                                    <?php echo icono('estrella'); ?>
                                    <span class="oculto-visual"><?php echo $n . ' de 5: ' . e($textoEstrellas[$n]); ?></span>
                                </label>
                            <?php endfor; ?>
                        </div>
                    </fieldset>

                    <div class="campo">
                        <label class="campo__etiqueta" for="comentario-<?php echo $producto['id']; ?>">Comentario <span class="campo__nota">(opcional)</span></label>
                        <textarea class="campo__entrada" id="comentario-<?php echo $producto['id']; ?>" name="comentario" rows="2" maxlength="500"
                                  placeholder="¿Qué te gustó? ¿Llegó bien?"><?php echo e($producto['comentario']); ?></textarea>
                    </div>

                    <button class="btn btn--primario btn--sm" type="submit">
                        <?php echo $producto['calificacion'] > 0 ? 'Actualizar reseña' : 'Publicar reseña'; ?>
                    </button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</main>

<?php require __DIR__ . '/../general/footer.php'; ?>
