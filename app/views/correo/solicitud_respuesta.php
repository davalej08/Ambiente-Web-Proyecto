<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Correo con la respuesta del administrador a una solicitud de emprendimiento (RF-03)
 * Variables: $nombre, $negocio, $aprobada, $observacion
 */
require __DIR__ . '/_marco_inicio.php';
?>
<?php if ($aprobada): ?>
    <h1 style="margin:0 0 12px;font-size:22px;color:#3CA656;">¡<?php echo e($negocio); ?> ya está en Ventica!</h1>
    <p>Hola, <?php echo e($nombre); ?>. Revisamos tu solicitud y tu vitrina quedó aprobada.</p>
    <p>Entrá al portal del emprendedor para subir tus productos y empezar a recibir pedidos.</p>
    <p><a href="<?php echo e(urlCompleta('auth/login')); ?>" style="display:inline-block;padding:10px 18px;background:#3CA656;color:#ffffff;border-radius:8px;text-decoration:none;font-weight:bold;">Ir a mi portal</a></p>
<?php else: ?>
    <h1 style="margin:0 0 12px;font-size:22px;color:#095573;">Revisamos tu solicitud</h1>
    <p>Hola, <?php echo e($nombre); ?>. Por ahora no pudimos aprobar la vitrina de <strong><?php echo e($negocio); ?></strong>.</p>
<?php endif; ?>
<?php if ($observacion !== ''): ?>
    <p style="padding:12px 16px;background:#F2EFDF;border-radius:8px;"><strong>Comentario del equipo:</strong><br><?php echo e($observacion); ?></p>
<?php endif; ?>
<?php require __DIR__ . '/_marco_fin.php'; ?>
