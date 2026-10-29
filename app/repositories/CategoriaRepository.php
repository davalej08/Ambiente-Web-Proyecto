<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * CategoriaRepository
 * Acceso a la tabla categoria mediante procedimientos almacenados.
 */

class CategoriaRepository extends Repository
{
    /** @return array Categorías con cantidad de productos y de marcas */
    public function listar()
    {
        return $this->consultar('CALL sp_categoria_listar()');
    }

    /**
     * @param int $idCategoria
     * @return array|null
     */
    public function obtener($idCategoria)
    {
        return $this->consultarUno('CALL sp_categoria_obtener(?)', [$idCategoria]);
    }

    /**
     * @param string $nombre
     * @return array|null
     */
    public function obtenerPorNombre($nombre)
    {
        return $this->consultarUno('CALL sp_categoria_obtener_por_nombre(?)', [$nombre]);
    }

    /**
     * @param Categoria $categoria
     * @return int Id de la categoría nueva
     */
    public function crear($categoria)
    {
        return $this->insertar('CALL sp_categoria_insertar(?, ?, ?, ?)', [
            $categoria->getNombre(),
            $categoria->getIcono(),
            $categoria->getDescripcion(),
            $categoria->getArtesanal(),
        ]);
    }

    /** @param Categoria $categoria */
    public function actualizar($categoria)
    {
        $this->ejecutar('CALL sp_categoria_actualizar(?, ?, ?, ?)', [
            $categoria->getIdCategoria(),
            $categoria->getNombre(),
            $categoria->getDescripcion(),
            $categoria->getArtesanal(),
        ]);
    }

    /** @param int $idCategoria */
    public function eliminar($idCategoria)
    {
        $this->ejecutar('CALL sp_categoria_eliminar(?)', [$idCategoria]);
    }
}
