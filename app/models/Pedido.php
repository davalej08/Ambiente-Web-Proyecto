<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Pedido
 * Encabezado de una compra (tabla pedido).
 * Solo propiedades, getters y setters (las reglas van en los services).
 */

class Pedido
{
    /** @var int Columna id_pedido */
    private $idPedido = 0;
    /** @var int Columna id_usuario */
    private $idUsuario = 0;
    /** @var string Columna metodo_pago */
    private $metodoPago = 'sinpe';
    /** @var float Columna total */
    private $total = 0;

    /**
     * Crea el objeto. Si se manda una fila de la base de datos, copia sus columnas.
     * Ej: $objeto = new Pedido($fila);
     *
     * @param array $fila
     */
    public function __construct($fila = [])
    {
        if (isset($fila['id_pedido'])) {
            $this->idPedido = (int) $fila['id_pedido'];
        }
        if (isset($fila['id_usuario'])) {
            $this->idUsuario = (int) $fila['id_usuario'];
        }
        if (isset($fila['metodo_pago'])) {
            $this->metodoPago = (string) $fila['metodo_pago'];
        }
        if (isset($fila['total'])) {
            $this->total = (float) $fila['total'];
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
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /** @param int $idUsuario */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = (int) $idUsuario;
    }

    /** @return string */
    public function getMetodoPago()
    {
        return $this->metodoPago;
    }

    /** @param string $metodoPago */
    public function setMetodoPago($metodoPago)
    {
        $this->metodoPago = (string) $metodoPago;
    }

    /** @return float */
    public function getTotal()
    {
        return $this->total;
    }

    /** @param float $total */
    public function setTotal($total)
    {
        $this->total = (float) $total;
    }
}
