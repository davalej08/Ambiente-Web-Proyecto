<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * ClienteRepository
 * Favoritos del cliente (FIDE_FAVORITO_TB) mediante procedimientos almacenados.
 */

class ClienteRepository extends Repository
{
    /**
     * @param int $idUsuario
     * @return array Productos favoritos del cliente
     */
    public function listarFavoritos($idUsuario)
    {
        return $this->consultar('CALL sp_favorito_listar(?)', [$idUsuario]);
    }

    /**
     * @param int $idUsuario
     * @param int $idProducto
     */
    public function agregarFavorito($idUsuario, $idProducto)
    {
        $this->ejecutar('CALL sp_favorito_agregar(?, ?)', [$idUsuario, $idProducto]);
    }

    /**
     * @param int $idUsuario
     * @param int $idProducto
     */
    public function quitarFavorito($idUsuario, $idProducto)
    {
        $this->ejecutar('CALL sp_favorito_eliminar(?, ?)', [$idUsuario, $idProducto]);
    }
}
