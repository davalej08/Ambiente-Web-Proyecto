<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 */

/**
 * ============================================================
 *  PLANTILLA BASE DE VENTICA
 * ============================================================
 *  Copiá este archivo para crear una pantalla nueva:
 *    1. Copialo a la carpeta de tu módulo (ej. views/carrito/carrito.php).
 *    2. Cambiá las variables de configuración de abajo.
 *    3. Reemplazá el contenido de <main> por tu pantalla.
 *    4. Tus estilos van en public/static/css/<modulo>.css,
 *       NO en general.css (ese archivo es compartido por todos).
 *
 *  Además, esta página sirve de guía visual: muestra los componentes
 *  que ya existen en general.css para reutilizarlos en vez de crearlos de nuevo.
 * ============================================================
 */

// ---- Configuración de la página ----
$titulo       = 'Plantilla base';
$estilos      = [];          // Ej: ['carrito']   -> public/static/css/carrito.css
$scripts      = [];          // Ej: ['checkout']  -> public/static/js/checkout.js
$paginaActiva = '';          // inicio | catalogo | categorias | historias
// $navbar = false;          // Descomentar en login, registro o paneles con sidebar
// $footer = false;

require __DIR__ . '/header.php';
?>

<main id="contenido" class="contenedor seccion">

    <!-- Encabezado de página -->
    <header class="encabezado-pagina">
        <p class="sobretitulo">Sobretítulo opcional</p>
        <h1 class="titulo-pagina">Título de la pantalla</h1>
        <p class="texto-guia">Descripción corta de lo que la persona puede hacer aquí.</p>
    </header>

    <!-- Botones -->
    <section class="tarjeta pila" aria-labelledby="t-botones">
        <h2 id="t-botones" class="titulo-seccion">Botones</h2>
        <div class="fila">
            <button class="btn btn--primario" type="button">Primario <?php echo icono('flecha'); ?></button>
            <button class="btn btn--oscuro" type="button">Oscuro</button>
            <button class="btn btn--borde" type="button">Con borde</button>
            <button class="btn btn--lima" type="button">Lima <?php echo icono('mas'); ?></button>
            <button class="btn btn--texto" type="button">Solo texto</button>
            <button class="btn btn--oscuro btn--sm" type="button">Pequeño <?php echo icono('rayo'); ?></button>
        </div>
    </section>

    <!-- Etiquetas y chips -->
    <section class="tarjeta pila" aria-labelledby="t-etiquetas">
        <h2 id="t-etiquetas" class="titulo-seccion">Etiquetas y chips</h2>
        <div class="fila">
            <span class="pill"><span class="punto"></span> Pill informativa</span>
            <span class="insignia">8 de 342 productos</span>
            <span class="estado estado--pendiente">Pendiente</span>
            <span class="estado estado--confirmado">Confirmado</span>
            <span class="estado estado--entregado">Entregado</span>
            <span class="estado estado--rechazado">Rechazado</span>
            <a class="chip es-activo" href="#">Activo</a>
            <a class="chip" href="#">Chip</a>
        </div>
    </section>

    <!-- Formulario -->
    <section class="tarjeta pila" aria-labelledby="t-form">
        <h2 id="t-form" class="titulo-seccion">Formulario</h2>
        <form class="formulario" method="post" action="#" novalidate>
            <div class="rejilla-2">
                <div class="campo">
                    <label class="campo__etiqueta" for="ejNombre">Nombre</label>
                    <div class="campo__control">
                        <input class="campo__entrada" id="ejNombre" name="nombre" type="text" placeholder="Mariana Solano">
                        <?php echo icono('usuario', 'campo__icono'); ?>
                    </div>
                </div>
                <div class="campo es-invalido">
                    <label class="campo__etiqueta" for="ejCorreo">Correo electrónico</label>
                    <div class="campo__control">
                        <input class="campo__entrada" id="ejCorreo" name="correo" type="email" value="correo-malo" aria-describedby="ejCorreoError">
                        <?php echo icono('correo', 'campo__icono'); ?>
                    </div>
                    <p class="campo__error" id="ejCorreoError">Ingresá un correo válido.</p>
                </div>
            </div>
            <div class="campo">
                <label class="campo__etiqueta" for="ejCategoria">Categoría</label>
                <select class="campo__entrada" id="ejCategoria" name="categoria">
                    <option>Crochet &amp; Tejido</option>
                    <option>Velas &amp; Aromas</option>
                </select>
            </div>
            <label class="casilla">
                <input type="checkbox" checked> Opción con casilla
            </label>
            <button class="btn btn--primario" type="submit">Guardar</button>
        </form>
    </section>

    <!-- Tabla -->
    <section class="tarjeta pila" aria-labelledby="t-tabla">
        <h2 id="t-tabla" class="titulo-seccion">Tabla</h2>
        <div class="tabla-envoltura">
            <table class="tabla">
                <thead>
                    <tr><th scope="col">Pedido</th><th scope="col">Cliente</th><th scope="col">Monto</th><th scope="col">Estado</th></tr>
                </thead>
                <tbody>
                    <tr><td>#1024</td><td>Mariana Solano</td><td><?php echo colones(9500); ?></td><td><span class="estado estado--pendiente">Pendiente</span></td></tr>
                    <tr><td>#1023</td><td>Carlos Mora</td><td><?php echo colones(14500); ?></td><td><span class="estado estado--entregado">Entregado</span></td></tr>
                </tbody>
            </table>
        </div>
    </section>

</main>

<?php require __DIR__ . '/footer.php'; ?>
