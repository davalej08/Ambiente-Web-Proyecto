<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Database
 * Conexión PDO a MySQL (base compartida en Aiven, con SSL).
 *
 * Uso en un repositorio:
 *   $db = Database::conectar();
 *   $consulta = $db->prepare('CALL sp_producto_listar(?)');
 *   $consulta->execute([$idVendedor]);
 */

require_once __DIR__ . '/../../config/database.php';

class Database
{
    /** @var PDO|null Conexión reutilizada durante toda la petición */
    private static $conexion = null;

    /**
     * Devuelve la conexión PDO. La crea la primera vez que se pide.
     *
     * @return PDO
     */
    public static function conectar(): PDO
    {
        if (self::$conexion !== null) {
            return self::$conexion;
        }

        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PUERTO . ';dbname=' . DB_NOMBRE . ';charset=utf8mb4';

        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // errores como excepciones
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // filas como arreglos asociativos
            PDO::ATTR_EMULATE_PREPARES   => false,                    // sentencias preparadas reales
            PDO::ATTR_TIMEOUT            => 10,
        ];

        // Aiven exige SSL: se indica el certificado CA
        if (DB_SSL_CA !== '') {
            if (!file_exists(DB_SSL_CA)) {
                throw new RuntimeException('No se encontró el certificado CA en ' . DB_SSL_CA);
            }
            $opciones[PDO::MYSQL_ATTR_SSL_CA] = DB_SSL_CA;
        }

        self::$conexion = new PDO($dsn, DB_USUARIO, DB_CLAVE, $opciones);
        return self::$conexion;
    }
}
