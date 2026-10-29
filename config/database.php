<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Datos de conexión a MySQL (base compartida en Aiven)
 *
 * Las credenciales reales están en config/db.php, que NO se sube a GitHub.
 * Cada integrante copia config/db.example.php como db.php y llena los datos.
 *
 * En Render (o cualquier hosting) no existe db.php: los datos se ponen como
 * variables de entorno DB_HOST, DB_PORT, DB_USER, DB_PASS y DB_NAME.
 */

$archivoCredenciales = __DIR__ . '/db.php';

if (file_exists($archivoCredenciales)) {
    $credenciales = require $archivoCredenciales;
} elseif (getenv('DB_HOST') !== false) {
    $credenciales = [
        'host'   => getenv('DB_HOST'),
        'port'   => getenv('DB_PORT'),
        'user'   => getenv('DB_USER'),
        'pass'   => getenv('DB_PASS'),
        'db'     => getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'ventica',
        // Aiven exige SSL con ca.pem. DB_SSL=0 solo para probar con un MySQL local.
        'ssl_ca' => getenv('DB_SSL') === '0' ? '' : __DIR__ . '/ca.pem',
    ];
} else {
    throw new RuntimeException('Falta config/db.php. Copie config/db.example.php como db.php y llene los datos de Aiven.');
}

define('DB_HOST',    $credenciales['host']);
define('DB_PUERTO',  (int) $credenciales['port']);
define('DB_NOMBRE',  $credenciales['db']);
define('DB_USUARIO', $credenciales['user']);
define('DB_CLAVE',   $credenciales['pass']);
define('DB_SSL_CA',  isset($credenciales['ssl_ca']) ? $credenciales['ssl_ca'] : '');

unset($credenciales, $archivoCredenciales);
