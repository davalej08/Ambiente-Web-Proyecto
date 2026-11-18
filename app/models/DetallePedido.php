<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * DetallePedido
 * Una línea del pedido: producto, cantidad y precio (FIDE_DETALLE_PEDIDO_TB).
 * Solo propiedades, getters y setters (las reglas van en los services).
 */

class DetallePedido
{
    /** @var int Columna id_pedido */
    private $idPedido = 0;
    /** @var int Columna id_producto */
    private $idProducto = 0;
    /** @var int Columna cantidad */
    private $cantidad = 0;
    /** @var float Columna precio_unitario */
    private $precioUnitario = 0;

    /**
     * Crea el objeto. Si se manda una fila de la base de datos, copia sus columnas.
     * Ej: $objeto = new DetallePedido($fila);
     *
     * @param array $fila
     */
    public function __construct($fila = [])
    {
        if (isset($fila['id_pedido'])) {
            $this->idPedido = (int) $fila['id_pedido'];
        }
        if (isset($fila['id_producto'])) {
            $this->idProducto = (int) $fila['id_producto'];
        }
        if (isset($fila['cantidad'])) {
            $this->cantidad = (int) $fila['cantidad'];
        }
        if (isset($fila['precio_unitario'])) {
            $this->precioUnitario = (float) $fila['precio_unitario'];
        }
    }

    /** @return int */
    public function getIdPedido()
    {
        return $this->idPedido;
    }

    /** @param int $idPedido */
    public function setIdPedido($idPedido)
    {
        $this->idPedido = (int) $idPedido;
    }

    /** @return int */
    public function getIdProducto()
    {
        return $this->idProducto;
    }

    /** @param int $idProducto */
    public function setIdProducto($idProducto)
    {
        $this->idProducto = (int) $idProducto;
    }

    /** @return int */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /** @param int $cantidad */
    public function setCantidad($cantidad)
    {
        $this->cantidad = (int) $cantidad;
    }

    /** @return float */
    public function getPrecioUnitario()
    {
        return $this->precioUnitario;
    }

    /** @param float $precioUnitario */
    public function setPrecioUnitario($precioUnitario)
    {
        $this->precioUnitario = (float) $precioUnitario;
    }
}
