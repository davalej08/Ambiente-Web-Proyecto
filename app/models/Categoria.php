<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Categoria
 * Categoría del catálogo (tabla categoria).
 * Solo propiedades, getters y setters (las reglas van en los services).
 */

class Categoria
{
    /** @var int Columna id_categoria */
    private $idCategoria = 0;
    /** @var string Columna nombre */
    private $nombre = '';
    /** @var string Columna icono */
    private $icono = '';
    /** @var string Columna descripcion */
    private $descripcion = '';
    /** @var int Columna artesanal */
    private $artesanal = 1;

    /**
     * Crea el objeto. Si se manda una fila de la base de datos, copia sus columnas.
     * Ej: $objeto = new Categoria($fila);
     *
     * @param array $fila
     */
    public function __construct($fila = [])
    {
        if (isset($fila['id_categoria'])) {
            $this->idCategoria = (int) $fila['id_categoria'];
        }
        if (isset($fila['nombre'])) {
            $this->nombre = (string) $fila['nombre'];
        }
        if (isset($fila['icono'])) {
            $this->icono = (string) $fila['icono'];
        }
        if (isset($fila['descripcion'])) {
            $this->descripcion = (string) $fila['descripcion'];
        }
        if (isset($fila['artesanal'])) {
            $this->artesanal = (int) $fila['artesanal'];
        }
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
    public function getIcono()
    {
        return $this->icono;
    }

    /** @param string $icono */
    public function setIcono($icono)
    {
        $this->icono = (string) $icono;
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
    public function getArtesanal()
    {
        return $this->artesanal;
    }

    /** @param int $artesanal */
    public function setArtesanal($artesanal)
    {
        $this->artesanal = (int) $artesanal;
    }
}
