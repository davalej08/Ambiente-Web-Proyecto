<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Usuario
 * Persona registrada en Ventica (FIDE_USUARIO_TB). El correo va en FIDE_CORREO_TB,
 * el teléfono en FIDE_TELEFONO_TB y la dirección en FIDE_DIRECCION_TB.
 * Solo propiedades, getters y setters (las reglas van en los services).
 */

class Usuario
{
    /** @var int Columna id_usuario */
    private $idUsuario = 0;
    /** @var int Columna id_rol */
    private $idRol = 0;
    /** @var string Columna cedula */
    private $cedula = '';
    /** @var string Columna nombre_pila */
    private $nombre = '';
    /** @var string Columna primer_apellido */
    private $primerApellido = '';
    /** @var string Columna segundo_apellido */
    private $segundoApellido = '';
    /** @var string Columna correo */
    private $correo = '';
    /** @var string Columna contrasena */
    private $contrasena = '';
    /** @var string Columna telefono */
    private $telefono = '';
    /** @var int Columna id_distrito */
    private $idDistrito = 0;
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
        if (isset($fila['cedula'])) {
            $this->cedula = (string) $fila['cedula'];
        }
        if (isset($fila['nombre_pila'])) {
            $this->nombre = (string) $fila['nombre_pila'];
        }
        if (isset($fila['primer_apellido'])) {
            $this->primerApellido = (string) $fila['primer_apellido'];
        }
        if (isset($fila['segundo_apellido'])) {
            $this->segundoApellido = (string) $fila['segundo_apellido'];
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
        if (isset($fila['id_distrito'])) {
            $this->idDistrito = (int) $fila['id_distrito'];
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
    public function getCedula()
    {
        return $this->cedula;
    }

    /** @param string $cedula */
    public function setCedula($cedula)
    {
        $this->cedula = (string) $cedula;
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
    public function getPrimerApellido()
    {
        return $this->primerApellido;
    }

    /** @param string $primerApellido */
    public function setPrimerApellido($primerApellido)
    {
        $this->primerApellido = (string) $primerApellido;
    }

    /** @return string */
    public function getSegundoApellido()
    {
        return $this->segundoApellido;
    }

    /** @param string $segundoApellido */
    public function setSegundoApellido($segundoApellido)
    {
        $this->segundoApellido = (string) $segundoApellido;
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
