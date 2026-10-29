<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Vista
 * Funciones de apoyo para las vistas (escapar texto, rutas, íconos, montos y fechas).
 * Se cargan una sola vez desde public/index.php (y header.php por si acaso).
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

/** Meses abreviados para las fechas. */
function mesCorto($numeroMes)
{
    $meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'set', 'oct', 'nov', 'dic'];
    return $meses[(int) $numeroMes - 1];
}

/** Fecha corta. Ej: fechaCorta('2026-10-27 14:05:00') devuelve 27 oct 2026 */
function fechaCorta($fecha)
{
    $marca = strtotime($fecha);
    return date('j', $marca) . ' ' . mesCorto(date('n', $marca)) . ' ' . date('Y', $marca);
}

/** Hora en formato 10:24 am */
function horaCorta($fecha)
{
    return date('g:i a', strtotime($fecha));
}

/** Tiempo transcurrido. Ej: Hoy, Ayer, Hace 5 días, Hace 2 semanas */
function haceCuanto($fecha)
{
    $dias = (int) floor((strtotime(date('Y-m-d')) - strtotime(date('Y-m-d', strtotime($fecha)))) / 86400);

    if ($dias <= 0) {
        return 'Hoy';
    } elseif ($dias === 1) {
        return 'Ayer';
    } elseif ($dias < 7) {
        return 'Hace ' . $dias . ' días';
    } elseif ($dias < 30) {
        $semanas = (int) floor($dias / 7);
        return $semanas === 1 ? 'Hace 1 semana' : 'Hace ' . $semanas . ' semanas';
    } else {
        $meses = (int) floor($dias / 30);
        return $meses <= 1 ? 'Hace 1 mes' : 'Hace ' . $meses . ' meses';
    }
}

/** Iniciales para los avatares. Ej: iniciales('Mariana Solano') devuelve MS */
function iniciales($nombre)
{
    $palabras = explode(' ', trim($nombre));
    $texto = mb_strtoupper(mb_substr($palabras[0], 0, 1));
    if (count($palabras) > 1) {
        $texto .= mb_strtoupper(mb_substr($palabras[1], 0, 1));
    }
    return $texto;
}

/**
 * Provincia que aparece dentro de una ubicación.
 * Ej: provinciaDe('Dota, San José') devuelve San José
 */
function provinciaDe($ubicacion)
{
    $provincias = ['San José', 'Alajuela', 'Cartago', 'Heredia', 'Guanacaste', 'Puntarenas', 'Limón'];
    for ($i = 0; $i < count($provincias); $i++) {
        if (mb_strpos($ubicacion, $provincias[$i]) !== false) {
            return $provincias[$i];
        }
    }
    return '';
}

/** Imagen guardada o, si no hay, la imagen genérica de Ventica. */
function imagenO($imagen)
{
    if ($imagen === null || $imagen === '') {
        return 'sin-foto.svg';
    }
    return $imagen;
}

/**
 * URL completa con el dominio (para enlaces dentro de correos).
 * Ej: urlCompleta('auth/login') devuelve https://ventica.onrender.com/auth/login
 */
function urlCompleta($ruta)
{
    $esquema = 'http';
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $esquema = 'https';
    }
    // Render y otros hostings avisan por este encabezado que la conexión es https
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $esquema = 'https';
    }
    $servidor = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost:8000';
    return $esquema . '://' . $servidor . ruta($ruta);
}
