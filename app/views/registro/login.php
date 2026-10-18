<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Inicio de sesión (RF-01)
 */

/**
 * Pantalla: Iniciar sesión (Figma: iniciar_sesion · nodo 8:1320)
 * Ruta sugerida: auth/login  (GET muestra el formulario, POST lo procesa)
 *
 * Variables que puede enviar el controlador:
 *   $datos   ['correo' => '...']        para volver a llenar el campo si hubo error
 *   $errores ['correo' => '...', 'contrasena' => '...', 'general' => '...']
 */

$titulo      = 'Iniciar sesión';
$descripcion = 'Ingresá a tu cuenta de Ventica para comprar o gestionar tu vitrina.';
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

<main id="contenido" class="acceso">

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
            <span class="pill"><?php echo icono('tienda'); ?> Mercado Local de Costa Rica <span class="separador">•</span> <span class="pill__azul">100% Pura Vida</span></span>
            <h1 class="acceso__titulo">¡Qué bueno verte de nuevo en Ventica!</h1>
            <p class="acceso__texto">
                Accedé a tus compras, contactá directamente a los creadores locales
                y gestioná tus pedidos con total tranquilidad.
            </p>
        </div>

        <figure class="sticker">
            <img class="sticker__imagen" src="<?php echo e(recurso('img/colibri.png')); ?>" alt="Sticker de colibrí, símbolo de Ventica" width="360" height="366">

            <div class="flotante flotante--arriba">
                <span class="flotante__icono flotante__icono--azul"><?php echo icono('celular'); ?></span>
                <span><strong>SINPE Móvil</strong><small class="verde">Pago directo e inmediato</small></span>
            </div>
            <div class="flotante flotante--derecha">
                <span class="flotante__icono flotante__icono--terracota"><?php echo icono('verificado'); ?></span>
                <span><strong>Comercio 100% Tico</strong><small>Talento y diseño local</small></span>
            </div>
            <div class="flotante flotante--abajo">
                <span class="flotante__icono flotante__icono--verde"><?php echo icono('trato'); ?></span>
                <span><strong>Trato Directo</strong><small>Sin intermediarios abusivos</small></span>
            </div>
        </figure>

        <div class="acceso__pie">
            <p><?php echo icono('tienda'); ?> Más de 650 marcas independientes impulsando el talento costarricense.</p>
            <span class="en-linea"><span class="punto"></span> Mercado activo</span>
        </div>
    </aside>

    <!-- ===== Panel derecho: formulario ===== -->
    <section class="acceso__panel" aria-labelledby="tituloLogin">
        <p class="acceso__cambio">
            ¿Aún no tenés cuenta?
            <a href="<?php echo e(ruta('auth/registro')); ?>">Creá tu cuenta gratis <?php echo icono('flecha'); ?></a>
        </p>

        <div class="acceso__formulario">
            <span class="pill pill--gris"><?php echo icono('candado'); ?> ACCESO SEGURO</span>
            <h2 class="acceso__titulo-form" id="tituloLogin">Iniciar sesión</h2>
            <p class="texto-guia">Ingresá a tu vitrina o perfil de compras en Costa Rica.</p>
            <hr class="divisor">

            <?php if (!empty($errores['general'])): ?>
                <div class="mensaje mensaje--error" role="alert">
                    <?php echo icono('alerta'); ?>
                    <p><?php echo e($errores['general']); ?></p>
                </div>
            <?php endif; ?>

            <form class="formulario" id="formLogin" method="post" action="<?php echo e(ruta('auth/login')); ?>" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">

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

                <div id="campo-contrasena" class="campo <?php echo claseError($errores, 'contrasena'); ?>">
                    <div class="campo__cabecera">
                        <label class="campo__etiqueta" for="contrasena">Contraseña</label>
                        <a class="enlace-sm" href="<?php echo e(ruta('auth/recuperar')); ?>">¿Olvidaste tu contraseña?</a>
                    </div>
                    <div class="campo__control">
                        <input class="campo__entrada" id="contrasena" name="contrasena" type="password"
                               placeholder="••••••••" autocomplete="current-password" required
                               aria-describedby="contrasenaError">
                        <button class="campo__boton-icono" type="button" id="verContrasena" aria-label="Mostrar contraseña">
                            <?php echo icono('ojo', 'icono-ver'); ?>
                            <?php echo icono('ojo-cerrado', 'icono-ocultar'); ?>
                        </button>
                    </div>
                    <p class="campo__error" id="contrasenaError"><?php echo e(valor($errores, 'contrasena', 'Ingresá tu contraseña.')); ?></p>
                </div>

                <label class="casilla">
                    <input type="checkbox" name="recordarme" value="1" <?php echo !empty($datos['recordarme']) ? 'checked' : ''; ?>>
                    Recordarme en este dispositivo
                </label>

                <button class="btn btn--primario btn--lg btn--bloque" type="submit">
                    Iniciar sesión <?php echo icono('flecha'); ?>
                </button>
            </form>
        </div>

        <footer class="acceso__confianza">
            <p>
                <span><?php echo icono('escudo'); ?> Acceso protegido SSL</span>
                <span class="separador">•</span>
                <span><?php echo icono('candado'); ?> SINPE Móvil seguro</span>
            </p>
            <p>Apoyando el comercio local de Costa Rica</p>
        </footer>
    </section>
</main>

<?php require __DIR__ . '/../general/footer.php'; ?>
?>
