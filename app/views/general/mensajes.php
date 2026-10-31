<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/**
 * Toast: mensaje flotante de retroalimentación (éxito, error, aviso, info).
 * Aparece arriba a la derecha, se cierra con la X y se va solo a los 6 segundos
 * (los de error se quedan hasta que la persona los cierre). Lo controla general.js.
 *
 * El controlador puede mandarlo de dos formas:
 *   1) Variable directa:  $mensaje = ['tipo' => 'exito', 'texto' => 'Cuenta creada'];
 *   2) Flash en sesión:   $_SESSION['mensaje'] = [...];  (se muestra una vez y se borra)
 *
 * Tipos válidos: exito | error | aviso | info
 */

if (!isset($mensaje) && isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

// Ícono y título de cada tipo de mensaje
$tiposMensaje = [
    'exito' => ['icono' => 'check',  'titulo' => '¡Listo!'],
    'error' => ['icono' => 'alerta', 'titulo' => 'Algo salió mal'],
    'aviso' => ['icono' => 'alerta', 'titulo' => 'Atención'],
    'info'  => ['icono' => 'info',   'titulo' => 'Para tu información'],
];
?>
<?php if (!empty($mensaje['texto'])): ?>
    <?php $tipoMensaje = isset($tiposMensaje[valor($mensaje, 'tipo', '')]) ? $mensaje['tipo'] : 'info'; ?>
    <div id="mensajeGeneral" class="toast toast--<?php echo e($tipoMensaje); ?>"
         role="<?php echo $tipoMensaje === 'error' ? 'alert' : 'status'; ?>"
         data-cierre="<?php echo $tipoMensaje === 'error' ? 'manual' : 'auto'; ?>">
        <span class="toast__icono"><?php echo icono($tiposMensaje[$tipoMensaje]['icono']); ?></span>
        <div class="toast__cuerpo">
            <p class="toast__titulo"><?php echo e($tiposMensaje[$tipoMensaje]['titulo']); ?></p>
            <p class="toast__texto"><?php echo e($mensaje['texto']); ?></p>
        </div>
        <button class="toast__cerrar" type="button" id="cerrarMensaje" aria-label="Cerrar mensaje"><?php echo icono('cerrar'); ?></button>
        <span class="toast__tiempo" aria-hidden="true"></span>
    </div>
<?php endif; ?>
