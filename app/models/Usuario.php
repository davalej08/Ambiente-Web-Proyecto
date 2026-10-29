<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Usuario
 * Persona registrada en Ventica (tabla usuario).
 * Solo propiedades, getters y setters (las reglas van en los services).
 */

class Usuario
{
    /** @var int Columna id_usuario */
    private $idUsuario = 0;
    /** @var int Columna id_rol */
    private $idRol = 0;
    /** @var string Columna nombre */
    private $nombre = '';
    /** @var string Columna correo */
    private $correo = '';
    /** @var string Columna contrasena */
    private $contrasena = '';
    /** @var string Columna telefono */
    private $telefono = '';
    /** @var string Columna provincia */
    private $provincia = '';
    /** @var string Columna direccion */
    private $direccion = '';
    /** @var string Columna estado */
    private $estado = 'activo';

    /**
     * Crea el objeto. Si se manda una fila de la base de datos, copia sus columnas.
     * Ej: $objeto = new Usuario($fila);
     *
     * @param array $fila
     */
    public function __construct($fila = [])
    {
        if (isset($fila['id_usuario'])) {
            $this->idUsuario = (int) $fila['id_usuario'];
        }
        if (isset($fila['id_rol'])) {
            $this->idRol = (int) $fila['id_rol'];
        }
        if (isset($fila['nombre'])) {
            $this->nombre = (string) $fila['nombre'];
        }
        if (isset($fila['correo'])) {
            $this->correo = (string) $fila['correo'];
        }
        if (isset($fila['contrasena'])) {
            $this->contrasena = (string) $fila['contrasena'];
        }
        if (isset($fila['telefono'])) {
            $this->telefono = (string) $fila['telefono'];
        }
        if (isset($fila['provincia'])) {
            $this->provincia = (string) $fila['provincia'];
        }
        if (isset($fila['direccion'])) {
            $this->direccion = (string) $fila['direccion'];
        }
        if (isset($fila['estado'])) {
            $this->estado = (string) $fila['estado'];
        }
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
    public function getIdRol()
    {
        return $this->idRol;
    }

    /** @param int $idRol */
    public function setIdRol($idRol)
    {
        $this->idRol = (int) $idRol;
    }

    /** @return string */
    public function getNombre()
    {
        return $this->nombre;
    }

    /** @param string $nombre */
    public function setNombre($nombre)
    {
        $this->nombre = (string) $nombre;
    }

    /** @return string */
    public function getCorreo()
    {
        return $this->correo;
    }

    /** @param string $correo */
    public function setCorreo($correo)
    {
        $this->correo = (string) $correo;
    }

    /** @return string */
    public function getContrasena()
    {
        return $this->contrasena;
    }

    /** @param string $contrasena */
    public function setContrasena($contrasena)
    {
        $this->contrasena = (string) $contrasena;
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
    public function getDireccion()
    {
        return $this->direccion;
    }

    /** @param string $direccion */
    public function setDireccion($direccion)
    {
        $this->direccion = (string) $direccion;
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
