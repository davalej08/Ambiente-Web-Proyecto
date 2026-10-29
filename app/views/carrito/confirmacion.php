<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Confirmación del pedido (RF-14)
 */

/**
 * Pantalla: Pedido realizado (después de "Confirmar pedido" en el carrito)
 * Ruta sugerida: carrito/confirmacion   (el POST de carrito/checkout redirige aquí)
 *
 * Variables que puede enviar CarritoController:
 *   $pedido   ['id', 'fecha', 'metodo', 'envio', 'grupos' => [...], 'entrega' => [...]]
 * Si no llega, se usan datos de muestra para el Avance 2.
 */

$titulo       = 'Pedido realizado';
$descripcion  = 'Tu pedido en Ventica fue recibido.';
$estilos      = ['confirmacion'];
$scripts      = ['confirmacion'];
$paginaActiva = '';

if (!isset($pedido)) {
    $pedido = [
        'id'     => 1049,
        'fecha'  => '28 de octubre de 2026',
        'metodo' => 'sinpe',
        'envio'  => 2400,
        'grupos' => [
            [
                'emprendimiento' => 'Beneficio La Pastora',
                'dueno'          => 'Don Mincho Valverde',
                'sinpe'          => '8842-9011',
                'productos'      => [
                    ['nombre' => 'Café Geisha Reserva Especial', 'cantidad' => 1, 'precio' => 9800,  'imagen' => 'productos/cafe-geisha.jpg'],
                    ['nombre' => 'Tabla de Madera Teca Noble',   'cantidad' => 1, 'precio' => 12000, 'imagen' => 'productos/tabla-teca.jpg'],
                ],
            ],
            [
                'emprendimiento' => 'Apiario Montes del Aguacate',
                'dueno'          => 'Laura Montes',
                'sinpe'          => '8711-3420',
                'productos'      => [
                    ['nombre' => 'Miel Cruda de Bosque Tropical', 'cantidad' => 2, 'precio' => 5800, 'imagen' => 'productos/miel-cruda.jpg'],
                ],
            ],
        ],
        'entrega' => [
            'nombre'    => 'Mariana Solano',
            'telefono'  => '8888-1234',
            'direccion' => 'Santo Domingo, Heredia. 200 m norte de la iglesia, casa verde.',
        ],
    ];
}

// Subtotal de cada emprendimiento y total del pedido (con un for, como en clase)
$totalProductos = 0;
for ($i = 0; $i < count($pedido['grupos']); $i++) {
    $subtotalGrupo = 0;
    foreach ($pedido['grupos'][$i]['productos'] as $producto) {
        $subtotalGrupo = $subtotalGrupo + ($producto['precio'] * $producto['cantidad']);
    }
    $pedido['grupos'][$i]['subtotal'] = $subtotalGrupo;
    $totalProductos = $totalProductos + $subtotalGrupo;
}
$totalPedido = $totalProductos + $pedido['envio'];
$pagaPorSinpe = $pedido['metodo'] === 'sinpe';

require __DIR__ . '/../general/header.php';
?>

