<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/**
 * Pantalla de error: 404 (no existe), 403 (no es para tu rol) o 500 (falló la base de datos).
 *
 * Variables que envía Controller:
 *   $codigo       404 | 403 | 500
 *   $tituloError  texto grande
 *   $textoError   explicación corta
 *   $detalle      mensaje técnico (solo en el computador local)
 */

if (!isset($codigo)) {
    $codigo = 404;
}
if (!isset($tituloError)) {
    $tituloError = 'No encontramos esta página';
}
if (!isset($textoError)) {
    $textoError = '';
}
if (!isset($detalle)) {
    $detalle = '';
}

$titulo  = $tituloError;
$estilos = ['paginas'];

require __DIR__ . '/header.php';
?>

<main id="contenido" class="contenedor seccion error-pagina">
    <div class="tarjeta error-pagina__tarjeta">
        <p class="error-pagina__codigo" aria-hidden="true"><?php echo (int) $codigo; ?></p>
        <h1 class="titulo-pagina"><?php echo e($tituloError); ?></h1>
        <p class="texto-guia"><?php echo e($textoError); ?></p>

        <?php if ($detalle !== ''): ?>
            <p class="error-pagina__detalle"><?php echo icono('info'); ?> <?php echo e($detalle); ?></p>
        <?php endif; ?>

        <div class="fila error-pagina__acciones">
            <a class="btn btn--primario" href="<?php echo e(ruta('')); ?>">Ir al inicio</a>
            <a class="btn btn--borde" href="<?php echo e(ruta('catalogo')); ?>">Explorar el catálogo</a>
        </div>
    </div>
</main>

<?php require __DIR__ . '/footer.php'; ?>
