<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Registro de emprendedor (RF-01)
 */

/**
 * Pantalla: Crear cuenta de emprendedor (mismo estilo que Crear cuenta de cliente)
 * Ruta sugerida: auth/registro-vendedor  (GET muestra el formulario, POST lo procesa)
 *
 * Crea el usuario (rol Emprendedor) y la solicitud del emprendimiento en estado "pendiente".
 * El administrador la revisa en "Emprendimientos Pendientes".
 *
 * Variables que puede enviar el controlador:
 *   $datos   ['cedula', 'nombre', 'primer_apellido', 'segundo_apellido', 'correo', 'telefono',
 *             'negocio', 'provincia', 'canton', 'distrito', 'otras_senas', 'descripcion']
 *   $errores los mismos campos + 'contrasena', 'logo', 'terminos', 'general'
 *   $ubicaciones, $ubicacionElegida  para general/ubicacion.php
 */

$titulo      = 'Vender en Ventica';
$descripcion = 'Abrí la vitrina digital de tu emprendimiento costarricense en Ventica.';
$estilos     = ['registro'];
$scripts     = ['ubicacion', 'validaciones'];
$navbar      = false;
$footer      = false;
$claseBody   = 'body-acceso';

if (!isset($datos)) {
    $datos = [];
}
if (!isset($errores)) {
    $errores = [];
}

require __DIR__ . '/../general/header.php';
?>

