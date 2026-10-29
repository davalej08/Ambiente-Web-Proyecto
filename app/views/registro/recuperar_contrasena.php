<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Recuperar contraseña (RF-02)
 */

/**
 * Pantalla: Recuperar contraseña
 * Ruta sugerida: auth/recuperar  (GET muestra el formulario, POST envía el correo)
 *
 * Variables que puede enviar el controlador:
 *   $enviado  true cuando ya se envió el correo con el enlace
 *   $datos    ['correo']
 *   $errores  ['correo', 'general']
 */

$titulo      = 'Recuperar contraseña';
$descripcion = 'Recuperá el acceso a tu cuenta de Ventica.';
$estilos     = ['registro'];
$scripts     = ['validaciones'];
$navbar      = false;
$footer      = false;
$claseBody   = 'body-acceso';

// Modo de prueba (sin servidor de correo): el controlador manda el enlace para mostrarlo aquí
if (!isset($enlaceDemo)) {
    $enlaceDemo = '';
}
if (!isset($enviado)) {
    $enviado = false;
}
if (!isset($datos)) {
    $datos = [];
}
if (!isset($errores)) {
    $errores = [];
}

require __DIR__ . '/../general/header.php';
?>

<main id="contenido" class="acceso-centrado">

    <a class="marca-tile" href="<?php echo e(ruta('')); ?>">
        <span class="marca-tile__logo">
            <img src="<?php echo e(recurso('img/logo-mini.png')); ?>" alt="" width="30" height="30">
        </span>
        <span>
            <strong class="marca-tile__nombre">VENTICA</strong>
            <span class="marca-tile__pais">COSTA RICA</span>
        </span>
    </a>

    <section class="tarjeta acceso-centrado__tarjeta" aria-labelledby="tituloRecuperar">

        <?php if ($enviado): ?>
            <!-- Estado: correo enviado -->
            <span class="acceso-centrado__icono acceso-centrado__icono--exito"><?php echo icono('correo'); ?></span>
            <h1 class="acceso__titulo-form" id="tituloRecuperar">Revisá tu correo</h1>
            <p class="texto-guia">
                Si <strong><?php echo e(valor($datos, 'correo', 'tu correo')); ?></strong> está registrado en Ventica,
                te enviamos un enlace para crear una contraseña nueva. El enlace vence en 1 hora.
            </p>
            <p class="campo__nota">¿No te llegó? Revisá la carpeta de correo no deseado.</p>

            <?php if ($enlaceDemo !== ''): ?>
                <!-- Solo en modo de prueba: el correo se guardó en storage/correos/ en vez de enviarse -->
                <div class="mensaje mensaje--info aviso-demo">
                    <?php echo icono('info'); ?>
                    <p><strong>Modo de prueba:</strong> todavía no hay servidor de correo, así que te dejamos el enlace aquí.
                        <a href="<?php echo e($enlaceDemo); ?>">Crear contraseña nueva</a></p>
                </div>
            <?php endif; ?>
            <div class="acceso-centrado__acciones">
                <a class="btn btn--primario btn--bloque" href="<?php echo e(ruta('auth/login')); ?>">Volver a iniciar sesión</a>
                <a class="btn btn--texto" href="<?php echo e(ruta('auth/recuperar')); ?>">Usar otro correo</a>
            </div>

        <?php else: ?>
            <!-- Estado: formulario -->
            <span class="acceso-centrado__icono"><?php echo icono('candado'); ?></span>
            <h1 class="acceso__titulo-form" id="tituloRecuperar">¿Olvidaste tu contraseña?</h1>
            <p class="texto-guia">Escribí el correo de tu cuenta y te enviamos un enlace para crear una nueva.</p>

            <?php if (!empty($errores['general'])): ?>
                <div class="mensaje mensaje--error" role="alert">
                    <?php echo icono('alerta'); ?>
                    <p><?php echo e($errores['general']); ?></p>
                </div>
            <?php endif; ?>

            <form class="formulario" id="formRecuperar" method="post" action="<?php echo e(ruta('auth/recuperar')); ?>" novalidate>
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

                <button class="btn btn--primario btn--lg btn--bloque" type="submit">
                    Enviar enlace <?php echo icono('flecha'); ?>
                </button>
            </form>

            <p class="acceso-centrado__volver">
                <a href="<?php echo e(ruta('auth/login')); ?>"><?php echo icono('izquierda'); ?> Volver a iniciar sesión</a>
            </p>
        <?php endif; ?>
    </section>

    <p class="acceso-centrado__pie"><?php echo icono('escudo'); ?> Acceso protegido · Ventica Costa Rica</p>
</main>

<?php require __DIR__ . '/../general/footer.php'; ?>
