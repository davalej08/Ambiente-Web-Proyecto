<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/**
 * Pantalla: Términos de Comercio y privacidad
 * Ruta: terminos  (enlazada desde el footer y desde los formularios de registro)
 */

$titulo      = 'Términos de Comercio';
$descripcion = 'Reglas para comprar y vender en Ventica y cómo cuidamos tus datos.';
$estilos     = ['paginas'];

// Secciones del documento: título => párrafos
$secciones = [
    '1. Qué es Ventica' => [
        'Ventica es un mercado digital que conecta a emprendimientos costarricenses con personas que quieren comprarles directamente. Ventica no fabrica ni almacena productos: cada emprendimiento es responsable de lo que publica y vende.',
    ],
    '2. Cuentas' => [
        'Para comprar o vender hace falta una cuenta con datos reales. La contraseña se guarda cifrada y nadie del equipo de Ventica la puede ver.',
        'La sesión se cierra sola después de 2 horas sin actividad. Si notás un uso extraño de tu cuenta, cambiá la contraseña y escribinos.',
    ],
    '3. Emprendimientos' => [
        'Cada solicitud para abrir una vitrina la revisa el equipo administrador antes de publicarla. Se puede rechazar si faltan datos o si los productos no son hechos o comercializados por emprendedores de Costa Rica.',
        'El emprendimiento se compromete a publicar precios e inventario reales, a responder los pedidos en un máximo de 48 horas y a enviar lo que vendió en las condiciones descritas.',
        'Si un emprendimiento incumple estas reglas, el administrador puede suspender su vitrina. La información no se borra: se conserva el historial de ventas.',
    ],
    '4. Pedidos y pagos' => [
        'Ventica no cobra comisión. El pago se hace directo al emprendimiento por SINPE Móvil, tarjeta o efectivo contra entrega, según lo que acepte cada vitrina.',
        'Un pedido puede incluir productos de varios emprendimientos; cada uno confirma su parte por separado. El costo de envío de Correos de Costa Rica se suma al total del pedido.',
    ],
    '5. Cambios y devoluciones' => [
        'Si un producto llega dañado o no corresponde a lo publicado, el cliente tiene 8 días naturales para reportarlo al emprendimiento, de acuerdo con la Ley 7472 de Promoción de la Competencia y Defensa Efectiva del Consumidor.',
    ],
    '6. Privacidad de los datos' => [
        'Los datos personales se usan solo para crear la cuenta, procesar pedidos y avisar sobre su estado. Al emprendimiento le llegan únicamente el nombre, teléfono y dirección de entrega de quien le compra.',
        'Podés pedir la corrección o eliminación de tus datos en cualquier momento, como lo establece la Ley 8968 de Protección de la Persona frente al Tratamiento de sus Datos Personales.',
    ],
];

require __DIR__ . '/header.php';
?>

<main id="contenido" class="contenedor seccion pagina-texto">
    <header class="encabezado-pagina">
        <p class="sobretitulo"><?php echo icono('escudo'); ?> Reglas claras</p>
        <h1 class="titulo-pagina">Términos de Comercio y privacidad</h1>
        <p class="texto-guia">Última actualización: <?php echo e(fechaCorta(date('Y-m-d'))); ?>. Proyecto académico del curso SC-502, Universidad Fidélitas.</p>
    </header>

    <div class="pagina-texto__rejilla">
        <!-- Índice para saltar a cada sección -->
        <nav class="tarjeta pagina-texto__indice" aria-label="Secciones">
            <p class="campo__etiqueta">En esta página</p>
            <ol>
                <?php $numero = 1; ?>
                <?php foreach ($secciones as $tituloSeccion => $parrafos): ?>
                    <li><a href="#seccion-<?php echo $numero; ?>"><?php echo e($tituloSeccion); ?></a></li>
                    <?php $numero++; ?>
                <?php endforeach; ?>
            </ol>
        </nav>

        <article class="tarjeta pagina-texto__cuerpo">
            <?php $numero = 1; ?>
            <?php foreach ($secciones as $tituloSeccion => $parrafos): ?>
                <section id="seccion-<?php echo $numero; ?>">
                    <h2 class="titulo-seccion"><?php echo e($tituloSeccion); ?></h2>
                    <?php for ($i = 0; $i < count($parrafos); $i++): ?>
                        <p><?php echo e($parrafos[$i]); ?></p>
                    <?php endfor; ?>
                </section>
                <?php $numero++; ?>
            <?php endforeach; ?>
        </article>
    </div>
</main>

<?php require __DIR__ . '/footer.php'; ?>
