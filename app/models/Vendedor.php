<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Vendedor
 * Emprendimiento de un usuario vendedor (FIDE_EMPRENDIMIENTO_TB).
 * metodosPago: códigos separados por coma (FIDE_EMPRENDIMIENTO_METODO_PAGO_TB).
 * Solo propiedades, getters y setters (las reglas van en los services).
 */

class Vendedor
{
    /** @var int Columna id_emprendimiento */
    private $idEmprendimiento = 0;
    /** @var int Columna id_usuario */
    private $idUsuario = 0;
    /** @var string Columna nombre_negocio */
    private $nombreNegocio = '';
    /** @var string Columna descripcion */
    private $descripcion = '';
    /** @var int Columna id_distrito */
    private $idDistrito = 0;
    /** @var string Columna otras_senas */
    private $otrasSenas = '';
    /** @var string Columna logo */
    private $logo = '';
    /** @var string Columna metodos_pago */
    private $metodosPago = 'sinpe';
    /** @var string Columna estado_verificacion */
    private $estadoVerificacion = 'pendiente';

    /**
     * Crea el objeto. Si se manda una fila de la base de datos, copia sus columnas.
     * Ej: $objeto = new Vendedor($fila);
     *
     * @param array $fila
     */
    public function __construct($fila = [])
    {
        if (isset($fila['id_emprendimiento'])) {
            $this->idEmprendimiento = (int) $fila['id_emprendimiento'];
        }
        if (isset($fila['id_usuario'])) {
            $this->idUsuario = (int) $fila['id_usuario'];
        }
        if (isset($fila['nombre_negocio'])) {
            $this->nombreNegocio = (string) $fila['nombre_negocio'];
        }
        if (isset($fila['descripcion'])) {
            $this->descripcion = (string) $fila['descripcion'];
        }
        if (isset($fila['id_distrito'])) {
            $this->idDistrito = (int) $fila['id_distrito'];
        }
        if (isset($fila['otras_senas'])) {
            $this->otrasSenas = (string) $fila['otras_senas'];
        }
        if (isset($fila['logo'])) {
            $this->logo = (string) $fila['logo'];
        }
        if (isset($fila['metodos_pago'])) {
            $this->metodosPago = (string) $fila['metodos_pago'];
        }
        if (isset($fila['estado_verificacion'])) {
            $this->estadoVerificacion = (string) $fila['estado_verificacion'];
        }
    }

    /** @return int */
    public function getIdEmprendimiento()
    {
        return $this->idEmprendimiento;
    }

    /** @param int $idEmprendimiento */
    public function setIdEmprendimiento($idEmprendimiento)
    {
        $this->idEmprendimiento = (int) $idEmprendimiento;
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
    public function getNombreNegocio()
    {
        return $this->nombreNegocio;
    }

    /** @param string $nombreNegocio */
    public function setNombreNegocio($nombreNegocio)
    {
        $this->nombreNegocio = (string) $nombreNegocio;
    }

    /** @return string */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /** @param string $descripcion */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = (string) $descripcion;
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
    public function getOtrasSenas()
    {
        return $this->otrasSenas;
    }

    /** @param string $otrasSenas */
    public function setOtrasSenas($otrasSenas)
    {
        $this->otrasSenas = (string) $otrasSenas;
    }

    /** @return string */
    public function getLogo()
    {
        return $this->logo;
    }

    /** @param string $logo */
    public function setLogo($logo)
    {
        $this->logo = (string) $logo;
    }

    /** @return string */
    public function getMetodosPago()
    {
        return $this->metodosPago;
    }

    /** @param string $metodosPago */
    public function setMetodosPago($metodosPago)
    {
        $this->metodosPago = (string) $metodosPago;
    }

    /** @return string */
    public function getEstadoVerificacion()
    {
        return $this->estadoVerificacion;
    }

    /** @param string $estadoVerificacion */
    public function setEstadoVerificacion($estadoVerificacion)
    {
        $this->estadoVerificacion = (string) $estadoVerificacion;
    }
}
