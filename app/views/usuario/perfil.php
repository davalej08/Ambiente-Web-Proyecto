<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Perfil del cliente (RF-05)
 */

/**
 * Pantalla: Mi perfil del cliente, con tres pestañas: Mis datos, Mis pedidos y Favoritos
 * Ruta sugerida: usuario/perfil   (usuario/perfil#pedidos abre directo la pestaña de pedidos)
 *
 * Variables que puede enviar ClienteController:
 *   $usuario    ['nombre_completo', 'cedula', 'nombre', 'primer_apellido', 'segundo_apellido',
 *                'correo', 'telefono', 'direccion', 'fecha_registro']
 *   $ubicaciones, $ubicacionElegida  selects de provincia, cantón y distrito (general/ubicacion.php)
 *   $pedidos    pedidos del cliente (ver datos de muestra abajo)
 *   $favoritos  productos marcados como favoritos
 *   $errores    errores del formulario de datos ['campo' => 'mensaje']
 * Si no llegan, se usan datos de muestra para el Avance 2.
 */

$titulo       = 'Mi perfil';
$descripcion  = 'Tus datos, tus pedidos y tus productos favoritos en Ventica.';
$estilos      = ['producto', 'usuario'];
$scripts      = ['ubicacion', 'validaciones', 'usuario'];
$paginaActiva = '';

if (!isset($usuario)) {
    $usuario = [
        'nombre_completo'  => 'Mariana Solano Mora',
        'cedula'           => '406183954',
        'nombre'           => 'Mariana',
        'primer_apellido'  => 'Solano',
        'segundo_apellido' => 'Mora',
        'correo'           => 'mariana@ejemplo.cr',
        'telefono'         => '8888-1234',
        'direccion'        => '200 m norte de la iglesia, casa verde',
        'fecha_registro'   => 'septiembre 2026',
    ];
}

if (!isset($pedidos)) {
    $pedidos = [
        ['id' => 1048, 'fecha' => '27 oct 2026', 'emprendimiento' => 'Beneficio La Pastora',      'productos' => 'Café Geisha Reserva Especial × 2',  'total' => 19000, 'metodo' => 'SINPE Móvil', 'estado' => 'confirmado', 'imagen' => 'productos/cafe-geisha.jpg'],
        ['id' => 1031, 'fecha' => '20 oct 2026', 'emprendimiento' => 'Chileras Don Chepe',        'productos' => 'Salsa Maracuyá & Habanero × 3',     'total' => 12600, 'metodo' => 'SINPE Móvil', 'estado' => 'enviado',    'imagen' => 'productos/salsa-maracuya.jpg'],
        ['id' => 1007, 'fecha' => '05 oct 2026', 'emprendimiento' => 'Botica Botánica del Valle', 'productos' => 'Jabón Romero y Cera de Abeja × 2',  'total' => 13000, 'metodo' => 'Tarjeta',     'estado' => 'entregado',  'imagen' => 'productos/jabon-romero.jpg'],
        ['id' => 998,  'fecha' => '28 sep 2026', 'emprendimiento' => 'Orfebrería Chibcha CR',     'productos' => 'Aretes Precolombinos × 1',          'total' => 14500, 'metodo' => 'SINPE Móvil', 'estado' => 'rechazado',  'imagen' => 'productos/aretes-precolombinos.jpg'],
    ];
}

if (!isset($favoritos)) {
    $favoritos = [
        ['id' => 2, 'nombre' => 'Miel Cruda de Bosque Tropical', 'emprendimiento' => 'Apiario Montes del Aguacate', 'ubicacion' => 'San Ramón, Alajuela', 'precio' => 5800,  'imagen' => 'productos/miel-cruda.jpg'],
        ['id' => 4, 'nombre' => 'Tabla de Madera Teca Noble',    'emprendimiento' => 'Taller Baula Artesanos',      'ubicacion' => 'Nicoya, Guanacaste',  'precio' => 18500, 'imagen' => 'productos/tabla-teca.jpg'],
        ['id' => 8, 'nombre' => 'Chocolate Fino 75% Bribri',     'emprendimiento' => 'Cacao Caribeño Ancestral',    'ubicacion' => 'Talamanca, Limón',    'precio' => 3800,  'imagen' => 'productos/chocolate-bribri.jpg'],
    ];
}

