<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * CarritoRepository
 * Carrito guardado de los usuarios con sesión (FIDE_CARRITO_TB y FIDE_CARRITO_DETALLE_TB).
 */

class CarritoRepository extends Repository
{
    /**
     * @param int $idUsuario
     * @return array Filas ['id_producto', 'cantidad']
     */
    public function listar($idUsuario)
    {
        return $this->consultar('CALL sp_carrito_listar(?)', [$idUsuario]);
    }

    /**
     * Pone la cantidad de un producto (0 lo quita del carrito).
     *
     * @param int $idUsuario
     * @param int $idProducto
     * @param int $cantidad
     */
    public function guardarProducto($idUsuario, $idProducto, $cantidad)
    {
        $this->ejecutar('CALL sp_carrito_guardar_producto(?, ?, ?)', [$idUsuario, $idProducto, $cantidad]);
    }

    /** @param int $idUsuario */
    public function vaciar($idUsuario)
    {
        $this->ejecutar('CALL sp_carrito_vaciar(?)', [$idUsuario]);
    }
}
