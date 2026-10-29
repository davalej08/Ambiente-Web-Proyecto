<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Instalador de la base de datos (solo desarrollo)
 * Ejecuta los scripts de database/ en orden contra la base compartida de Aiven,
 * usando las credenciales de config/db.php.
 *
 * Uso (desde la carpeta del proyecto, en la terminal):
 *     php database/instalar.php
 *
 * OJO: 01_tablas.sql borra y vuelve a crear las tablas. Lo corre UNA sola persona.
 */

require_once __DIR__ . '/../config/database.php';

$scripts = ['01_tablas.sql', '02_procedimientos.sql', '03_datos_prueba.sql'];

// Se conecta SIN escoger base, porque 01_tablas.sql es el que la crea
$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PUERTO . ';charset=utf8mb4';
$opciones = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

if (DB_SSL_CA !== '') {
    $opciones[PDO::MYSQL_ATTR_SSL_CA] = DB_SSL_CA;
}

try {
    $db = new PDO($dsn, DB_USUARIO, DB_CLAVE, $opciones);
    echo "Conectado a " . DB_HOST . "\n";
} catch (PDOException $error) {
    echo "No se pudo conectar: " . $error->getMessage() . "\n";
    exit(1);
}

foreach ($scripts as $nombre) {
    $ruta = __DIR__ . '/' . $nombre;
    $contenido = file_get_contents($ruta);

    // Se quitan los comentarios "-- ..." para ver si el archivo tiene SQL real
    $lineas = explode("\n", $contenido);
    $sql = '';
    for ($i = 0; $i < count($lineas); $i++) {
        $linea = trim($lineas[$i]);
        if ($linea !== '' && substr($linea, 0, 2) !== '--') {
            $sql = $sql . $lineas[$i] . "\n";
        }
    }

    if (trim($sql) === '') {
        echo "- $nombre: vacío, se omite\n";
        continue;
    }

    // Procedimientos almacenados: usan DELIMITER $$ (se ejecuta cada bloque completo)
    if (strpos($sql, 'DELIMITER') !== false) {
        $sql = str_replace(['DELIMITER $$', 'DELIMITER ;'], '', $sql);
        $sentencias = explode('$$', $sql);
    } else {
        $sentencias = explode(";\n", $sql);
    }

    $cantidad = 0;
    foreach ($sentencias as $sentencia) {
        $sentencia = trim($sentencia);
        if ($sentencia === '' || $sentencia === ';') {
            continue;
        }
        try {
            $db->exec($sentencia);
            $cantidad++;
        } catch (PDOException $error) {
            echo "Error en $nombre:\n" . substr($sentencia, 0, 200) . "\n" . $error->getMessage() . "\n";
            exit(1);
        }
    }
    echo "- $nombre: $cantidad sentencias ejecutadas\n";
}

$tablas = $db->query('SHOW TABLES FROM ' . DB_NOMBRE)->fetchAll(PDO::FETCH_COLUMN);
echo "Listo. Tablas en '" . DB_NOMBRE . "': " . implode(', ', $tablas) . "\n";
