<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Cambiar contraseña (RF-02)
 */

/**
 * Pantalla: Crear contraseña nueva (se llega desde el enlace del correo de recuperación)
 * Ruta sugerida: auth/cambiar-contrasena?token=...  (GET muestra el formulario, POST la guarda)
 *
 * Variables que puede enviar el controlador:
 *   $token      token del enlace del correo
 *   $cambiada   true cuando la contraseña ya se guardó
 *   $errores    ['contrasena', 'confirmacion', 'general']
 */

$titulo      = 'Crear contraseña nueva';
$descripcion = 'Creá una contraseña nueva para tu cuenta de Ventica.';
$estilos     = ['registro'];
$scripts     = ['validaciones'];
$navbar      = false;
$footer      = false;
$claseBody   = 'body-acceso';

if (!isset($token)) {
    $token = isset($_GET['token']) ? $_GET['token'] : '';
}
if (!isset($cambiada)) {
    $cambiada = false;
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

    <section class="tarjeta acceso-centrado__tarjeta" aria-labelledby="tituloCambiar">

        <?php if ($cambiada): ?>
            <!-- Estado: contraseña guardada -->
            <span class="acceso-centrado__icono acceso-centrado__icono--exito"><?php echo icono('check'); ?></span>
            <h1 class="acceso__titulo-form" id="tituloCambiar">¡Contraseña actualizada!</h1>
            <p class="texto-guia">Ya podés iniciar sesión con tu contraseña nueva.</p>
            <div class="acceso-centrado__acciones">
                <a class="btn btn--primario btn--bloque" href="<?php echo e(ruta('auth/login')); ?>">Iniciar sesión <?php echo icono('flecha'); ?></a>
            </div>

        <?php else: ?>
            <!-- Estado: formulario -->
            <span class="acceso-centrado__icono"><?php echo icono('candado'); ?></span>
            <h1 class="acceso__titulo-form" id="tituloCambiar">Creá tu contraseña nueva</h1>
            <p class="texto-guia">Usá una contraseña que no hayas usado antes en Ventica.</p>

            <?php if (!empty($errores['general'])): ?>
                <div class="mensaje mensaje--error" role="alert">
                    <?php echo icono('alerta'); ?>
                    <p><?php echo e($errores['general']); ?></p>
                </div>
            <?php endif; ?>

            <form class="formulario" id="formCambiar" method="post" action="<?php echo e(ruta('auth/cambiar-contrasena')); ?>" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">
                <input type="hidden" name="token" value="<?php echo e($token); ?>">

                <div id="campo-contrasena" class="campo <?php echo claseError($errores, 'contrasena'); ?>">
                    <label class="campo__etiqueta" for="contrasena">Contraseña nueva</label>
                    <div class="campo__control">
                        <input class="campo__entrada" id="contrasena" name="contrasena" type="password"
                               placeholder="••••••••" autocomplete="new-password" required minlength="8"
                               aria-describedby="contrasenaError requisitos">
                        <button class="campo__boton-icono" type="button" id="verContrasena" aria-label="Mostrar contraseña">
                            <?php echo icono('ojo', 'icono-ver'); ?>
                            <?php echo icono('ojo-cerrado', 'icono-ocultar'); ?>
                        </button>
                    </div>
                    <p class="campo__error" id="contrasenaError"><?php echo e(valor($errores, 'contrasena', 'La contraseña no cumple los requisitos.')); ?></p>
                </div>

                <!-- Requisitos: se marcan en verde mientras se escribe (validaciones.js) -->
                <ul class="requisitos" id="requisitos">
                    <li id="reqLargo"><?php echo icono('check'); ?> Al menos 8 caracteres</li>
                    <li id="reqNumero"><?php echo icono('check'); ?> Al menos un número</li>
                    <li id="reqMayuscula"><?php echo icono('check'); ?> Al menos una mayúscula</li>
                </ul>

                <div id="campo-confirmacion" class="campo <?php echo claseError($errores, 'confirmacion'); ?>">
                    <label class="campo__etiqueta" for="confirmacion">Confirmá la contraseña</label>
                    <input class="campo__entrada" id="confirmacion" name="confirmacion" type="password"
                           placeholder="••••••••" autocomplete="new-password" required
                           aria-describedby="confirmacionError">
                    <p class="campo__error" id="confirmacionError"><?php echo e(valor($errores, 'confirmacion', 'Las contraseñas no coinciden.')); ?></p>
                </div>

                <button class="btn btn--primario btn--lg btn--bloque" type="submit">
                    Guardar contraseña <?php echo icono('flecha'); ?>
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
