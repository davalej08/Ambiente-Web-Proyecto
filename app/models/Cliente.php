<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Cliente
 * Datos de entrega de un pedido (tabla direccion_entrega): a quién y a dónde se envía.
 * Solo propiedades, getters y setters (las reglas van en los services).
 */

class Cliente
{
    /** @var string Columna nombre_receptor */
    private $nombreReceptor = '';
    /** @var string Columna telefono */
    private $telefono = '';
    /** @var string Columna provincia */
    private $provincia = '';
    /** @var string Columna canton */
    private $canton = '';
    /** @var string Columna distrito */
    private $distrito = '';
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
        if (isset($fila['provincia'])) {
            $this->provincia = (string) $fila['provincia'];
        }
        if (isset($fila['canton'])) {
            $this->canton = (string) $fila['canton'];
        }
        if (isset($fila['distrito'])) {
            $this->distrito = (string) $fila['distrito'];
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

    /** @return string */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /** @param string $provincia */
    public function setProvincia($provincia)
    {
        $this->provincia = (string) $provincia;
    }

    /** @return string */
    public function getCanton()
    {
        return $this->canton;
    }

    /** @param string $canton */
    public function setCanton($canton)
    {
        $this->canton = (string) $canton;
    }

    /** @return string */
    public function getDistrito()
    {
        return $this->distrito;
    }

    /** @param string $distrito */
    public function setDistrito($distrito)
    {
        $this->distrito = (string) $distrito;
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
