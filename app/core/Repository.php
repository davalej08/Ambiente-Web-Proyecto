<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Repository
 * Repositorio base: ejecuta procedimientos almacenados con PDO y sentencias preparadas.
 * Todos los repositorios heredan de esta clase (class XRepository extends Repository).
 *
 * Ej. en un repositorio:
 *     return $this->consultar('CALL sp_producto_listar_por_emprendimiento(?, ?)', [$id, 1]);
 */

require_once __DIR__ . '/Database.php';

class Repository
{
    /** @var PDO */
    protected $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /**
     * Ejecuta un CALL y devuelve todas las filas.
     *
     * @param string $sql      Ej: 'CALL sp_categoria_listar()'
     * @param array $valores   Valores para los signos ?
     * @return array
     */
    protected function consultar($sql, $valores = [])
    {
        $consulta = $this->db->prepare($sql);
        $consulta->execute($valores);
        $filas = $consulta->fetchAll();
        // Un CALL deja resultados extra abiertos: se cierran antes de la siguiente consulta
        $consulta->closeCursor();
        return $filas;
    }

    /**
     * Ejecuta un CALL y devuelve solo la primera fila (o null si no hay).
     *
     * @param string $sql
     * @param array $valores
     * @return array|null
     */
    protected function consultarUno($sql, $valores = [])
    {
        $filas = $this->consultar($sql, $valores);
        if (count($filas) > 0) {
            return $filas[0];
        } else {
            return null;
        }
    }

    /**
     * Ejecuta un CALL que no devuelve filas (UPDATE, DELETE).
     *
     * @param string $sql
     * @param array $valores
     */
    protected function ejecutar($sql, $valores = [])
    {
        $consulta = $this->db->prepare($sql);
        $consulta->execute($valores);
        $consulta->closeCursor();
    }

    /**
     * Ejecuta un CALL de inserción que devuelve SELECT LAST_INSERT_ID() AS id.
     *
     * @param string $sql
     * @param array $valores
     * @return int Id del registro nuevo
     */
    protected function insertar($sql, $valores = [])
    {
        $fila = $this->consultarUno($sql, $valores);
        return (int) $fila['id'];
    }

    /** Inicia una transacción (todo o nada). */
    public function iniciarTransaccion()
    {
        $this->db->beginTransaction();
    }

    /** Guarda los cambios de la transacción. */
    public function confirmarTransaccion()
    {
        $this->db->commit();
    }

    /** Deshace los cambios de la transacción si algo falló. */
    public function deshacerTransaccion()
    {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
    }
}
