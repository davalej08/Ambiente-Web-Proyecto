<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Configuración del servidor de correo (SMTP)
 *
 * CORREO_ACTIVO en false = modo de prueba: los correos no se envían, se
 * guardan en storage/correos/ y el enlace de "Recuperar contraseña" se
 * muestra en pantalla para poder probar el flujo completo en clase.
 * Para enviar de verdad hace falta un servidor SMTP (ej. Gmail con
 * contraseña de aplicación) y poner CORREO_ACTIVO en true.
 */

define('CORREO_ACTIVO', false);
define('CORREO_REMITENTE', 'no-responder@ventica.cr');
define('CORREO_NOMBRE', 'Ventica');
define('CARPETA_CORREOS', __DIR__ . '/../storage/correos/');