if (!isset($errores)) {
    $errores = [];
}

// Texto de cada estado del pedido (coincide con detalle_pedido.estado_entrega)
$textoEstado = [
    'pendiente'  => 'Pendiente de pago',
    'confirmado' => 'Pago confirmado',
    'enviado'    => 'En camino',
    'entregado'  => 'Entregado',
    'rechazado'  => 'Rechazado',
];

// Iniciales para el círculo del avatar: "Mariana Solano" -> "MS" (función de Vista.php)
$iniciales = iniciales($usuario['nombre_completo']);

require __DIR__ . '/../general/header.php';
?>

<main id="contenido" class="contenedor seccion perfil">

    <!-- ===== Encabezado del perfil ===== -->
    <header class="perfil__encabezado tarjeta">
        <span class="perfil__avatar" aria-hidden="true"><?php echo e($iniciales); ?></span>
        <div class="perfil__datos">
            <p class="sobretitulo">Mi cuenta</p>
            <h1 class="titulo-pagina"><?php echo e($usuario['nombre_completo']); ?></h1>
            <p class="texto-guia"><?php echo e($usuario['correo']); ?> · Cliente desde <?php echo e($usuario['fecha_registro']); ?></p>
        </div>
        <dl class="perfil__resumen">
            <div>
                <dt><?php echo count($pedidos); ?></dt>
                <dd>Pedidos</dd>
            </div>
            <div>
                <dt id="cantidadFavoritos"><?php echo count($favoritos); ?></dt>
                <dd>Favoritos</dd>
            </div>
        </dl>
    </header>

    <!-- ===== Pestañas ===== -->
    <div class="pestanas" role="tablist" aria-label="Secciones de mi perfil">
        <button class="pestana es-activa" type="button" role="tab" id="tab-datos" data-panel="panel-datos" aria-selected="true"><?php echo icono('usuario'); ?> Mis datos</button>
        <button class="pestana" type="button" role="tab" id="tab-pedidos" data-panel="panel-pedidos" aria-selected="false"><?php echo icono('recibo'); ?> Mis pedidos</button>
        <button class="pestana" type="button" role="tab" id="tab-favoritos" data-panel="panel-favoritos" aria-selected="false"><?php echo icono('corazon'); ?> Favoritos</button>
    </div>

    <!-- ===== Panel: Mis datos ===== -->
    <section class="panel-perfil es-visible" id="panel-datos" role="tabpanel" aria-labelledby="tab-datos">
        <div class="perfil__columnas">
            <form class="tarjeta formulario" id="formPerfil" method="post" action="<?php echo e(ruta('usuario/perfil/guardar')); ?>" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                <h2 class="titulo-seccion">Datos personales</h2>

                <div class="rejilla-2">
                    <div class="campo">
                        <label class="campo__etiqueta" for="cedulaFija">Cédula</label>
                        <input class="campo__entrada" id="cedulaFija" type="text" value="<?php echo e($usuario['cedula']); ?>" readonly>
                    </div>

                    <div id="campo-nombre" class="campo <?php echo claseError($errores, 'nombre'); ?>">
                        <label class="campo__etiqueta" for="nombre">Nombre</label>
                        <input class="campo__entrada" id="nombre" name="nombre" type="text" maxlength="60" value="<?php echo e($usuario['nombre']); ?>" autocomplete="given-name" aria-describedby="nombreError">
                        <p class="campo__error" id="nombreError"><?php echo e(valor($errores, 'nombre', 'Escribí tu nombre.')); ?></p>
                    </div>

                    <div id="campo-primer_apellido" class="campo <?php echo claseError($errores, 'primer_apellido'); ?>">
                        <label class="campo__etiqueta" for="primer_apellido">Primer apellido</label>
                        <input class="campo__entrada" id="primer_apellido" name="primer_apellido" type="text" maxlength="40" value="<?php echo e($usuario['primer_apellido']); ?>" autocomplete="family-name" aria-describedby="primer_apellidoError">
                        <p class="campo__error" id="primer_apellidoError"><?php echo e(valor($errores, 'primer_apellido', 'Escribí tu primer apellido.')); ?></p>
                    </div>

                    <div id="campo-segundo_apellido" class="campo <?php echo claseError($errores, 'segundo_apellido'); ?>">
                        <label class="campo__etiqueta" for="segundo_apellido">Segundo apellido</label>
                        <input class="campo__entrada" id="segundo_apellido" name="segundo_apellido" type="text" maxlength="40" value="<?php echo e($usuario['segundo_apellido']); ?>" aria-describedby="segundo_apellidoError">
                        <p class="campo__error" id="segundo_apellidoError"><?php echo e(valor($errores, 'segundo_apellido', 'El segundo apellido es muy largo.')); ?></p>
                    </div>

                    <div id="campo-correo" class="campo <?php echo claseError($errores, 'correo'); ?>">
                        <label class="campo__etiqueta" for="correo">Correo electrónico</label>
                        <input class="campo__entrada" id="correo" name="correo" type="email" value="<?php echo e($usuario['correo']); ?>" autocomplete="email" aria-describedby="correoError">
                        <p class="campo__error" id="correoError"><?php echo e(valor($errores, 'correo', 'Ingresá un correo válido.')); ?></p>
                    </div>

                    <div id="campo-telefono" class="campo <?php echo claseError($errores, 'telefono'); ?>">
                        <label class="campo__etiqueta" for="telefono">Teléfono / WhatsApp</label>
                        <div class="campo__grupo">
                            <span class="campo__prefijo">+506</span>
                            <input class="campo__entrada" id="telefono" name="telefono" type="tel" inputmode="numeric" maxlength="9" value="<?php echo e($usuario['telefono']); ?>" aria-describedby="telefonoError">
                        </div>
                        <p class="campo__error" id="telefonoError"><?php echo e(valor($errores, 'telefono', 'Usá 8 dígitos, ej. 8888-0000.')); ?></p>
                    </div>
                </div>

                <h2 class="titulo-seccion">Dirección de entrega habitual</h2>
                <?php require __DIR__ . '/../general/ubicacion.php'; ?>

                <div id="campo-direccion" class="campo <?php echo claseError($errores, 'direccion'); ?>">
                    <label class="campo__etiqueta" for="direccion">Otras señas</label>
                    <textarea class="campo__entrada" id="direccion" name="direccion" rows="2" maxlength="255" placeholder="200 m norte de la iglesia, casa verde"><?php echo e($usuario['direccion']); ?></textarea>
                    <p class="campo__error"><?php echo e(valor($errores, 'direccion', 'La dirección es muy larga.')); ?></p>
                </div>

                <p id="mensajePerfil" class="perfil__mensaje" role="status"></p>

                <div class="fila">
                    <button class="btn btn--primario" type="submit">Guardar cambios</button>
                </div>
            </form>

            <aside class="tarjeta perfil__seguridad">
                <h2 class="titulo-seccion"><?php echo icono('candado'); ?> Seguridad</h2>
                <p class="texto-guia">Te enviamos un enlace a tu correo para crear una contraseña nueva.</p>
                <a class="btn btn--borde btn--bloque" href="<?php echo e(ruta('auth/recuperar')); ?>">Cambiar contraseña</a>
                <hr class="divisor">
                <p class="texto-guia">¿Querés vender tus propios productos?</p>
                <a class="btn btn--oscuro btn--bloque" href="<?php echo e(ruta('auth/registro-vendedor')); ?>"><?php echo icono('tienda'); ?> Abrir mi vitrina</a>
                <a class="btn btn--texto btn--bloque" href="<?php echo e(ruta('auth/logout')); ?>"><?php echo icono('salir'); ?> Cerrar sesión</a>
            </aside>
        </div>
    </section>

    <!-- ===== Panel: Mis pedidos ===== -->
    <section class="panel-perfil" id="panel-pedidos" role="tabpanel" aria-labelledby="tab-pedidos">
        <?php if (count($pedidos) === 0): ?>
            <div class="tarjeta perfil__vacio">
                <?php echo icono('bolsa'); ?>
                <h2>Todavía no tenés pedidos</h2>
                <a class="btn btn--primario" href="<?php echo e(ruta('catalogo')); ?>">Explorar el catálogo</a>
            </div>
        <?php else: ?>
            <ul class="pedidos-cliente">
                <?php foreach ($pedidos as $pedido): ?>
                    <li class="pedido-cliente tarjeta">
                        <img src="<?php echo e(recurso('img/uploads/' . $pedido['imagen'])); ?>" alt="" width="72" height="72">
                        <div class="pedido-cliente__info">
                            <p class="pedido-cliente__numero">Pedido #<?php echo (int) $pedido['id']; ?> · <?php echo e($pedido['fecha']); ?></p>
                            <h3><?php echo e($pedido['emprendimiento']); ?></h3>
                            <p class="texto-guia"><?php echo e($pedido['productos']); ?></p>
                        </div>
                        <div class="pedido-cliente__lado">
                            <span class="estado estado--<?php echo e($pedido['estado']); ?>"><?php echo e($textoEstado[$pedido['estado']]); ?></span>
                            <p class="pedido-cliente__total"><?php echo colones($pedido['total']); ?></p>
                            <p class="campo__nota"><?php echo e($pedido['metodo']); ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <!-- Formulario escondido para quitar un favorito: usuario.js le pone el id y lo envía -->
    <form id="formQuitarFavorito" method="post" action="<?php echo e(ruta('usuario/favoritos/quitar')); ?>" hidden>
        <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
        <input type="hidden" name="id_producto" id="idFavoritoQuitar" value="">
    </form>

    <!-- ===== Panel: Favoritos ===== -->
    <section class="panel-perfil" id="panel-favoritos" role="tabpanel" aria-labelledby="tab-favoritos">
        <div class="tarjeta perfil__vacio" id="favoritosVacio" <?php echo count($favoritos) > 0 ? 'hidden' : ''; ?>>
            <?php echo icono('corazon'); ?>
            <h2>No tenés favoritos todavía</h2>
            <p class="texto-guia">Tocá el corazón de un producto en el catálogo para guardarlo aquí.</p>
            <a class="btn btn--primario" href="<?php echo e(ruta('catalogo')); ?>">Explorar el catálogo</a>
        </div>

        <ul class="productos perfil__favoritos" id="listaFavoritos">
            <?php foreach ($favoritos as $producto): ?>
                <?php $urlDetalle = ruta('producto/detalle') . '?id=' . (int) $producto['id']; ?>
                <li>
                    <article class="producto">
                        <div class="producto__media">
                            <a href="<?php echo e($urlDetalle); ?>" tabindex="-1" aria-hidden="true">
                                <img src="<?php echo e(recurso('img/uploads/' . $producto['imagen'])); ?>" alt="" width="206" height="206">
                            </a>
                            <button class="producto__favorito quitar-favorito" type="button" data-id="<?php echo (int) $producto['id']; ?>" aria-pressed="true" aria-label="Quitar <?php echo e($producto['nombre']); ?> de favoritos">
                                <?php echo icono('corazon'); ?>
                            </button>
                        </div>
                        <div class="producto__cuerpo">
                            <p class="producto__ubicacion"><?php echo icono('ubicacion'); ?> <?php echo e($producto['ubicacion']); ?></p>
                            <h3 class="producto__nombre"><a href="<?php echo e($urlDetalle); ?>"><?php echo e($producto['nombre']); ?></a></h3>
                            <p class="producto__emprendimiento"><?php echo e($producto['emprendimiento']); ?></p>
                            <div class="producto__pie">
                                <p class="producto__precio"><small>Precio</small> <?php echo colones($producto['precio']); ?></p>
                                <form method="post" action="<?php echo e(ruta('carrito/agregar')); ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                                    <input type="hidden" name="id_producto" value="<?php echo (int) $producto['id']; ?>">
                                    <input type="hidden" name="cantidad" value="1">
                                    <button class="btn btn--oscuro btn--sm" type="submit">Pedir <?php echo icono('rayo'); ?></button>
                                </form>
                            </div>
                        </div>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</main>

<?php require __DIR__ . '/../general/footer.php'; ?>
