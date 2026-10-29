<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Configuración general de la aplicación
 */

define('APP_NOMBRE', 'Ventica');

// Zona horaria de Costa Rica para fechas y reportes
date_default_timezone_set('America/Costa_Rica');

/*
 * BASE_URL: carpeta desde donde se sirve la aplicación. Se calcula sola:
 *   - php -S localhost:8000 -t public public/index.php  ->  /
 *   - XAMPP: http://localhost/VenticaProyecto/public/   ->  /VenticaProyecto/public/
 *   - Render                                            ->  /
 */
if (PHP_SAPI === 'cli-server' || PHP_SAPI === 'cli') {
    define('BASE_URL', '/');
} else {
    $carpetaPublica = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    if ($carpetaPublica === '/' || $carpetaPublica === '.') {
        $carpetaPublica = '';
    }
    define('BASE_URL', $carpetaPublica . '/');
    unset($carpetaPublica);
}

define('SESION_INACTIVIDAD', 7200);        // 2 horas sin actividad cierran la sesión (RF-07)
define('COSTO_ENVIO', 2400);               // Tarifa de Correos de Costa Rica por pedido
define('PRODUCTOS_POR_PAGINA', 8);         // Catálogo
define('TAMANO_MAXIMO_IMAGEN', 2097152);   // 2 MB
define('CARPETA_SUBIDAS', __DIR__ . '/../public/static/img/uploads/');

// Las 7 provincias (se usan en formularios y validaciones)
define('PROVINCIAS', ['San José', 'Alajuela', 'Cartago', 'Heredia', 'Guanacaste', 'Puntarenas', 'Limón']);
