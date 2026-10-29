<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/**
 * Pantalla: Preguntas frecuentes
 * Ruta: ayuda  (enlazada desde el footer y el botón de ayuda del portal del vendedor)
 *
 * Cada pregunta usa <details>: el navegador la abre y la cierra sin JavaScript.
 */

$titulo      = 'Preguntas frecuentes';
$descripcion = 'Respuestas rápidas sobre compras, pagos por SINPE y vitrinas en Ventica.';
$estilos     = ['paginas'];

$grupos = [
    'Para compradores' => [
        ['¿Cómo pago mi pedido?', 'Al confirmar el pedido te mostramos el número SINPE Móvil de cada emprendimiento y el monto exacto. También podés escoger tarjeta o efectivo contra entrega si la vitrina lo acepta.'],
        ['¿Por qué tengo que pagarle a cada emprendimiento por aparte?', 'Porque Ventica no cobra comisión ni retiene tu dinero: le pagás directo a quien hizo el producto. Por eso un pedido con dos vitrinas tiene dos pagos.'],
        ['¿Cuánto cuesta el envío?', 'El envío por Correos de Costa Rica cuesta ₡' . number_format(COSTO_ENVIO, 0, '.', ',') . ' por pedido a cualquier provincia.'],
        ['¿Dónde veo el estado de mi pedido?', 'En Mi perfil > Mis pedidos. El estado cambia cuando el emprendimiento confirma tu pago y cuando marca el pedido como entregado.'],
    ],
    'Para emprendedores' => [
        ['¿Cuánto cuesta vender en Ventica?', 'Nada. Abrir la vitrina es gratis y no cobramos comisión por venta.'],
        ['¿Cuánto tarda la aprobación de mi vitrina?', 'El equipo revisa las solicitudes en un máximo de 48 horas hábiles. Te avisamos por correo si fue aprobada o si falta algún dato.'],
        ['¿Cómo confirmo un pago por SINPE?', 'En el portal, sección Pedidos, revisá que te llegó el SINPE y tocá "Confirmar pedido". Cuando lo entregues, tocá "Marcar como entregado".'],
        ['¿Puedo pausar un producto?', 'Sí. En Mis Productos usá el interruptor Activo / Pausado. El producto deja de aparecer en el catálogo pero no se borra.'],
    ],
    'Cuenta y seguridad' => [
        ['Olvidé mi contraseña', 'En la pantalla de iniciar sesión tocá "¿Olvidaste tu contraseña?" y te enviamos un enlace que vence en 1 hora.'],
        ['¿Por qué se cerró mi sesión?', 'Por seguridad la sesión se cierra después de 2 horas sin actividad. Solo tenés que volver a ingresar.'],
    ],
];

require __DIR__ . '/header.php';
?>

<main id="contenido" class="contenedor seccion pagina-texto">
    <header class="encabezado-pagina">
        <p class="sobretitulo"><?php echo icono('ayuda'); ?> Centro de ayuda</p>
        <h1 class="titulo-pagina">Preguntas frecuentes</h1>
        <p class="texto-guia">¿No encontrás tu respuesta? <a href="<?php echo e(ruta('contacto')); ?>">Escribinos</a> y te ayudamos.</p>
    </header>

    <?php foreach ($grupos as $tituloGrupo => $preguntas): ?>
        <section class="faq" aria-label="<?php echo e($tituloGrupo); ?>">
            <h2 class="titulo-seccion"><?php echo e($tituloGrupo); ?></h2>
            <?php for ($i = 0; $i < count($preguntas); $i++): ?>
                <details class="faq__pregunta tarjeta">
                    <summary><?php echo e($preguntas[$i][0]); ?> <?php echo icono('abajo'); ?></summary>
                    <p><?php echo e($preguntas[$i][1]); ?></p>
                </details>
            <?php endfor; ?>
        </section>
    <?php endforeach; ?>
</main>

<?php require __DIR__ . '/footer.php'; ?>
