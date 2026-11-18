<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/**
 * Cédula, nombre y apellidos (FIDE_USUARIO_TB). Se usa en los dos registros:
 *   <?php require __DIR__ . '/datos_persona.php'; ?>
 * Necesita $datos y $errores.
 */
?>
<div class="rejilla-2">
    <div id="campo-cedula" class="campo <?php echo claseError($errores, 'cedula'); ?>">
        <label class="campo__etiqueta" for="cedula">Cédula</label>
        <div class="campo__control">
            <input class="campo__entrada" id="cedula" name="cedula" type="text" inputmode="numeric"
                   placeholder="102340567" required maxlength="14"
                   value="<?php echo e(valor($datos, 'cedula', '')); ?>" aria-describedby="cedulaError">
            <?php echo icono('usuario', 'campo__icono'); ?>
        </div>
        <p class="campo__error" id="cedulaError"><?php echo e(valor($errores, 'cedula', 'Escribí tu cédula solo con números (9 dígitos).')); ?></p>
    </div>

    <div id="campo-nombre" class="campo <?php echo claseError($errores, 'nombre'); ?>">
        <label class="campo__etiqueta" for="nombre">Nombre</label>
        <input class="campo__entrada" id="nombre" name="nombre" type="text"
               placeholder="Mariana" autocomplete="given-name" required maxlength="60"
               value="<?php echo e(valor($datos, 'nombre', '')); ?>" aria-describedby="nombreError">
        <p class="campo__error" id="nombreError"><?php echo e(valor($errores, 'nombre', 'Escribí tu nombre.')); ?></p>
    </div>
</div>

<div class="rejilla-2">
    <div id="campo-primer_apellido" class="campo <?php echo claseError($errores, 'primer_apellido'); ?>">
        <label class="campo__etiqueta" for="primer_apellido">Primer apellido</label>
        <input class="campo__entrada" id="primer_apellido" name="primer_apellido" type="text"
               placeholder="Solano" autocomplete="family-name" required maxlength="40"
               value="<?php echo e(valor($datos, 'primer_apellido', '')); ?>" aria-describedby="primer_apellidoError">
        <p class="campo__error" id="primer_apellidoError"><?php echo e(valor($errores, 'primer_apellido', 'Escribí tu primer apellido.')); ?></p>
    </div>

    <div id="campo-segundo_apellido" class="campo <?php echo claseError($errores, 'segundo_apellido'); ?>">
        <label class="campo__etiqueta" for="segundo_apellido">Segundo apellido <span class="campo__nota">(opcional)</span></label>
        <input class="campo__entrada" id="segundo_apellido" name="segundo_apellido" type="text"
               placeholder="Vargas" maxlength="40"
               value="<?php echo e(valor($datos, 'segundo_apellido', '')); ?>" aria-describedby="segundo_apellidoError">
        <p class="campo__error" id="segundo_apellidoError"><?php echo e(valor($errores, 'segundo_apellido', 'El segundo apellido es muy largo.')); ?></p>
    </div>
</div>
