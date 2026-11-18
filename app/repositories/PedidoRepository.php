<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * PedidoRepository
 * Acceso a FIDE_PEDIDO_TB y FIDE_DETALLE_PEDIDO_TB mediante procedimientos almacenados.
 */

class PedidoRepository extends Repository
{
    /**
     * @param Pedido $pedido
     * @return int Id del pedido nuevo
     */
    public function crear($pedido)
    {
        return $this->insertar('CALL sp_pedido_insertar(?, ?, ?, ?, ?, ?)', [
            $pedido->getIdUsuario(),
            $pedido->getIdDireccion(),
            $pedido->getMetodoPago(),
            $pedido->getNombreReceptor(),
            $pedido->getTelefonoReceptor(),
            $pedido->getTotal(),
        ]);
    }

    /**
     * Agrega una línea al pedido (el procedimiento rebaja el inventario).
     *
     * @param DetallePedido $detalle
     */
    public function agregarDetalle($detalle)
    {
        $this->ejecutar('CALL sp_detalle_pedido_insertar(?, ?, ?, ?)', [
            $detalle->getIdPedido(),
            $detalle->getIdProducto(),
            $detalle->getCantidad(),
            $detalle->getPrecioUnitario(),
        ]);
    }

    /**
     * Encabezado y dirección. Con $idUsuario solo devuelve el pedido si es de esa persona.
     *
     * @param int $idPedido
     * @param int $idUsuario 0 = cualquiera
     * @return array|null
     */
    public function obtener($idPedido, $idUsuario)
    {
        return $this->consultarUno('CALL sp_pedido_obtener(?, ?)', [$idPedido, $idUsuario]);
    }

    /**
     * @param int $idPedido
     * @return array Líneas del pedido con su emprendimiento
     */
    public function listarDetalle($idPedido)
    {
        return $this->consultar('CALL sp_detalle_pedido_listar(?)', [$idPedido]);
    }

    /**
     * @param int $idUsuario
     * @return array Pedidos del cliente
     */
    public function listarPorCliente($idUsuario)
    {
        return $this->consultar('CALL sp_pedido_listar_por_cliente(?)', [$idUsuario]);
    }

    /**
     * @param int $idEmprendimiento
     * @param int $limite
     * @return array Pedidos que recibió el emprendimiento
     */
    public function listarPorEmprendimiento($idEmprendimiento, $limite)
    {
        return $this->consultar('CALL sp_pedido_listar_por_emprendimiento(?, ?)', [$idEmprendimiento, $limite]);
    }

    /**
     * Cambia el estado de las líneas de un emprendimiento dentro del pedido.
     *
     * @param int $idPedido
     * @param int $idEmprendimiento
     * @param string $estado confirmado | enviado | entregado | rechazado
     * @return int Cantidad de líneas que cambiaron
     */
    public function cambiarEstado($idPedido, $idEmprendimiento, $estado)
    {
        $fila = $this->consultarUno('CALL sp_detalle_pedido_cambiar_estado(?, ?, ?)', [$idPedido, $idEmprendimiento, $estado]);
        return (int) $fila['filas'];
    }
}
