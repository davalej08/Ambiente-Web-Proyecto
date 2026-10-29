<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/**
 * Pantalla: Contacto
 * Ruta: contacto  (GET muestra el formulario, POST lo procesa)
 *
 * Variables que envía CatalogoController:
 *   $datos    ['nombre', 'correo', 'motivo', 'mensaje']  para volver a llenar los campos
 *   $errores  ['campo' => 'mensaje']
 */

$titulo      = 'Contacto';
$descripcion = 'Escribile al equipo de Ventica.';
$estilos     = ['registro', 'paginas'];
$scripts     = ['validaciones'];

if (!isset($datos)) {
    $datos = [];
}
if (!isset($errores)) {
    $errores = [];
}

$motivos = [
    'compra' => 'Tengo una consulta sobre una compra',
    'vender' => 'Quiero vender en Ventica',
    'cuenta' => 'Problemas con mi cuenta',
    'otro'   => 'Otro tema',
];
$motivoElegido = valor($datos, 'motivo', '');

require __DIR__ . '/header.php';
?>

<main id="contenido" class="contenedor seccion contacto">
    <div class="contacto__rejilla">
        <section class="contacto__info">
            <p class="sobretitulo"><?php echo icono('mensaje'); ?> Hablemos</p>
            <h1 class="titulo-pagina">¿En qué te ayudamos?</h1>
            <p class="texto-guia">Respondemos en menos de 24 horas hábiles. Si es sobre un pedido, incluí el número (#CR-…).</p>

            <ul class="contacto__canales">
                <li><?php echo icono('correo'); ?> <span><strong>Correo</strong> soporte@ventica.cr</span></li>
                <li><?php echo icono('celular'); ?> <span><strong>WhatsApp</strong> +506 8888-0502</span></li>
                <li><?php echo icono('reloj'); ?> <span><strong>Horario</strong> Lunes a viernes, 8 a.m. a 5 p.m.</span></li>
            </ul>
            <a class="btn btn--texto" href="<?php echo e(ruta('ayuda')); ?>"><?php echo icono('ayuda'); ?> Ver preguntas frecuentes</a>
        </section>

        <form class="tarjeta formulario" id="formContacto" method="post" action="<?php echo e(ruta('contacto')); ?>" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">

            <div class="campo <?php echo claseError($errores, 'nombre'); ?>" id="campo-nombre">
                <label class="campo__etiqueta" for="nombre">Nombre</label>
                <input class="campo__entrada" id="nombre" name="nombre" type="text" maxlength="100" autocomplete="name"
                       value="<?php echo e(valor($datos, 'nombre', '')); ?>">
                <p class="campo__error"><?php echo e(valor($errores, 'nombre', 'Escribí tu nombre.')); ?></p>
            </div>

            <div class="campo <?php echo claseError($errores, 'correo'); ?>" id="campo-correo">
                <label class="campo__etiqueta" for="correo">Correo</label>
                <input class="campo__entrada" id="correo" name="correo" type="email" maxlength="150" autocomplete="email"
                       value="<?php echo e(valor($datos, 'correo', '')); ?>">
                <p class="campo__error"><?php echo e(valor($errores, 'correo', 'Escribí un correo válido.')); ?></p>
            </div>

            <div class="campo <?php echo claseError($errores, 'motivo'); ?>" id="campo-motivo">
                <label class="campo__etiqueta" for="motivo">Motivo</label>
                <select class="campo__entrada" id="motivo" name="motivo">
                    <option value="">Escogé una opción</option>
                    <?php foreach ($motivos as $clave => $texto): ?>
                        <option value="<?php echo e($clave); ?>" <?php echo $motivoElegido === $clave ? 'selected' : ''; ?>><?php echo e($texto); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="campo__error"><?php echo e(valor($errores, 'motivo', 'Escogé un motivo.')); ?></p>
            </div>

            <div class="campo <?php echo claseError($errores, 'mensaje'); ?>" id="campo-mensaje">
                <label class="campo__etiqueta" for="mensaje">Mensaje</label>
                <textarea class="campo__entrada campo__entrada--area" id="mensaje" name="mensaje" rows="5" maxlength="1000"><?php echo e(valor($datos, 'mensaje', '')); ?></textarea>
                <p class="campo__error"><?php echo e(valor($errores, 'mensaje', 'Contanos un poco más (mínimo 15 caracteres).')); ?></p>
            </div>

            <button class="btn btn--primario btn--bloque" type="submit">Enviar mensaje <?php echo icono('flecha'); ?></button>
        </form>
    </div>
</main>

<?php require __DIR__ . '/footer.php'; ?>
