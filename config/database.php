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
 */

$archivoCredenciales = __DIR__ . '/db.php';

if (!file_exists($archivoCredenciales)) {
    throw new RuntimeException('Falta config/db.php. Copie config/db.example.php como db.php y llene los datos de Aiven.');
}

$credenciales = require $archivoCredenciales;

define('DB_HOST',    $credenciales['host']);
define('DB_PUERTO',  (int) $credenciales['port']);
define('DB_NOMBRE',  $credenciales['db']);
define('DB_USUARIO', $credenciales['user']);
define('DB_CLAVE',   $credenciales['pass']);
define('DB_SSL_CA',  isset($credenciales['ssl_ca']) ? $credenciales['ssl_ca'] : '');

unset($credenciales, $archivoCredenciales);
