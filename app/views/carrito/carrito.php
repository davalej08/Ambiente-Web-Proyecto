<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Carrito de compras (RF-14) (Figma: Cliente · Carrito)
 * Ruta sugerida: carrito
 *
 * Variables que puede enviar CarritoController:
 *   $grupos           productos del carrito agrupados por emprendimiento (ver datos de muestra abajo)
 *   $costoEnvio       tarifa de envío de Correos de Costa Rica
 *   $recomendados     productos sugeridos para agregar
 *   $datosEntrega     datos guardados del cliente (nombre, teléfono, dirección…)
 *   $ubicaciones, $ubicacionElegida  selects de provincia, cantón y distrito (general/ubicacion.php)
 *   $errores          errores de validación del servidor ['campo' => 'mensaje']
 * Si no llegan, se usan datos de muestra para el Avance 2.
 */

$titulo       = 'Tu carrito';
$descripcion  = 'Revisá tu pedido y pagá directo al emprendedor por SINPE Móvil.';
$estilos      = ['carrito'];
$scripts      = ['ubicacion', 'validaciones', 'carrito'];
$paginaActiva = '';


if (!isset($grupos)) {
    $grupos = [
        [
            'id'        => 1,
            'nombre'    => 'Beneficio La Pastora',
            'ubicacion' => 'Dota, San José',
            'dueno'     => 'Don Mincho Valverde',
            'imagen'    => 'productos/cafe-geisha.jpg',
            'sinpe'     => '8842-9011',
            'productos' => [
                ['id' => 1, 'nombre' => 'Café Geisha Reserva Especial', 'nota' => 'Notas florales a miel virgen y albaricoque de altura.', 'etiqueta' => 'Cosecha fresca',    'detalle' => 'Grano entero • 340 g', 'precio' => 9800,  'cantidad' => 1, 'imagen' => 'productos/cafe-geisha.jpg'],
                ['id' => 4, 'nombre' => 'Tabla de Madera Teca Noble',   'nota' => 'Tallada artesanalmente en madera nativa recuperada.',   'etiqueta' => 'Madera sostenible', 'detalle' => 'Incluye bolsa de tela', 'precio' => 12000, 'cantidad' => 1, 'imagen' => 'productos/tabla-teca.jpg'],
            ],
        ],
        [
            'id'        => 2,
            'nombre'    => 'Chileras Don Chepe',
            'ubicacion' => 'Atenas, Alajuela',
            'dueno'     => 'Receta familiar',
            'imagen'    => 'productos/salsa-maracuya.jpg',
            'sinpe'     => '8711-3420',
            'productos' => [
                ['id' => 5, 'nombre' => 'Salsa Maracuyá & Habanero', 'nota' => 'Elaborada con pulpa natural de maracuyá ateniense y chile fresco.', 'etiqueta' => 'Picante casero', 'detalle' => 'Botella de vidrio 250 ml', 'precio' => 4200, 'cantidad' => 2, 'imagen' => 'productos/salsa-maracuya.jpg'],
            ],
        ],
    ];
}
if (!isset($costoEnvio)) {
    $costoEnvio = 2400;
}
if (!isset($recomendados)) {
    $recomendados = [
        ['id' => 2, 'nombre' => 'Miel Cruda de Bosque',    'emprendimiento' => 'Apiario Montes del Aguacate', 'precio' => 5800, 'imagen' => 'productos/miel-cruda.jpg'],
        ['id' => 7, 'nombre' => 'Jabón Romero y Abeja',    'emprendimiento' => 'Botica del Valle • Cartago',  'precio' => 6500, 'imagen' => 'productos/jabon-romero.jpg'],
        ['id' => 8, 'nombre' => 'Chocolate 75% Bribri',    'emprendimiento' => 'Cacao Caribeño • Talamanca',  'precio' => 3800, 'imagen' => 'productos/chocolate-bribri.jpg'],
        ['id' => 6, 'nombre' => 'Tote Bag de Yute',        'emprendimiento' => 'ReciclaTico • Heredia',       'precio' => 16000, 'imagen' => 'productos/tote-yute.jpg'],
    ];
}
if (!isset($datosEntrega)) {
    $datosEntrega = [];
}
if (!isset($errores)) {
    $errores = [];
}

