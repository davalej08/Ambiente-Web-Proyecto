<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * ClienteRepository
 * Favoritos y reseñas del cliente (FIDE_FAVORITO_TB, FIDE_RESENA_TB) mediante procedimientos almacenados.
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

    /**
     * Productos entregados de un pedido con la reseña que ya escribió el cliente (HU-04).
     *
     * @param int $idPedido
     * @param int $idUsuario
     * @return array
     */
    public function listarParaResena($idPedido, $idUsuario)
    {
        return $this->consultar('CALL sp_resena_listar_por_pedido(?, ?)', [$idPedido, $idUsuario]);
    }

    /**
     * Guarda o cambia una reseña (FIDE_RESENA_TB).
     *
     * @param int $idUsuario
     * @param int $idProducto
     * @param int $calificacion 1 a 5
     * @param string $comentario
     * @return bool false si el cliente no ha recibido ese producto
     */
    public function guardarResena($idUsuario, $idProducto, $calificacion, $comentario)
    {
        $fila = $this->consultarUno('CALL sp_resena_guardar(?, ?, ?, ?)', [$idUsuario, $idProducto, $calificacion, $comentario]);
        return $fila !== null && (int) $fila['filas'] > 0;
    }
}
