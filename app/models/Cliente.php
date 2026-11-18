<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Cliente
 * Datos de entrega de un pedido: a quién (FIDE_PEDIDO_TB) y a dónde (FIDE_DIRECCION_TB) se envía.
 * Solo propiedades, getters y setters (las reglas van en los services).
 */

class Cliente
{
    /** @var string Columna nombre_receptor */
    private $nombreReceptor = '';
    /** @var string Columna telefono */
    private $telefono = '';
    /** @var int Columna id_distrito */
    private $idDistrito = 0;
    /** @var string Columna direccion_exacta */
    private $direccionExacta = '';

    /**
     * Crea el objeto. Si se manda una fila de la base de datos, copia sus columnas.
     * Ej: $objeto = new Cliente($fila);
     *
     * @param array $fila
     */
    public function __construct($fila = [])
    {
        if (isset($fila['nombre_receptor'])) {
            $this->nombreReceptor = (string) $fila['nombre_receptor'];
        }
        if (isset($fila['telefono'])) {
            $this->telefono = (string) $fila['telefono'];
        }
        if (isset($fila['id_distrito'])) {
            $this->idDistrito = (int) $fila['id_distrito'];
        }
        if (isset($fila['direccion_exacta'])) {
            $this->direccionExacta = (string) $fila['direccion_exacta'];
        }
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
    public function getTelefono()
    {
        return $this->telefono;
    }

    /** @param string $telefono */
    public function setTelefono($telefono)
    {
        $this->telefono = (string) $telefono;
    }

    /** @return int */
    public function getIdDistrito()
    {
        return $this->idDistrito;
    }

    /** @param int $idDistrito */
    public function setIdDistrito($idDistrito)
    {
        $this->idDistrito = (int) $idDistrito;
    }

    /** @return string */
    public function getDireccionExacta()
    {
        return $this->direccionExacta;
    }

    /** @param string $direccionExacta */
    public function setDireccionExacta($direccionExacta)
    {
        $this->direccionExacta = (string) $direccionExacta;
    }
}
