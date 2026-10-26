<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Perfil público del vendedor / configuración de la vitrina (Figma: Vendedor · Mi Perfil)
 * Ruta sugerida: vendedor/perfil
 *
 * Variables que puede enviar VendedorController:
 *   $perfil   datos del emprendimiento (ver datos de muestra abajo)
 *   $errores  errores de validación del servidor ['campo' => 'mensaje']
 */

$titulo       = 'Mi Perfil · Portal del emprendedor';
$estilos      = ['vendedor'];
$scripts      = ['validaciones', 'vendedor'];
$navbar       = false;
$footer       = false;
$claseBody    = 'body-panel';
$seccionPanel = 'perfil';
$pillPanel    = 'Emprendedor Local Verificado';

if (!isset($perfil)) {
    $perfil = [
        'nombre'      => 'Café Don Mincho',
        'categoria'   => 'Comida & Café de Especialidad',
        'verificado'  => true,
        'descripcion' => 'Microbeneficio familiar en las montañas de Santa María de Dota, Tarrazú. Cosechamos café de especialidad con secado al sol en camas africanas y procesos 100% artesanales.',
        'ubicacion'   => 'Tarrazú, San José',
        'telefono'    => '8841-2910',
        'pagos'       => ['sinpe', 'tarjeta'],
        'imagen'      => 'productos/cafe-geisha.jpg',
    ];
}
if (!isset($errores)) {
    $errores = [];
}

$metodosPago = [
    'sinpe'    => ['titulo' => 'SINPE Móvil',              'nota' => 'Transferencia directa e inmediata', 'icono' => 'celular'],
    'tarjeta'  => ['titulo' => 'Tarjeta de crédito o débito', 'nota' => 'Procesamiento seguro en Ventica',  'icono' => 'tarjeta'],
    'efectivo' => ['titulo' => 'Efectivo contra entrega',   'nota' => 'Válido en entregas locales directas', 'icono' => 'efectivo'],
];

require __DIR__ . '/../general/header.php';
?>

