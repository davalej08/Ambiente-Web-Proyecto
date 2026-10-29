<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Correo de bienvenida al crear la cuenta (RF-01, RF-02)
 * Variables: $nombre, $esEmprendedor
 */
require __DIR__ . '/_marco_inicio.php';
?>
<h1 style="margin:0 0 12px;font-size:22px;color:#095573;">¡Hola, <?php echo e($nombre); ?>!</h1>
<?php if ($esEmprendedor): ?>
    <p>Recibimos la solicitud para abrir tu vitrina en Ventica. El equipo la va a revisar en las próximas 48 horas hábiles.</p>
    <p>Cuando quede aprobada te avisamos por este medio y vas a poder entrar al portal del emprendedor para publicar tus productos.</p>
<?php else: ?>
    <p>Tu cuenta en Ventica ya está lista. Ahora podés comprar directo a emprendedores de las 7 provincias y pagar por SINPE Móvil.</p>
    <p><a href="<?php echo e(urlCompleta('catalogo')); ?>" style="display:inline-block;padding:10px 18px;background:#3CA656;color:#ffffff;border-radius:8px;text-decoration:none;font-weight:bold;">Explorar el catálogo</a></p>
<?php endif; ?>
<?php require __DIR__ . '/_marco_fin.php'; ?>
