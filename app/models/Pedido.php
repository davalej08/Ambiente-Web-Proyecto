<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Pedido
 * Encabezado de una compra (FIDE_PEDIDO_TB).
 * Solo propiedades, getters y setters (las reglas van en los services).
 */

class Pedido
{
    /** @var int Columna id_pedido */
    private $idPedido = 0;
    /** @var int Columna id_usuario */
    private $idUsuario = 0;
    /** @var int Columna id_direccion */
    private $idDireccion = 0;
    /** @var string Columna metodo_pago */
    private $metodoPago = 'sinpe';
    /** @var string Columna nombre_receptor */
    private $nombreReceptor = '';
    /** @var string Columna telefono */
    private $telefonoReceptor = '';
    /** @var float Columna total */
    private $total = 0;
    /** @var string Columna estado */
    private $estado = 'pendiente';

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
        if (isset($fila['id_direccion'])) {
            $this->idDireccion = (int) $fila['id_direccion'];
        }
        if (isset($fila['metodo_pago'])) {
            $this->metodoPago = (string) $fila['metodo_pago'];
        }
        if (isset($fila['nombre_receptor'])) {
            $this->nombreReceptor = (string) $fila['nombre_receptor'];
        }
        if (isset($fila['telefono'])) {
            $this->telefonoReceptor = (string) $fila['telefono'];
        }
        if (isset($fila['total'])) {
            $this->total = (float) $fila['total'];
        }
        if (isset($fila['estado'])) {
            $this->estado = (string) $fila['estado'];
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

    /** @return int */
    public function getIdDireccion()
    {
        return $this->idDireccion;
    }

    /** @param int $idDireccion */
    public function setIdDireccion($idDireccion)
    {
        $this->idDireccion = (int) $idDireccion;
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

    /** @return string */
    public function getNombreReceptor()
    {
        return $this->nombreReceptor;
    }

    /** @param string $nombreReceptor */
    public function setNombreReceptor($nombreReceptor)
    {
        $this->nombreReceptor = (string) $nombreReceptor;
    }

    /** @return string */
    public function getTelefonoReceptor()
    {
        return $this->telefonoReceptor;
    }

    /** @param string $telefonoReceptor */
    public function setTelefonoReceptor($telefonoReceptor)
    {
        $this->telefonoReceptor = (string) $telefonoReceptor;
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

    /** @return string */
    public function getEstado()
    {
        return $this->estado;
    }

    /** @param string $estado */
    public function setEstado($estado)
    {
        $this->estado = (string) $estado;
    }
}