<div class="panel">
    <?php require __DIR__ . '/../general/sidebar_vendedor.php'; ?>

    <div class="panel__principal">
        <?php require __DIR__ . '/../general/barra_vendedor.php'; ?>

        <main id="contenido" class="panel__contenido panel__contenido--angosto">

            <div class="encabezado-pagina">
                <p class="sobretitulo"><?php echo icono('tienda'); ?> Configuración de vitrina</p>
                <h1 class="titulo-pagina">Mi Perfil de Vendedor</h1>
                <p class="texto-guia">Gestioná la información pública y comercial de tu vitrina en Ventica.</p>
            </div>

            <form class="formulario" id="formPerfil" method="post" enctype="multipart/form-data" action="<?php echo e(ruta('vendedor/perfil/guardar')); ?>" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo e(valor($_SESSION, 'csrf_token', '')); ?>">

                <!-- Tarjeta de identidad -->
                <section class="tarjeta perfil-identidad">
                    <div class="perfil-identidad__foto">
                        <img id="vistaLogo" src="<?php echo e(recurso('img/uploads/' . $perfil['imagen'])); ?>" alt="Logo de <?php echo e($perfil['nombre']); ?>" width="96" height="96">
                        <label class="perfil-identidad__camara" for="logo" aria-label="Cambiar foto de la vitrina">
                            <?php echo icono('camara'); ?>
                        </label>
                        <input class="oculto-visual" type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/webp">
                    </div>
                    <div>
                        <h2 id="nombreVista"><?php echo e($perfil['nombre']); ?></h2>
                        <p><?php echo e($perfil['categoria']); ?></p>
                        <?php if ($perfil['verificado']): ?>
                            <span class="insignia insignia--verificado"><?php echo icono('verificado'); ?> Emprendimiento verificado · Costa Rica</span>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Datos -->
                <section class="tarjeta formulario">
                    <div class="campo <?php echo claseError($errores, 'nombre'); ?>" id="campo-nombre">
                        <label class="campo__etiqueta" for="nombre">Nombre del negocio</label>
                        <input class="campo__entrada" type="text" id="nombre" name="nombre" maxlength="120" value="<?php echo e($perfil['nombre']); ?>">
                        <p class="campo__nota">Visible públicamente en el catálogo general y comprobantes.</p>
                        <p class="campo__error"><?php echo e(valor($errores, 'nombre', 'Escribí el nombre de tu negocio.')); ?></p>
                    </div>

                    <div class="campo <?php echo claseError($errores, 'descripcion'); ?>" id="campo-descripcion">
                        <div class="campo__cabecera">
                            <label class="campo__etiqueta" for="descripcion">Descripción breve</label>
                            <span class="campo__nota">3–4 líneas</span>
                        </div>
                        <textarea class="campo__entrada" id="descripcion" name="descripcion" rows="4" maxlength="400"><?php echo e($perfil['descripcion']); ?></textarea>
                        <p class="campo__nota">Contá tu historia, origen de materias primas y valor artesanal.</p>
                        <p class="campo__error"><?php echo e(valor($errores, 'descripcion', 'La descripción debe tener al menos 30 caracteres.')); ?></p>
                    </div>

                    <div class="campo <?php echo claseError($errores, 'ubicacion'); ?>" id="campo-ubicacion">
                        <label class="campo__etiqueta" for="ubicacion">Ubicación / Cantón</label>
                        <div class="campo__control campo__control--icono">
                            <?php echo icono('ubicacion', 'campo__icono-izq'); ?>
                            <input class="campo__entrada" type="text" id="ubicacion" name="ubicacion" value="<?php echo e($perfil['ubicacion']); ?>" placeholder="Ej: Tarrazú, San José">
                        </div>
                        <p class="campo__error"><?php echo e(valor($errores, 'ubicacion', 'Indicá el cantón y la provincia.')); ?></p>
                    </div>

                    <div class="campo <?php echo claseError($errores, 'telefono'); ?>" id="campo-telefono">
                        <label class="campo__etiqueta" for="telefono">Teléfono / WhatsApp de contacto</label>
                        <div class="campo__grupo">
                            <span class="campo__prefijo"><?php echo icono('mensaje'); ?> +506</span>
                            <input class="campo__entrada" type="tel" id="telefono" name="telefono" maxlength="9" inputmode="numeric" value="<?php echo e($perfil['telefono']); ?>" placeholder="8888-8888">
                        </div>
                        <p class="campo__nota">Canal prioritario para coordinar envíos o entregas directas con clientes.</p>
                        <p class="campo__error"><?php echo e(valor($errores, 'telefono', 'El teléfono debe tener 8 números.')); ?></p>
                    </div>

                    <fieldset class="campo metodos-pago <?php echo claseError($errores, 'pagos'); ?>" id="campo-pagos">
                        <legend class="campo__etiqueta">Métodos de pago aceptados</legend>
                        <?php foreach ($metodosPago as $clave => $metodo): ?>
                            <label class="metodo-pago">
                                <?php echo icono($metodo['icono']); ?>
                                <span>
                                    <strong><?php echo e($metodo['titulo']); ?></strong>
                                    <small><?php echo e($metodo['nota']); ?></small>
                                </span>
                                <input type="checkbox" name="pagos[]" value="<?php echo e($clave); ?>"
                                    <?php echo in_array($clave, $perfil['pagos']) ? 'checked' : ''; ?>>
                            </label>
                        <?php endforeach; ?>
                        <p class="campo__error"><?php echo e(valor($errores, 'pagos', 'Seleccioná al menos un método de pago.')); ?></p>
                    </fieldset>

                    <button class="btn btn--primario btn--bloque btn--lg" type="submit"><?php echo icono('check'); ?> Guardar cambios</button>
                </section>
            </form>

        </main>
    </div>
</div>

<?php require __DIR__ . '/../general/footer.php'; ?>
