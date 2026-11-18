<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Registro de cliente (RF-04)
 */

/**
 * Pantalla: Crear cuenta de cliente (Figma: crear_cuenta · nodo 8:1102)
 * Ruta sugerida: auth/registro  (GET muestra el formulario, POST lo procesa)
 *
 * La tarjeta "Emprendedor / Artesano" lleva a registro_vendedor.php (otra persona del grupo).
 *
 * Variables que puede enviar el controlador:
 *   $datos   ['cedula', 'nombre', 'primer_apellido', 'segundo_apellido', 'correo', 'telefono']
 *   $errores los mismos campos + 'contrasena', 'terminos', 'general'
 */

$titulo      = 'Crear cuenta';
$descripcion = 'Creá tu cuenta gratis en Ventica y comprá directo a emprendedores costarricenses.';
$estilos     = ['registro'];
$scripts     = ['validaciones'];
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
            <span class="pill"><?php echo icono('destellos'); ?> Mercado Digital Autóctono <span class="separador">•</span> <span class="pill__azul">100% Pura Vida</span></span>
            <h1 class="acceso__titulo acceso__titulo--grande">
                El mercado del <span class="acento">talento</span> y la creatividad costarricense.
            </h1>
            <p class="acceso__texto">
                Conectate con cientos de emprendedores ticos: diseño, crochet, cosmética natural,
                velas aromáticas, arte y café de especialidad.
            </p>
        </div>

        <ul class="insignias-giradas" aria-label="Beneficios de Ventica">
            <li class="insignia-girada insignia-girada--1">
                <span class="flotante__icono flotante__icono--azul"><?php echo icono('verificado'); ?></span>
                <span><strong>100% HECHO EN CR</strong><small>Manos ticas creadoras</small></span>
            </li>
            <li class="insignia-girada insignia-girada--2">
                <span class="flotante__icono flotante__icono--verde"><?php echo icono('trato'); ?></span>
                <span><strong>Trato Directo</strong><small>Cero comisiones abusivas</small></span>
            </li>
            <li class="insignia-girada insignia-girada--3">
                <span class="flotante__icono flotante__icono--azul"><?php echo icono('celular'); ?></span>
                <span><strong>SINPE Móvil Seguro</strong><small class="verde">Pago directo al creador</small></span>
            </li>
        </ul>

        <div class="acceso__pie">
            <div class="comunidad">
                <div class="avatares" aria-hidden="true">
                    <span>SJ</span><span>AL</span><span>CT</span><span>+4</span>
                </div>
                <p><strong>+650 creadores locales</strong><br>Creciendo juntos en las 7 provincias de Costa Rica</p>
            </div>
            <span class="en-linea en-linea--caja"><?php echo icono('check'); ?> Comunidad Verificada</span>
        </div>
    </aside>

    <!-- ===== Panel derecho: formulario ===== -->
    <section class="acceso__panel" aria-labelledby="tituloRegistro">
        <div class="acceso__barra">
            <span class="pill"><span class="punto"></span> Registro Gratuito</span>
            <p class="acceso__cambio">
                ¿Ya tenés cuenta?
                <a href="<?php echo e(ruta('auth/login')); ?>">Iniciar sesión <?php echo icono('flecha'); ?></a>
            </p>
        </div>

        <div class="acceso__formulario acceso__formulario--ancho">
            <h2 class="acceso__titulo-form" id="tituloRegistro">Creá tu cuenta en Ventica</h2>
            <p class="texto-guia">Empezá a comprar directo o abrí la vitrina digital de tu emprendimiento en minutos.</p>

            <?php if (!empty($errores['general'])): ?>
                <div class="mensaje mensaje--error" role="alert">
                    <?php echo icono('alerta'); ?>
                    <p><?php echo e($errores['general']); ?></p>
                </div>
            <?php endif; ?>

            <!-- Elección de rol -->
            <p class="campo__etiqueta rol__titulo" id="tituloRol">Elegí cómo querés participar:</p>
            <div class="roles" role="group" aria-labelledby="tituloRol">
                <div class="rol es-activo" aria-current="true">
                    <span class="rol__icono"><?php echo icono('bolsa'); ?></span>
                    <span class="rol__marca"><?php echo icono('check'); ?></span>
                    <strong>Comprador / Cliente</strong>
                    <small>Quiero descubrir y comprar productos locales únicos y pagar por SINPE.</small>
                </div>
                <a class="rol" href="<?php echo e(ruta('auth/registro-vendedor')); ?>">
                    <span class="rol__icono"><?php echo icono('tienda'); ?></span>
                    <span class="rol__marca"></span>
                    <strong>Emprendedor / Artesano</strong>
                    <small>Quiero vender mis creaciones, tener vitrina digital y recibir pedidos directos.</small>
                </a>
            </div>

            <form class="formulario" id="formRegistro" method="post" action="<?php echo e(ruta('auth/registro')); ?>" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                <input type="hidden" name="rol" value="cliente">

                <?php require __DIR__ . '/datos_persona.php'; ?>

                <div class="rejilla-2">
                    <div id="campo-correo" class="campo <?php echo claseError($errores, 'correo'); ?>">
                        <label class="campo__etiqueta" for="correo">Correo electrónico</label>
                        <div class="campo__control">
                            <input class="campo__entrada" id="correo" name="correo" type="email"
                                   placeholder="mariana@ejemplo.cr" autocomplete="email" required
                                   value="<?php echo e(valor($datos, 'correo', '')); ?>" aria-describedby="correoError">
                            <?php echo icono('correo', 'campo__icono'); ?>
                        </div>
                        <p class="campo__error" id="correoError"><?php echo e(valor($errores, 'correo', 'Ingresá un correo válido.')); ?></p>
                    </div>

                    <div id="campo-telefono" class="campo <?php echo claseError($errores, 'telefono'); ?>">
                        <label class="campo__etiqueta" for="telefono">Teléfono / WhatsApp (SINPE)</label>
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

                <div id="campo-terminos" class="campo <?php echo claseError($errores, 'terminos'); ?>">
                    <label class="casilla">
                        <input type="checkbox" id="terminos" name="terminos" value="1" required <?php echo !empty($datos['terminos']) ? 'checked' : ''; ?> aria-describedby="terminosError">
                        <span>
                            Acepto los <a href="<?php echo e(ruta('terminos')); ?>">Términos de Uso</a>, la política de privacidad
                            y me comprometo a apoyar el <strong>comercio justo local</strong> de Costa Rica.
                        </span>
                    </label>
                    <p class="campo__error" id="terminosError"><?php echo e(valor($errores, 'terminos', 'Tenés que aceptar los términos para continuar.')); ?></p>
                </div>

                <button class="btn btn--primario btn--lg btn--bloque" type="submit">
                    Crear mi cuenta gratis <?php echo icono('flecha'); ?>
                </button>
            </form>
        </div>

        <footer class="acceso__confianza">
            <p>
                <span><?php echo icono('candado'); ?> Tus datos están protegidos</span>
                <span><?php echo icono('trato'); ?> Apoyo directo sin intermediarios abusivos</span>
            </p>
            <p class="azul">Hecho con orgullo en Costa Rica</p>
        </footer>
    </section>
</main>

<?php require __DIR__ . '/../general/footer.php'; ?>
?>
