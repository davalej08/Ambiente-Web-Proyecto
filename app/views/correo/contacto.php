<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Correo interno con el mensaje que llega desde la página de Contacto
 * Variables: $nombre, $correo, $motivo, $mensaje
 */
require __DIR__ . '/_marco_inicio.php';
?>
<h1 style="margin:0 0 12px;font-size:20px;color:#095573;">Mensaje nuevo desde Contacto</h1>
<p><strong>De:</strong> <?php echo e($nombre); ?> (<?php echo e($correo); ?>)<br>
<strong>Motivo:</strong> <?php echo e($motivo); ?></p>
<p style="padding:12px 16px;background:#F2EFDF;border-radius:8px;"><?php echo nl2br(e($mensaje)); ?></p>
<?php require __DIR__ . '/_marco_fin.php'; ?>
