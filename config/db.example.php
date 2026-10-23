<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Plantilla de credenciales de la base de datos (Aiven).
 * 1. Copiar este archivo como config/db.php
 * 2. Llenar los datos que muestra Aiven > servicio MySQL > Overview
 * 3. config/db.php NO se sube a GitHub (está en .gitignore)
 */

return [
    'host'   => 'su-servicio-proyecto.aivencloud.com',
    'port'   => 0,                      // el puerto que muestra Aiven (ej. 12345)
    'user'   => 'avnadmin',
    'pass'   => '',
    'db'     => 'ventica',
    'ssl_ca' => __DIR__ . '/ca.pem',    // certificado CA descargado de Aiven
];
