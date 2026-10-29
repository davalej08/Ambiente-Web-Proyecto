<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * ProductoRepository
 * Acceso a las tablas producto, imagen_producto y resena mediante procedimientos almacenados.
 */

class ProductoRepository extends Repository
{
    /**
     * Catálogo con filtros (RF-09, RF-11).
     *
     * @param array $filtros ['texto', 'provincia', 'categorias' (ej. '1,3'), 'minimo', 'maximo', 'pago', 'orden']
     * @param int $limite   Productos por página
     * @param int $desde    Cuántos saltarse (paginación)
     * @return array
     */
    public function buscar($filtros, $limite, $desde)
    {
        return $this->consultar('CALL sp_producto_buscar(?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $filtros['texto'],
            $filtros['provincia'],
            $filtros['categorias'],
            $filtros['minimo'],
            $filtros['maximo'],
            $filtros['pago'],
            $filtros['orden'],
            $limite,
            $desde,
        ]);
    }

    /**
     * Total de productos que cumplen los filtros.
     *
     * @param array $filtros
     * @return int
     */
    public function contarBusqueda($filtros)
    {
        $fila = $this->consultarUno('CALL sp_producto_contar_busqueda(?, ?, ?, ?, ?, ?)', [
            $filtros['texto'],
            $filtros['provincia'],
            $filtros['categorias'],
            $filtros['minimo'],
            $filtros['maximo'],
            $filtros['pago'],
        ]);
        return (int) $fila['total'];
    }

    /**
     * @param int $idEmprendimiento
     * @param bool $soloActivos true para la vitrina pública
     * @return array
     */
    public function listarPorEmprendimiento($idEmprendimiento, $soloActivos)
    {
        return $this->consultar('CALL sp_producto_listar_por_emprendimiento(?, ?)', [$idEmprendimiento, $soloActivos ? 1 : 0]);
    }

    /**
     * @param int $idProducto
     * @return array|null
     */
    public function obtener($idProducto)
    {
        return $this->consultarUno('CALL sp_producto_obtener(?)', [$idProducto]);
    }

    /**
     * Productos del carrito con los datos de su emprendimiento.
     *
     * @param array $ids Ej: [1, 5, 8]
     * @return array
     */
    public function listarPorIds($ids)
    {
        if (count($ids) === 0) {
            return [];
        }
        return $this->consultar('CALL sp_producto_listar_por_ids(?)', [implode(',', $ids)]);
    }

    /**
     * @param int $limite
     * @param array $excluir Ids que ya están en el carrito
     * @return array
     */
    public function recomendados($limite, $excluir)
    {
        return $this->consultar('CALL sp_producto_recomendados(?, ?)', [$limite, implode(',', $excluir)]);
    }

    /**
     * @param Producto $producto
     * @return int Id del producto nuevo
     */
    public function crear($producto)
    {
        return $this->insertar('CALL sp_producto_insertar(?, ?, ?, ?, ?, ?)', [
            $producto->getIdEmprendimiento(),
            $producto->getIdCategoria(),
            $producto->getNombre(),
            $producto->getDescripcion(),
            $producto->getPrecio(),
            $producto->getCantidadDisponible(),
        ]);
    }

    /** @param Producto $producto */
    public function actualizar($producto)
    {
        $this->ejecutar('CALL sp_producto_actualizar(?, ?, ?, ?, ?, ?, ?)', [
            $producto->getIdProducto(),
            $producto->getIdEmprendimiento(),
            $producto->getIdCategoria(),
            $producto->getNombre(),
            $producto->getDescripcion(),
            $producto->getPrecio(),
            $producto->getCantidadDisponible(),
        ]);
    }

    /**
     * @param int $idProducto
     * @param int $idEmprendimiento Dueño (solo cambia si el producto es suyo)
     * @param string $estado activo | inactivo | agotado
     */
    public function cambiarEstado($idProducto, $idEmprendimiento, $estado)
    {
        $this->ejecutar('CALL sp_producto_cambiar_estado(?, ?, ?)', [$idProducto, $idEmprendimiento, $estado]);
    }

    /**
     * @param int $idProducto
     * @param string $url Ruta dentro de img/uploads. Ej: productos/p12_ab12.jpg
     */
    public function guardarImagen($idProducto, $url)
    {
        $this->ejecutar('CALL sp_imagen_producto_guardar(?, ?)', [$idProducto, $url]);
    }

    /**
     * Reseñas de los productos de un emprendimiento.
     *
     * @param int $idEmprendimiento
     * @param int $limite
     * @return array
     */
    public function listarResenas($idEmprendimiento, $limite)
    {
        return $this->consultar('CALL sp_resena_listar_por_emprendimiento(?, ?)', [$idEmprendimiento, $limite]);
    }
}
