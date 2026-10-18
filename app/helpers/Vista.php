<?php
/**
 *  * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * AdminController
 * Gestión de vendedores, clientes y solicitudes de registro (RF-03, RF-05).
 * -----------------------------------------------------------------------------------------------------
 * Funciones de apoyo para las vistas.
 * Se cargan una sola vez desde app/views/general/header.php.
 */

// Normalmente BASE_URL se define en config/. Este valor es solo un respaldo.
if (!defined('BASE_URL')) {
    define('BASE_URL', '/');
}

/** Escapa texto antes de imprimirlo en HTML (evita que alguien meta código). */
function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

/** Arma la URL de una ruta de la aplicación. Ej: ruta('catalogo') */
function ruta($ruta)
{
    return BASE_URL . ltrim($ruta, '/');
}

/** URL de un archivo dentro de public/static. Ej: recurso('css/general.css') */
function recurso($archivo)
{
    return BASE_URL . 'static/' . ltrim($archivo, '/');
}

/** Devuelve el ícono del archivo public/static/img/iconos.svg. Ej: icono('carrito') */
function icono($nombre, $clase = '')
{
    $clases = trim('icono ' . $clase);
    return '<svg class="' . e($clases) . '" aria-hidden="true" focusable="false">'
        . '<use href="' . e(recurso('img/iconos.svg')) . '#i-' . e($nombre) . '"></use></svg>';
}

/** Formatea un monto en colones. Ej: colones(9500) devuelve ₡9,500 */
function colones($monto)
{
    return '₡' . number_format((float) $monto, 0, '.', ',');
}

/**
 * Lee un valor de un arreglo; si no existe, devuelve el valor por defecto.
 * Ej: valor($datos, 'correo', '')  es lo mismo que
 *     isset($datos['correo']) ? $datos['correo'] : ''
 */
function valor($arreglo, $llave, $porDefecto)
{
    if (isset($arreglo[$llave])) {
        return $arreglo[$llave];
    } else {
        return $porDefecto;
    }
}

/** Devuelve la clase 'es-invalido' si ese campo trae error (pinta el campo en rojo). */
function claseError($errores, $campo)
{
    if (isset($errores[$campo])) {
        return 'es-invalido';
    } else {
        return '';
    }
}