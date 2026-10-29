<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Correo con el enlace para cambiar la contraseña (RF-04)
 * Variables: $nombre, $enlace
 */
require __DIR__ . '/_marco_inicio.php';
?>
<h1 style="margin:0 0 12px;font-size:22px;color:#095573;">Cambiá tu contraseña</h1>
<p>Hola, <?php echo e($nombre); ?>. Alguien (ojalá vos) pidió cambiar la contraseña de tu cuenta de Ventica.</p>
<p><a href="<?php echo e($enlace); ?>" style="display:inline-block;padding:10px 18px;background:#095573;color:#ffffff;border-radius:8px;text-decoration:none;font-weight:bold;">Crear una contraseña nueva</a></p>
<p style="color:#5b6b70;font-size:13px;">El enlace vence en 1 hora y solo funciona una vez. Si no fuiste vos, ignorá este correo: tu contraseña sigue igual.</p>
<?php require __DIR__ . '/_marco_fin.php'; ?>
