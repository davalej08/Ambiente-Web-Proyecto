<?php
/**
 * ============================================================
 *  SERVIDOR DE PRUEBA DE VISTAS  (solo para desarrollo)
 * ============================================================
 *  Sirve para ver las pantallas en el navegador mientras el
 *  Router real (app/core/Router.php) no esté terminado.
 *
 *  Cómo usarlo: en la terminal de VS Code, parada en la carpeta
 *  VenticaProyecto, escribir:
 *
 *      php -S localhost:8000 -t public public/prueba.php
 *
 *  y abrir http://localhost:8000 en el navegador.
 *
 *  Para agregar una pantalla nueva: agregar un "case" con su ruta.
 *  Cuando el Router real funcione, este archivo se puede borrar.
 * ============================================================
 */

$ruta = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Si piden un archivo que existe (css, js, imágenes), el servidor lo entrega tal cual
if ($ruta !== '/' && file_exists(__DIR__ . $ruta)) {
    return false;
}

$vistas = __DIR__ . '/../app/views/';

switch ($ruta) {
    case '/':
        require $vistas . 'general/landing.php';
        break;
    case '/catalogo':
        require $vistas . 'producto/listado.php';
        break;
    case '/auth/login':
        require $vistas . 'registro/login.php';
        break;
    case '/auth/registro':
        require $vistas . 'registro/registro_cliente.php';
        break;
    case '/plantilla':
        require $vistas . 'general/plantilla.php';
        break;

    // Cliente: detalle (vitrina del emprendimiento) y carrito
    case '/producto/detalle':
        require $vistas . 'producto/detalle.php';
        break;
    case '/carrito':
        require $vistas . 'carrito/carrito.php';
        break;

    // Portal del vendedor
    case '/vendedor/dashboard':
        require $vistas . 'constante/dashboard_vendedor.php';
        break;
    case '/vendedor/productos':
        require $vistas . 'producto/gestion.php';
        break;
    case '/vendedor/productos/nuevo':
        require $vistas . 'producto/formulario.php';
        break;
    case '/vendedor/productos/editar':
        // Producto de muestra para ver el formulario en modo edición
        $producto = ['id' => 1, 'nombre' => 'Café Geisha Reserva Especial', 'id_categoria' => 1, 'precio' => 9500, 'stock' => 24,
                     'descripcion' => 'Grano entero de altura cultivado en Dota, con notas florales a jazmín y miel de abeja.', 'imagen' => 'productos/cafe-geisha.jpg'];
        require $vistas . 'producto/formulario.php';
        break;
    case '/vendedor/perfil':
        require $vistas . 'vendedor/perfil.php';
        break;
    case '/vendedor/pedidos':
        require $vistas . 'vendedor/pedidos.php';
        break;
    case '/vendedor/reportes':
        require $vistas . 'vendedor/reportes.php';
        break;

    // Ruta en caso de que la pantalla que se busca no esté
    default:
        http_response_code(404);
        echo '<h1>Esta pantalla todavía no existe</h1>';
        echo '<p>Ruta pedida: ' . htmlspecialchars($ruta) . '</p>';
        echo '<p><a href="/">Volver al inicio</a></p>';
        break;
}