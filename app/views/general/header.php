<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 * 
 * Encabezado común de todas las páginas.
 * Abre el documento HTML; footer.php lo cierra.
 *
 * Variables opcionales que la vista puede definir ANTES de incluir este archivo:
 *   $titulo        string  Texto de la pestaña. Ej: 'Catálogo'
 *   $descripcion   string  Meta descripción de la página
 *   $estilos       array   CSS propios de la página, sin .css. Ej: ['producto']
 *   $scripts       array   JS propios de la página, sin .js. Ej: ['producto']
 *   $navbar        bool    false para ocultar la barra pública (login, registro, paneles)
 *   $footer        bool    false para ocultar el pie de página
 *   $paginaActiva  string  Marca el enlace activo del menú: inicio | catalogo | categorias | historias
 *   $claseBody     string  Clase extra para <body>. Ej: 'body-panel'
 */

require_once __DIR__ . '/../../helpers/Vista.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($titulo)) {
    $titulo = 'Ventica';
}
if (!isset($descripcion)) {
    $descripcion = 'Ventica: mercado digital de emprendimientos costarricenses.';
}
if (!isset($estilos)) {
    $estilos = [];
}
if (!isset($navbar)) {
    $navbar = true;
}
if (!isset($claseBody)) {
    $claseBody = '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo e($descripcion); ?>">
    <title><?php echo e($titulo); ?> | Ventica</title>

    <link rel="icon" href="<?php echo e(recurso('fav/favicon.png')); ?>">

    <!-- Tipografías del diseño: Plus Jakarta Sans (títulos) e Inter (texto) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Estilos base compartidos por todo el sitio -->
    <link rel="stylesheet" href="<?php echo e(recurso('css/general.css')); ?>">

    <!-- Estilos propios de cada página -->
    <?php foreach ($estilos as $hoja): ?>
        <link rel="stylesheet" href="<?php echo e(recurso('css/' . $hoja . '.css')); ?>">
    <?php endforeach; ?>
</head>
<body class="<?php echo e($claseBody); ?>">
    <a class="saltar-contenido" href="#contenido">Saltar al contenido</a>

    <?php
    if ($navbar) {
        require __DIR__ . '/navbar.php';
    }
    require __DIR__ . '/mensajes.php';
    ?>