// Totales iniciales (carrito.js los recalcula al cambiar cantidades)
$subtotal       = 0;
$totalUnidades  = 0;
$totalProductos = 0;
foreach ($grupos as $grupo) {
    foreach ($grupo['productos'] as $item) {
        $subtotal      += $item['precio'] * $item['cantidad'];
        $totalUnidades += $item['cantidad'];
        $totalProductos++;
    }
}
$totalPagar = $subtotal + $costoEnvio;

// El número SINPE que se muestra es el del primer emprendimiento
$numeroSinpe = count($grupos) > 0 ? $grupos[0]['sinpe'] : '';

$metodosPago = [
    'sinpe'    => ['texto' => 'SINPE Móvil inmediato',    'icono' => 'celular',  'nota' => 'Recomendado'],
    'tarjeta'  => ['texto' => 'Tarjeta débito / crédito', 'icono' => 'tarjeta',  'nota' => 'Visa / MC'],
    'efectivo' => ['texto' => 'Efectivo contra entrega',  'icono' => 'efectivo', 'nota' => 'Solo GAM'],
];
$pagoActual = isset($datosEntrega['pago']) ? $datosEntrega['pago'] : 'sinpe';

require __DIR__ . '/../general/header.php';
?>

<main id="contenido" class="contenedor carrito">

    <header class="encabezado-pagina carrito__encabezado">
        <span class="pill">Compra solidaria costarricense <span class="separador">•</span> 100% directa</span>
        <h1 class="titulo-pagina">Tu carrito de compras</h1>
        <p class="texto-guia">Comprás directo al artesano y productor local. 0% de comisiones abusivas para las familias creadoras.</p>
    </header>

    <?php if (count($grupos) === 0): ?>
        <section class="tarjeta carrito-vacio">
            <?php echo icono('carrito'); ?>
            <h2 class="titulo-seccion">Tu carrito está vacío</h2>
            <p class="texto-guia">Explorá el directorio y agregá productos hechos por emprendedores ticos.</p>
            <a class="btn btn--oscuro" href="<?php echo e(ruta('catalogo')); ?>">Explorar el catálogo <?php echo icono('flecha'); ?></a>
        </section>
    <?php else: ?>

        <form class="carrito__rejilla" id="formCarrito" method="post" action="<?php echo e(ruta('carrito/checkout')); ?>" novalidate
              data-envio="<?php echo (int) $costoEnvio; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">

            <div class="carrito__principal">

                <div class="aviso-vitrinas">
                    <span class="aviso-vitrinas__icono"><?php echo icono('trato'); ?></span>
                    <p>
                        <strong>Estás comprando en <span id="cantidadVitrinas"><?php echo count($grupos); ?></span> vitrinas comunitarias</strong>
                        Los productos se empacan en sus talleres de origen. Coordinamos un solo despacho unificado a través de Correos de Costa Rica.
                    </p>
                </div>

                <!-- ===== Productos por emprendimiento ===== -->
                <?php foreach ($grupos as $grupo): ?>
                    <section class="vitrina" aria-labelledby="vitrina-<?php echo (int) $grupo['id']; ?>">
                        <header class="vitrina__cabecera">
                            <img src="<?php echo e(recurso('img/uploads/' . $grupo['imagen'])); ?>" alt="" width="48" height="48">
                            <div>
                                <h2 id="vitrina-<?php echo (int) $grupo['id']; ?>"><?php echo e($grupo['nombre']); ?> <?php echo icono('verificado'); ?></h2>
                                <p><?php echo icono('ubicacion'); ?> <?php echo e($grupo['ubicacion']); ?> <span class="separador">•</span> <?php echo e($grupo['dueno']); ?></p>
                            </div>
                            <span class="insignia vitrina__conteo"><?php echo count($grupo['productos']); ?> artículos</span>
                        </header>

                        <ul class="vitrina__productos">
                            <?php foreach ($grupo['productos'] as $item): ?>
                                <li class="item-carrito" data-precio="<?php echo (int) $item['precio']; ?>">
                                    <img class="item-carrito__imagen" src="<?php echo e(recurso('img/uploads/' . $item['imagen'])); ?>" alt="" width="88" height="88">

                                    <div class="item-carrito__info">
                                        <p class="item-carrito__etiquetas">
                                            <span class="insignia"><?php echo e($item['etiqueta']); ?></span>
                                            <small><?php echo e($item['detalle']); ?></small>
                                        </p>
                                        <h3><?php echo e($item['nombre']); ?></h3>
                                        <p class="item-carrito__nota"><?php echo e($item['nota']); ?></p>
                                        <p class="item-carrito__unitario"><?php echo colones($item['precio']); ?> c/u</p>
                                    </div>

                                    <div class="cantidad" role="group" aria-label="Cantidad de <?php echo e($item['nombre']); ?>">
                                        <button class="cantidad__boton" type="button" data-cambio="-1" aria-label="Restar uno"><?php echo icono('menos'); ?></button>
                                        <input class="cantidad__entrada" type="number" name="cantidades[<?php echo (int) $item['id']; ?>]"
                                               value="<?php echo (int) $item['cantidad']; ?>" min="1" max="99" inputmode="numeric"
                                               aria-label="Cantidad">
                                        <button class="cantidad__boton" type="button" data-cambio="1" aria-label="Sumar uno"><?php echo icono('sumar'); ?></button>
                                    </div>

                                    <p class="item-carrito__subtotal"><?php echo colones($item['precio'] * $item['cantidad']); ?></p>

                                    <button class="item-carrito__quitar" type="button" data-id="<?php echo (int) $item['id']; ?>"
                                            aria-label="Quitar <?php echo e($item['nombre']); ?> del carrito">
                                        <?php echo icono('basura'); ?> Quitar
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="vitrina__mensaje">
                            <label for="mensaje-<?php echo (int) $grupo['id']; ?>"><?php echo icono('lapiz'); ?> Mensaje para <?php echo e($grupo['nombre']); ?> (opcional)</label>
                            <input class="campo__entrada" type="text" id="mensaje-<?php echo (int) $grupo['id']; ?>" name="mensajes[<?php echo (int) $grupo['id']; ?>]"
                                   maxlength="200" placeholder="Ej: 'Favor moler para chorreador fino' o una dedicatoria de regalo…">
                        </div>
                    </section>
                <?php endforeach; ?>

                <!-- ===== Datos de entrega ===== -->
                <section class="tarjeta entrega" aria-labelledby="tituloEntrega">
                    <header class="entrega__cabecera">
                        <span class="entrega__icono"><?php echo icono('camion'); ?></span>
                        <div>
                            <h2 id="tituloEntrega">Datos de entrega en Costa Rica</h2>
                            <p>Enviamos directo a tu casa u oficina mediante Correos de Costa Rica.</p>
                        </div>
                    </header>

                    <div class="entrega__campos">
                        <div class="campo <?php echo claseError($errores, 'nombre'); ?>" id="campo-nombre">
                            <label class="campo__etiqueta" for="nombre">Nombre y apellidos *</label>
                            <input class="campo__entrada" type="text" id="nombre" name="nombre" autocomplete="name"
                                   value="<?php echo e(valor($datosEntrega, 'nombre', '')); ?>" placeholder="Ej: Mariana Solano Vargas">
                            <p class="campo__error"><?php echo e(valor($errores, 'nombre', 'Escribí tu nombre completo.')); ?></p>
                        </div>

                        <div class="campo <?php echo claseError($errores, 'telefono'); ?>" id="campo-telefono">
                            <label class="campo__etiqueta" for="telefono">Teléfono (WhatsApp / SINPE) *</label>
                            <div class="campo__grupo">
                                <span class="campo__prefijo">+506</span>
                                <input class="campo__entrada" type="tel" id="telefono" name="telefono" maxlength="9" inputmode="numeric" autocomplete="tel-national"
                                       value="<?php echo e(valor($datosEntrega, 'telefono', '')); ?>" placeholder="8888-8888">
                            </div>
                            <p class="campo__error"><?php echo e(valor($errores, 'telefono', 'El teléfono debe tener 8 números.')); ?></p>
                        </div>

                        <!-- Provincia, cantón y distrito (FIDE_DISTRITO_TB); el código postal sale del distrito -->
                        <div class="entrega__ancho">
                            <?php require __DIR__ . '/../general/ubicacion.php'; ?>
                        </div>

                        <div class="campo entrega__ancho <?php echo claseError($errores, 'direccion'); ?>" id="campo-direccion">
                            <label class="campo__etiqueta" for="direccion">Dirección exacta con señas ticas *</label>
                            <textarea class="campo__entrada" id="direccion" name="direccion" rows="2" maxlength="300"
                                      placeholder="Ej: De la iglesia de Granadilla 200 m este y 50 m norte, casa #14, portón negro."><?php echo e(valor($datosEntrega, 'direccion', '')); ?></textarea>
                            <p class="campo__error"><?php echo e(valor($errores, 'direccion', 'Escribí la dirección exacta (al menos 15 caracteres).')); ?></p>
                        </div>
                    </div>

                    <div class="envio-opcion">
                        <?php echo icono('caja'); ?>
                        <div>
                            <strong>Correos de Costa Rica (Pymexpress)</strong>
                            <small>Tarifa plana solidaria con guía de rastreo. Entrega estimada en 24 a 48 horas hábiles.</small>
                        </div>
                        <span><?php echo colones($costoEnvio); ?></span>
                    </div>
                </section>
            </div>

            <!-- ===== Resumen ===== -->
            <aside class="resumen" aria-labelledby="tituloResumen">
                <header class="resumen__cabecera">
                    <h2 id="tituloResumen"><?php echo icono('recibo'); ?> Resumen del pedido</h2>
                    <span class="resumen__conteo"><span id="conteoProductos"><?php echo (int) $totalProductos; ?></span> productos</span>
                </header>

                <dl class="resumen__lineas">
                    <div><dt>Subtotal (<span id="conteoUnidades"><?php echo (int) $totalUnidades; ?></span> unidades)</dt><dd id="montoSubtotal"><?php echo colones($subtotal); ?></dd></div>
                    <div><dt>Envío Correos de CR</dt><dd><?php echo colones($costoEnvio); ?></dd></div>
                    <div class="resumen__comision"><dt><?php echo icono('trato'); ?> Comisión Ventica (0%)</dt><dd>₡0 gratis</dd></div>
                </dl>

                <div class="resumen__total">
                    <p>Total a pagar <small>Incluye IVA y garantía comunitaria</small></p>
                    <strong id="montoTotal"><?php echo colones($totalPagar); ?></strong>
                </div>

                <fieldset class="pagos">
                    <legend>Elegí tu forma de pago:</legend>
                    <?php foreach ($metodosPago as $clave => $metodo): ?>
                        <label class="pago <?php echo $pagoActual === $clave ? 'es-marcado' : ''; ?>">
                            <input type="radio" name="pago" value="<?php echo e($clave); ?>" <?php echo $pagoActual === $clave ? 'checked' : ''; ?>>
                            <?php echo icono($metodo['icono']); ?>
                            <span class="pago__texto"><?php echo e($metodo['texto']); ?></span>
                            <span class="pago__nota"><?php echo e($metodo['nota']); ?></span>
                        </label>

                        <?php if ($clave === 'sinpe'): ?>
                            <div class="pago__detalle" id="detalleSinpe" <?php echo $pagoActual === 'sinpe' ? '' : 'hidden'; ?>>
                                <?php if (count($grupos) > 1): ?>
                                    <p>Tu pedido es de <?php echo count($grupos); ?> emprendimientos. Al confirmar te mostramos el número SINPE y el monto de cada uno. Este es el primero:</p>
                                <?php else: ?>
                                    <p>Transferí sin comisiones al número del emprendimiento:</p>
                                <?php endif; ?>
                                <div class="sinpe-numero">
                                    <span>
                                        <strong id="numeroSinpe">+506 <?php echo e($numeroSinpe); ?></strong>
                                        <small>A nombre de <?php echo e($grupos[0]['nombre']); ?></small>
                                    </span>
                                    <button class="btn btn--sm btn--oscuro" type="button" id="copiarSinpe" data-numero="<?php echo e($numeroSinpe); ?>"><?php echo icono('copiar'); ?> Copiar</button>
                                </div>
                                <div class="campo" id="campo-comprobante">
                                    <label class="oculto-visual" for="comprobante">Número de comprobante SINPE (opcional)</label>
                                    <input class="campo__entrada" type="text" id="comprobante" name="comprobante" maxlength="30" placeholder="Comprobante (opcional: también podés pagar después)">
                                    <p class="campo__error">El comprobante debe tener al menos 4 caracteres.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </fieldset>

                <button class="btn btn--primario btn--bloque btn--lg resumen__pagar" type="submit" id="botonPagar">
                    <span id="textoPagar">Confirmar y pagar <?php echo colones($totalPagar); ?> con SINPE</span> <?php echo icono('flecha'); ?>
                </button>

                <ul class="resumen__garantias">
                    <li><?php echo icono('escudo'); ?> Compra 100% protegida por el sello Ventica</li>
                    <li><?php echo icono('camion'); ?> Envíos asegurados por Correos de Costa Rica</li>
                    <li><?php echo icono('mensaje'); ?> Soporte tico inmediato por WhatsApp</li>
                </ul>
            </aside>
        </form>

        <!-- Formulario escondido para quitar un producto: carrito.js le pone el id y lo envía -->
        <form id="formQuitar" method="post" action="<?php echo e(ruta('carrito/quitar')); ?>" hidden>
            <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
            <input type="hidden" name="id_producto" id="idQuitar" value="">
        </form>

        <!-- Se muestra con JavaScript si la persona quita todos los productos -->
        <section class="tarjeta carrito-vacio" id="carritoVacio" hidden>
            <?php echo icono('carrito'); ?>
            <h2 class="titulo-seccion">Tu carrito está vacío</h2>
            <p class="texto-guia">Explorá el directorio y agregá productos hechos por emprendedores ticos.</p>
            <a class="btn btn--oscuro" href="<?php echo e(ruta('catalogo')); ?>">Explorar el catálogo <?php echo icono('flecha'); ?></a>
        </section>
    <?php endif; ?>

    <!-- ===== Recomendados ===== -->
    <?php if (count($recomendados) > 0): ?>
        <section class="recomendados" aria-labelledby="tituloRecomendados">
            <div class="recomendados__cabecera">
                <div>
                    <p class="sobretitulo"><?php echo icono('destellos'); ?> Complementos recomendados</p>
                    <h2 class="titulo-seccion" id="tituloRecomendados">Otros compradores también agregaron</h2>
                </div>
                <a href="<?php echo e(ruta('catalogo')); ?>">Explorar todo el catálogo <?php echo icono('flecha'); ?></a>
            </div>

            <ul class="recomendados__lista">
                <?php foreach ($recomendados as $sugerido): ?>
                    <li class="sugerido tarjeta">
                        <div class="sugerido__info">
                            <img src="<?php echo e(recurso('img/uploads/' . $sugerido['imagen'])); ?>" alt="" width="64" height="64">
                            <div>
                                <small><?php echo e($sugerido['emprendimiento']); ?></small>
                                <h3><?php echo e($sugerido['nombre']); ?></h3>
                                <strong><?php echo colones($sugerido['precio']); ?></strong>
                            </div>
                        </div>
                        <form method="post" action="<?php echo e(ruta('carrito/agregar')); ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                            <input type="hidden" name="id_producto" value="<?php echo (int) $sugerido['id']; ?>">
                            <input type="hidden" name="cantidad" value="1">
                            <button class="btn btn--oscuro btn--sm btn--bloque" type="submit"><?php echo icono('carrito'); ?> Agregar al carrito</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

</main>

<?php require __DIR__ . '/../general/footer.php'; ?>
