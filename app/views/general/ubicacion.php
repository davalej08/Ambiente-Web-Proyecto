<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/**
 * Selects de provincia, cantón y distrito en cascada (FIDE_PROVINCIA_TB,
 * FIDE_CANTON_TB, FIDE_DISTRITO_TB). Se incluye dentro de un <form>:
 *
 *   <?php require __DIR__ . '/../general/ubicacion.php'; ?>
 *
 * Variables que necesita (las manda el controlador):
 *   $ubicaciones      UbicacionService::opciones()
 *   $ubicacionElegida UbicacionService::elegidos(...)  ['provincia', 'canton', 'distrito']
 *   $errores          ['distrito' => 'mensaje'] si el distrito no es válido
 *
 * El formulario envía provincia, canton y distrito (ids). El servidor solo usa distrito.
 * Los cantones y distritos de las otras provincias van en dos selects ocultos;
 * ubicacion.js copia los que corresponden cada vez que cambia la provincia o el cantón.
 * La página tiene que cargar el script 'ubicacion'.
 */

if (!isset($ubicacionElegida)) {
    $ubicacionElegida = ['provincia' => 0, 'canton' => 0, 'distrito' => 0];
}
if (!isset($errores)) {
    $errores = [];
}
?>
<div class="rejilla-3">
    <div id="campo-provincia" class="campo <?php echo claseError($errores, 'provincia'); ?>">
        <label class="campo__etiqueta" for="provincia">Provincia</label>
        <select class="campo__entrada" id="provincia" name="provincia" required aria-describedby="provinciaError">
            <option value="">Elegí</option>
            <?php foreach ($ubicaciones['provincias'] as $idProvincia => $nombreProvincia): ?>
                <option value="<?php echo $idProvincia; ?>" <?php echo $ubicacionElegida['provincia'] === $idProvincia ? 'selected' : ''; ?>><?php echo e($nombreProvincia); ?></option>
            <?php endforeach; ?>
        </select>
        <p class="campo__error" id="provinciaError"><?php echo e(valor($errores, 'provincia', 'Elegí la provincia.')); ?></p>
    </div>

    <div id="campo-canton" class="campo <?php echo claseError($errores, 'canton'); ?>">
        <label class="campo__etiqueta" for="canton">Cantón</label>
        <select class="campo__entrada" id="canton" name="canton" required aria-describedby="cantonError">
            <option value="">Elegí</option>
            <?php foreach ($ubicaciones['cantones'] as $canton): ?>
                <?php if ($canton['padre'] === $ubicacionElegida['provincia']): ?>
                    <option value="<?php echo $canton['id']; ?>" <?php echo $ubicacionElegida['canton'] === $canton['id'] ? 'selected' : ''; ?>><?php echo e($canton['nombre']); ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <p class="campo__error" id="cantonError"><?php echo e(valor($errores, 'canton', 'Elegí el cantón.')); ?></p>
    </div>

    <div id="campo-distrito" class="campo <?php echo claseError($errores, 'distrito'); ?>">
        <label class="campo__etiqueta" for="distrito">Distrito</label>
        <select class="campo__entrada" id="distrito" name="distrito" required aria-describedby="distritoError">
            <option value="">Elegí</option>
            <?php foreach ($ubicaciones['distritos'] as $distrito): ?>
                <?php if ($distrito['padre'] === $ubicacionElegida['canton']): ?>
                    <option value="<?php echo $distrito['id']; ?>" <?php echo $ubicacionElegida['distrito'] === $distrito['id'] ? 'selected' : ''; ?>><?php echo e($distrito['nombre']); ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <p class="campo__error" id="distritoError"><?php echo e(valor($errores, 'distrito', 'Elegí el distrito.')); ?></p>
    </div>
</div>

<!-- Lista completa (oculta) de donde ubicacion.js copia las opciones -->
<select id="todosCantones" hidden disabled aria-hidden="true">
    <?php foreach ($ubicaciones['cantones'] as $canton): ?>
        <option value="<?php echo $canton['id']; ?>" data-padre="<?php echo $canton['padre']; ?>"><?php echo e($canton['nombre']); ?></option>
    <?php endforeach; ?>
</select>
<select id="todosDistritos" hidden disabled aria-hidden="true">
    <?php foreach ($ubicaciones['distritos'] as $distrito): ?>
        <option value="<?php echo $distrito['id']; ?>" data-padre="<?php echo $distrito['padre']; ?>"><?php echo e($distrito['nombre']); ?></option>
    <?php endforeach; ?>
</select>
