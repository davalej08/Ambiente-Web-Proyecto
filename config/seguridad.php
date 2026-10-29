<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Roles y rutas permitidas por rol (RF-06)
 *
 * Los nombres son los mismos de la tabla rol. Cada controlador pide el rol
 * que necesita con $this->requiereRol(ROL_...). La pantalla de inicio de
 * cada rol se usa después del login.
 */

define('ROL_CLIENTE', 'Cliente');
define('ROL_EMPRENDEDOR', 'Emprendedor');
define('ROL_ADMIN', 'Administrador');

define('INICIO_POR_ROL', [
    ROL_CLIENTE     => 'catalogo',
    ROL_EMPRENDEDOR => 'vendedor/dashboard',
    ROL_ADMIN       => 'admin/dashboard',
]);

// Vigencia del enlace para cambiar la contraseña (1 hora)
define('VIGENCIA_ENLACE_CONTRASENA', 3600);
