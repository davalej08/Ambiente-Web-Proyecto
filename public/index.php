<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Punto de entrada único: carga la configuración y ejecuta el Router
 *
 * Cómo levantar el proyecto (terminal de VS Code, en la carpeta VenticaProyecto):
 *     php -S localhost:8000 -t public public/index.php
 * y abrir http://localhost:8000
 */

// Con el servidor de PHP, los archivos que existen (css, js, imágenes) se entregan tal cual
if (PHP_SAPI === 'cli-server') {
    $archivoPedido = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($archivoPedido)) {
        return false;
    }
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../config/correo.php';
require_once __DIR__ . '/../app/helpers/Vista.php';

/**
 * Carga automáticamente una clase cuando se usa por primera vez.
 * Ej: new ProductoService() busca app/services/ProductoService.php
 *
 * @param string $clase
 */
function cargarClase($clase)
{
    $carpetas = ['core', 'controllers', 'services', 'repositories', 'models', 'helpers'];

    for ($i = 0; $i < count($carpetas); $i++) {
        $archivo = __DIR__ . '/../app/' . $carpetas[$i] . '/' . $clase . '.php';
        if (file_exists($archivo)) {
            require_once $archivo;
            return;
        }
    }
}
spl_autoload_register('cargarClase');

Sesion::iniciar();

$router = new Router();
require __DIR__ . '/../config/rutas.php';
$router->despachar();
