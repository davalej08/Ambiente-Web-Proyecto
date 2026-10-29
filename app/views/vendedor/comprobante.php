<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/**
 * Comprobante de un pedido para imprimir o guardar en PDF (portal del vendedor)
 * Ruta: vendedor/pedidos/imprimir?id=
 *
 * Solo muestra las líneas del emprendimiento que lo imprime. imprimir.js abre
 * la ventana de impresión del navegador ("Guardar como PDF").
 *
 * Variables que envía VendedorController:
 *   $encabezado  datos del pedido y de la dirección de entrega
 *   $lineas      productos del vendedor dentro del pedido
 *   $total       suma de las líneas
 *   $tienda      ['nombre', 'ubicacion']
 */

$titulo  = 'Comprobante #CR-' . (int) $encabezado['id_pedido'];
$estilos = ['paginas'];
$scripts = ['imprimir'];
$navbar  = false;
$footer  = false;

$metodos = ['sinpe' => 'SINPE Móvil', 'tarjeta' => 'Tarjeta', 'efectivo' => 'Efectivo contra entrega'];

require __DIR__ . '/../general/header.php';
?>

<main id="contenido" class="comprobante">
    <div class="comprobante__acciones solo-pantalla">
        <a class="btn btn--texto" href="<?php echo e(ruta('vendedor/pedidos') . '?id=' . (int) $encabezado['id_pedido']); ?>"><?php echo icono('izquierda'); ?> Volver a pedidos</a>
        <button class="btn btn--oscuro" type="button" id="botonImprimir"><?php echo icono('recibo'); ?> Imprimir o guardar PDF</button>
    </div>

    <article class="tarjeta comprobante__hoja">
        <header class="comprobante__cabecera">
            <div>
                <img src="<?php echo e(recurso('img/logo-ventica.png')); ?>" alt="Ventica" width="110" height="38">
                <p class="texto-guia"><?php echo e($tienda['nombre']); ?> · <?php echo e($tienda['ubicacion']); ?></p>
            </div>
            <div class="comprobante__numero">
                <p class="sobretitulo">Comprobante de pedido</p>
                <h1>#CR-<?php echo (int) $encabezado['id_pedido']; ?></h1>
                <p><?php echo e(fechaCorta($encabezado['fecha_pedido'])); ?> · <?php echo e(horaCorta($encabezado['fecha_pedido'])); ?></p>
            </div>
        </header>

        <section class="comprobante__datos">
            <div>
                <p class="campo__etiqueta">Entregar a</p>
                <p><strong><?php echo e($encabezado['nombre_receptor']); ?></strong> · <?php echo e($encabezado['telefono']); ?></p>
                <p><?php echo e($encabezado['canton'] . ', ' . $encabezado['provincia']); ?></p>
                <p><?php echo e($encabezado['direccion_exacta']); ?></p>
            </div>
            <div>
                <p class="campo__etiqueta">Forma de pago</p>
                <p><?php echo e(valor($metodos, $encabezado['metodo_pago'], $encabezado['metodo_pago'])); ?></p>
                <p class="campo__etiqueta">Envío</p>
                <p>Correos de Costa Rica</p>
            </div>
        </section>

        <table class="tabla comprobante__tabla">
            <thead>
                <tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr>
            </thead>
            <tbody>
                <?php foreach ($lineas as $linea): ?>
                    <tr>
                        <td><?php echo e($linea['nombre']); ?></td>
                        <td><?php echo (int) $linea['cantidad']; ?></td>
                        <td><?php echo colones($linea['precio_unitario']); ?></td>
                        <td><?php echo colones($linea['subtotal']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><th colspan="3">Total para <?php echo e($tienda['nombre']); ?></th><th><?php echo colones($total); ?></th></tr>
            </tfoot>
        </table>

        <p class="comprobante__pie">Gracias por comprar local. Este comprobante no sustituye la factura electrónica del emprendimiento.</p>
    </article>
</main>

<?php require __DIR__ . '/../general/footer.php'; ?>
