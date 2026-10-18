<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/**
 * Mensajes de retroalimentación (éxito, error, aviso).
 *
 * El controlador puede mandarlos de dos formas:
 *   1) Variable directa:  $mensaje = ['tipo' => 'exito', 'texto' => 'Cuenta creada'];
 *   2) Flash en sesión:   $_SESSION['mensaje'] = [...];  (se muestra una vez y se borra)
 *
 * Tipos válidos: exito | error | aviso | info
 */

if (!isset($mensaje) && isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

$iconosMensaje = ['exito' => 'check', 'error' => 'alerta', 'aviso' => 'alerta', 'info' => 'info'];
?>
<?php if (!empty($mensaje['texto'])): ?>
    <?php $tipoMensaje = isset($iconosMensaje[valor($mensaje, 'tipo', '')]) ? $mensaje['tipo'] : 'info'; ?>
    <div id="mensajeGeneral" class="mensaje mensaje--<?php echo e($tipoMensaje); ?>" role="<?php echo $tipoMensaje === 'error' ? 'alert' : 'status'; ?>">
        <?php echo icono($iconosMensaje[$tipoMensaje]); ?>
        <p><?php echo e($mensaje['texto']); ?></p>
        <button class="mensaje__cerrar" type="button" id="cerrarMensaje" aria-label="Cerrar mensaje"><?php echo icono('cerrar'); ?></button>
    </div>
<?php endif; ?>