<main id="contenido" class="contenedor seccion confirmacion">

    <!-- ===== Mensaje principal ===== -->
    <header class="confirmacion__encabezado">
        <span class="confirmacion__check"><?php echo icono('check'); ?></span>
        <p class="sobretitulo">Pedido #<?php echo (int) $pedido['id']; ?> · <?php echo e($pedido['fecha']); ?></p>
        <h1 class="titulo-pagina">¡Gracias por comprar local!</h1>
        <p class="texto-guia">
            Recibimos tu pedido y ya les avisamos a los emprendedores.
            <?php if ($pagaPorSinpe): ?>
                Para que lo preparen, hacé el pago por SINPE Móvil a cada uno.
            <?php endif; ?>
        </p>
    </header>

    <div class="confirmacion__rejilla">

        <!-- ===== Pagos por emprendimiento ===== -->
        <section class="confirmacion__principal" aria-labelledby="tituloPagos">
            <h2 class="titulo-seccion" id="tituloPagos">
                <?php echo $pagaPorSinpe ? 'Pagos por SINPE Móvil' : 'Resumen por emprendimiento'; ?>
            </h2>

            <?php foreach ($pedido['grupos'] as $grupo): ?>
                <article class="tarjeta pago-grupo">
                    <header class="pago-grupo__cabecera">
                        <div>
                            <h3><?php echo e($grupo['emprendimiento']); ?></h3>
                            <p class="campo__nota"><?php echo e($grupo['dueno']); ?></p>
                        </div>
                        <span class="estado estado--pendiente">Pendiente de pago</span>
                    </header>

                    <ul class="pago-grupo__productos">
                        <?php foreach ($grupo['productos'] as $producto): ?>
                            <li>
                                <img src="<?php echo e(recurso('img/uploads/' . $producto['imagen'])); ?>" alt="" width="48" height="48">
                                <span><?php echo e($producto['nombre']); ?> <small>× <?php echo (int) $producto['cantidad']; ?></small></span>
                                <strong><?php echo colones($producto['precio'] * $producto['cantidad']); ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if ($pagaPorSinpe): ?>
                        <div class="pago-grupo__sinpe">
                            <div>
                                <p class="campo__etiqueta">SINPE Móvil</p>
                                <p class="pago-grupo__numero" id="sinpe-<?php echo e(str_replace('-', '', $grupo['sinpe'])); ?>"><?php echo e($grupo['sinpe']); ?></p>
                            </div>
                            <div>
                                <p class="campo__etiqueta">Monto</p>
                                <p class="pago-grupo__monto"><?php echo colones($grupo['subtotal']); ?></p>
                            </div>
                            <button class="btn btn--borde btn--sm copiar-sinpe" type="button" data-numero="<?php echo e($grupo['sinpe']); ?>">
                                <?php echo icono('copiar'); ?> <span>Copiar número</span>
                            </button>
                        </div>
                        <p class="campo__nota">En el detalle del SINPE escribí: <strong>Ventica #<?php echo (int) $pedido['id']; ?></strong></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>

        <!-- ===== Resumen lateral ===== -->
        <aside class="confirmacion__lateral">
            <div class="tarjeta">
                <h2 class="titulo-seccion">Resumen</h2>
                <dl class="confirmacion__totales">
                    <div><dt>Productos</dt><dd><?php echo colones($totalProductos); ?></dd></div>
                    <div><dt>Envío Correos de CR</dt><dd><?php echo colones($pedido['envio']); ?></dd></div>
                    <div class="confirmacion__total"><dt>Total</dt><dd><?php echo colones($totalPedido); ?></dd></div>
                </dl>
            </div>

            <div class="tarjeta">
                <h2 class="titulo-seccion"><?php echo icono('camion'); ?> Entrega</h2>
                <p><strong><?php echo e($pedido['entrega']['nombre']); ?></strong> · <?php echo e($pedido['entrega']['telefono']); ?></p>
                <p class="texto-guia"><?php echo e($pedido['entrega']['direccion']); ?></p>
            </div>

            <div class="tarjeta">
                <h2 class="titulo-seccion">¿Qué sigue?</h2>
                <ol class="confirmacion__pasos">
                    <li>Hacés el SINPE a cada emprendedor.</li>
                    <li>El emprendedor confirma tu pago.</li>
                    <li>Te llega por Correos de Costa Rica.</li>
                </ol>
            </div>

            <div class="confirmacion__acciones">
                <a class="btn btn--primario btn--bloque" href="<?php echo e(ruta('usuario/perfil') . '#pedidos'); ?>">Ver mis pedidos <?php echo icono('flecha'); ?></a>
                <a class="btn btn--borde btn--bloque" href="<?php echo e(ruta('catalogo')); ?>">Seguir comprando</a>
            </div>
        </aside>
    </div>
</main>

<?php require __DIR__ . '/../general/footer.php'; ?>