<main id="contenido" class="acceso acceso--registro">

    <!-- ===== Panel izquierdo: marca ===== -->
    <aside class="acceso__marca">
        <a class="marca-tile" href="<?php echo e(ruta('')); ?>">
            <span class="marca-tile__logo">
                <img src="<?php echo e(recurso('img/logo-mini.png')); ?>" alt="" width="30" height="30">
            </span>
            <span>
                <strong class="marca-tile__nombre">VENTICA</strong>
                <span class="marca-tile__pais">COSTA RICA</span>
            </span>
        </a>

        <div class="acceso__intro">
            <span class="pill"><?php echo icono('tienda'); ?> Para emprendedores <span class="separador">•</span> <span class="pill__azul">Sin comisiones abusivas</span></span>
            <h1 class="acceso__titulo acceso__titulo--grande">
                Tu <span class="acento">vitrina digital</span> para vender en todo Costa Rica.
            </h1>
            <p class="acceso__texto">
                Publicá tus productos, recibí pedidos directos y cobrá por SINPE Móvil.
                Sin intermediarios y con reportes de tus ventas.
            </p>
        </div>

        <ol class="pasos-registro" aria-label="Cómo funciona">
            <li>
                <span class="pasos-registro__numero">1</span>
                <span><strong>Llenás la solicitud</strong><small>Tus datos y los de tu emprendimiento</small></span>
            </li>
            <li>
                <span class="pasos-registro__numero">2</span>
                <span><strong>Te verificamos</strong><small>El equipo de Ventica la revisa en 24 a 48 horas</small></span>
            </li>
            <li>
                <span class="pasos-registro__numero">3</span>
                <span><strong>Abrís tu vitrina</strong><small>Publicás productos y recibís pedidos</small></span>
            </li>
        </ol>

        <div class="acceso__pie">
            <p><?php echo icono('verificado'); ?> Solo emprendimientos costarricenses verificados.</p>
            <span class="en-linea"><span class="punto"></span> Comunidad activa</span>
        </div>
    </aside>

    <!-- ===== Panel derecho: formulario ===== -->
    <section class="acceso__panel" aria-labelledby="tituloRegistroVendedor">
        <div class="acceso__barra">
            <span class="pill"><span class="punto"></span> Registro de Emprendedor</span>
            <p class="acceso__cambio">
                ¿Ya tenés cuenta?
                <a href="<?php echo e(ruta('auth/login')); ?>">Iniciar sesión <?php echo icono('flecha'); ?></a>
            </p>
        </div>

        <div class="acceso__formulario acceso__formulario--ancho">
            <h2 class="acceso__titulo-form" id="tituloRegistroVendedor">Abrí tu vitrina en Ventica</h2>
            <p class="texto-guia">Creá tu cuenta y enviá la solicitud de tu emprendimiento. Es gratis.</p>

            <?php if (!empty($errores['general'])): ?>
                <div class="mensaje mensaje--error" role="alert">
                    <?php echo icono('alerta'); ?>
                    <p><?php echo e($errores['general']); ?></p>
                </div>
            <?php endif; ?>

            <!-- Elección de rol -->
            <p class="campo__etiqueta rol__titulo" id="tituloRol">Elegí cómo querés participar:</p>
            <div class="roles" role="group" aria-labelledby="tituloRol">
                <a class="rol" href="<?php echo e(ruta('auth/registro')); ?>">
                    <span class="rol__icono"><?php echo icono('bolsa'); ?></span>
                    <span class="rol__marca"></span>
                    <strong>Comprador / Cliente</strong>
                    <small>Quiero descubrir y comprar productos locales únicos y pagar por SINPE.</small>
                </a>
                <div class="rol es-activo" aria-current="true">
                    <span class="rol__icono"><?php echo icono('tienda'); ?></span>
                    <span class="rol__marca"><?php echo icono('check'); ?></span>
                    <strong>Emprendedor / Artesano</strong>
                    <small>Quiero vender mis creaciones, tener vitrina digital y recibir pedidos directos.</small>
                </div>
            </div>

            <form class="formulario" id="formRegistroVendedor" method="post" action="<?php echo e(ruta('auth/registro-vendedor')); ?>" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                <input type="hidden" name="rol" value="emprendedor">

                <!-- Datos de la persona responsable -->
                <p class="formulario__seccion"><?php echo icono('usuario'); ?> Tus datos</p>
                <?php require __DIR__ . '/datos_persona.php'; ?>

                <div class="rejilla-2">
                    <div id="campo-correo" class="campo <?php echo claseError($errores, 'correo'); ?>">
                        <label class="campo__etiqueta" for="correo">Correo electrónico</label>
                        <div class="campo__control">
                            <input class="campo__entrada" id="correo" name="correo" type="email"
                                   placeholder="hola@miemprendimiento.cr" autocomplete="email" required
                                   value="<?php echo e(valor($datos, 'correo', '')); ?>" aria-describedby="correoError">
                            <?php echo icono('correo', 'campo__icono'); ?>
                        </div>
                        <p class="campo__error" id="correoError"><?php echo e(valor($errores, 'correo', 'Ingresá un correo válido.')); ?></p>
                    </div>

                    <div id="campo-telefono" class="campo <?php echo claseError($errores, 'telefono'); ?>">
                        <label class="campo__etiqueta" for="telefono">SINPE Móvil / WhatsApp</label>
                        <div class="campo__grupo">
                            <span class="campo__prefijo">+506</span>
                            <input class="campo__entrada" id="telefono" name="telefono" type="tel"
                                   placeholder="8888-0000" autocomplete="tel-national" required
                                   inputmode="numeric" maxlength="9"
                                   value="<?php echo e(valor($datos, 'telefono', '')); ?>" aria-describedby="telefonoError">
                        </div>
                        <p class="campo__error" id="telefonoError"><?php echo e(valor($errores, 'telefono', 'Usá 8 dígitos, ej. 8888-0000.')); ?></p>
                    </div>

                    <div id="campo-contrasena" class="campo <?php echo claseError($errores, 'contrasena'); ?>">
                        <div class="campo__cabecera">
                            <label class="campo__etiqueta" for="contrasena">Contraseña</label>
                            <span class="campo__nota">Mínimo 8 caracteres</span>
                        </div>
                        <div class="campo__control">
                            <input class="campo__entrada" id="contrasena" name="contrasena" type="password"
                                   placeholder="••••••••" autocomplete="new-password" required minlength="8"
                                   aria-describedby="contrasenaError">
                            <button class="campo__boton-icono" type="button" id="verContrasena" aria-label="Mostrar contraseña">
                                <?php echo icono('ojo', 'icono-ver'); ?>
                                <?php echo icono('ojo-cerrado', 'icono-ocultar'); ?>
                            </button>
                        </div>
                        <p class="campo__error" id="contrasenaError"><?php echo e(valor($errores, 'contrasena', 'La contraseña debe tener al menos 8 caracteres.')); ?></p>
                    </div>
                </div>

                <!-- Datos del emprendimiento -->
                <p class="formulario__seccion"><?php echo icono('tienda'); ?> Tu emprendimiento</p>

                <div id="campo-negocio" class="campo <?php echo claseError($errores, 'negocio'); ?>">
                    <label class="campo__etiqueta" for="negocio">Nombre del emprendimiento</label>
                    <input class="campo__entrada" id="negocio" name="negocio" type="text"
                           placeholder="Beneficio La Pastora" required minlength="3" maxlength="120"
                           value="<?php echo e(valor($datos, 'negocio', '')); ?>" aria-describedby="negocioError">
                    <p class="campo__error" id="negocioError"><?php echo e(valor($errores, 'negocio', 'Escribí el nombre de tu emprendimiento.')); ?></p>
                </div>

                <?php require __DIR__ . '/../general/ubicacion.php'; ?>

                <div id="campo-otras_senas" class="campo <?php echo claseError($errores, 'otras_senas'); ?>">
                    <label class="campo__etiqueta" for="otras_senas">Otras señas <span class="campo__nota">(opcional)</span></label>
                    <input class="campo__entrada" id="otras_senas" name="otras_senas" type="text"
                           placeholder="Finca La Pastora, 2 km al sur del templo" maxlength="255"
                           value="<?php echo e(valor($datos, 'otras_senas', '')); ?>" aria-describedby="otras_senasError">
                    <p class="campo__error" id="otras_senasError"><?php echo e(valor($errores, 'otras_senas', 'Las señas son muy largas.')); ?></p>
                </div>

                <div id="campo-descripcion" class="campo <?php echo claseError($errores, 'descripcion'); ?>">
                    <div class="campo__cabecera">
                        <label class="campo__etiqueta" for="descripcion">¿Qué vendés?</label>
                        <span class="campo__nota"><span id="contadorDescripcion">0</span> / 300</span>
                    </div>
                    <textarea class="campo__entrada campo__entrada--area" id="descripcion" name="descripcion" rows="4" maxlength="300"
                              placeholder="Contá en pocas palabras qué hacés, con qué materiales y qué te hace único."
                              required aria-describedby="descripcionError"><?php echo e(valor($datos, 'descripcion', '')); ?></textarea>
                    <p class="campo__error" id="descripcionError"><?php echo e(valor($errores, 'descripcion', 'Describí tu emprendimiento (mínimo 20 caracteres).')); ?></p>
                </div>

                <div id="campo-logo" class="campo <?php echo claseError($errores, 'logo'); ?>">
                    <label class="campo__etiqueta" for="logo">Logo o foto de tu marca <span class="campo__nota">(opcional, JPG o PNG hasta 2 MB)</span></label>
                    <input class="campo__entrada campo__entrada--archivo" id="logo" name="logo" type="file" accept="image/png, image/jpeg" aria-describedby="logoError">
                    <p class="campo__error" id="logoError"><?php echo e(valor($errores, 'logo', 'El logo debe ser JPG o PNG de máximo 2 MB.')); ?></p>
                </div>

                <div id="campo-terminos" class="campo <?php echo claseError($errores, 'terminos'); ?>">
                    <label class="casilla">
                        <input type="checkbox" id="terminos" name="terminos" value="1" required <?php echo !empty($datos['terminos']) ? 'checked' : ''; ?> aria-describedby="terminosError">
                        <span>
                            Acepto los <a href="<?php echo e(ruta('terminos')); ?>">Términos de Comercio</a> y confirmo que mi
                            emprendimiento es <strong>costarricense</strong> y que los productos son propios.
                        </span>
                    </label>
                    <p class="campo__error" id="terminosError"><?php echo e(valor($errores, 'terminos', 'Tenés que aceptar los términos para continuar.')); ?></p>
                </div>

                <div class="aviso-revision">
                    <?php echo icono('reloj'); ?>
                    <p>Tu solicitud queda <strong>pendiente</strong> hasta que el equipo de Ventica la apruebe. Te avisamos por correo.</p>
                </div>

                <button class="btn btn--primario btn--lg btn--bloque" type="submit">
                    Enviar solicitud <?php echo icono('flecha'); ?>
                </button>
            </form>
        </div>

        <footer class="acceso__confianza">
            <p>
                <span><?php echo icono('candado'); ?> Tus datos están protegidos</span>
                <span><?php echo icono('trato'); ?> Cobro directo por SINPE Móvil</span>
            </p>
            <p class="azul">Hecho con orgullo en Costa Rica</p>
        </footer>
    </section>
</main>

<?php require __DIR__ . '/../general/footer.php'; ?>
