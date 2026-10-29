<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Producto
 * Producto que publica un emprendimiento (tabla producto).
 * Solo propiedades, getters y setters (las reglas van en los services).
 */

class Producto
{
    /** @var int Columna id_producto */
    private $idProducto = 0;
    /** @var int Columna id_emprendimiento */
    private $idEmprendimiento = 0;
    /** @var int Columna id_categoria */
    private $idCategoria = 0;
    /** @var string Columna nombre */
    private $nombre = '';
    /** @var string Columna descripcion */
    private $descripcion = '';
    /** @var float Columna precio */
    private $precio = 0;
    /** @var int Columna cantidad_disponible */
    private $cantidadDisponible = 0;
    /** @var string Columna estado */
    private $estado = 'activo';
    /** @var string Columna imagen */
    private $imagen = '';

    /**
     * Crea el objeto. Si se manda una fila de la base de datos, copia sus columnas.
     * Ej: $objeto = new Producto($fila);
     *
     * @param array $fila
     */
    public function __construct($fila = [])
    {
        if (isset($fila['id_producto'])) {
            $this->idProducto = (int) $fila['id_producto'];
        }
        if (isset($fila['id_emprendimiento'])) {
            $this->idEmprendimiento = (int) $fila['id_emprendimiento'];
        }
        if (isset($fila['id_categoria'])) {
            $this->idCategoria = (int) $fila['id_categoria'];
        }
        if (isset($fila['nombre'])) {
            $this->nombre = (string) $fila['nombre'];
        }
        if (isset($fila['descripcion'])) {
            $this->descripcion = (string) $fila['descripcion'];
        }
        if (isset($fila['precio'])) {
            $this->precio = (float) $fila['precio'];
        }
        if (isset($fila['cantidad_disponible'])) {
            $this->cantidadDisponible = (int) $fila['cantidad_disponible'];
        }
        if (isset($fila['estado'])) {
            $this->estado = (string) $fila['estado'];
        }
        if (isset($fila['imagen'])) {
            $this->imagen = (string) $fila['imagen'];
        }
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
    public function getIdCategoria()
    {
        return $this->idCategoria;
    }

    /** @param int $idCategoria */
    public function setIdCategoria($idCategoria)
    {
        $this->idCategoria = (int) $idCategoria;
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
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /** @param string $descripcion */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = (string) $descripcion;
    }

    /** @return float */
    public function getPrecio()
    {
        return $this->precio;
    }

    /** @param float $precio */
    public function setPrecio($precio)
    {
        $this->precio = (float) $precio;
    }

    /** @return int */
    public function getCantidadDisponible()
    {
        return $this->cantidadDisponible;
    }

    /** @param int $cantidadDisponible */
    public function setCantidadDisponible($cantidadDisponible)
    {
        $this->cantidadDisponible = (int) $cantidadDisponible;
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

    /** @return string */
    public function getImagen()
    {
        return $this->imagen;
    }

    /** @param string $imagen */
    public function setImagen($imagen)
    {
        $this->imagen = (string) $imagen;
    }
}
