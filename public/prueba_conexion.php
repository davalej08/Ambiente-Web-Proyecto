<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Prueba de conexión (solo desarrollo)
 * Confirma que la base compartida en Aiven responde.
 * Abrir en: http://localhost:8000/prueba_conexion.php
 */

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/helpers/Vista.php';

$conectado = false;
$error     = '';
$version   = '';
$cifrado   = '';
$tablas    = [];

try {
    $db      = Database::conectar();
    $version = $db->query('SELECT VERSION() AS version')->fetch()['version'];
    $fila    = $db->query("SHOW SESSION STATUS LIKE 'Ssl_cipher'")->fetch();
    $cifrado = $fila ? $fila['Value'] : '';

    $resultado = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($resultado as $nombreTabla) {
        $cantidad = $db->query('SELECT COUNT(*) FROM `' . $nombreTabla . '`')->fetchColumn();
        $tablas[] = ['nombre' => $nombreTabla, 'filas' => $cantidad];
    }
    $conectado = true;
} catch (Throwable $excepcion) {
    $error = $excepcion->getMessage();
}

$titulo  = 'Prueba de conexión';
$navbar  = false;
$footer  = false;
$estilos = ['inicio'];

require __DIR__ . '/../app/views/general/header.php';
?>

<main id="contenido" class="contenedor seccion">
    <div class="tarjeta">
        <p class="sobretitulo">Solo desarrollo</p>
        <h1 class="titulo-seccion">Ventica · Prueba de conexión</h1>

        <?php if ($conectado): ?>
            <div class="mensaje mensaje--exito"><?php echo icono('check'); ?><p>Conectado a la base compartida.</p></div>
            <p>Servidor: <strong><?php echo e($version); ?></strong></p>
            <p>SSL: <strong><?php echo $cifrado !== '' ? e($cifrado) : 'sin SSL'; ?></strong></p>

            <?php if (count($tablas) > 0): ?>
                <div class="tabla-envoltura">
                    <table class="tabla">
                        <thead><tr><th>Tabla</th><th>Filas</th></tr></thead>
                        <tbody>
                            <?php foreach ($tablas as $tabla): ?>
                                <tr><td><?php echo e($tabla['nombre']); ?></td><td><?php echo (int) $tabla['filas']; ?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="texto-guia">La base está vacía. Ejecute <code>database/01_tablas.sql</code> en Workbench.</p>
            <?php endif; ?>
        <?php else: ?>
            <div class="mensaje mensaje--error"><?php echo icono('alerta'); ?><p>No se pudo conectar.</p></div>
            <p><code><?php echo e($error); ?></code></p>
        <?php endif; ?>
    </div>
</main>

<?php require __DIR__ . '/../app/views/general/footer.php'; ?>
