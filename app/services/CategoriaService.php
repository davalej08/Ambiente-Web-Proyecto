<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * CategoriaService
 * Gestión de categorías del catálogo (RF-10).
 * Regla: una categoría que tiene productos no se puede eliminar.
 */

class CategoriaService
{
    /** @var CategoriaRepository */
    private $categorias;

    public function __construct()
    {
        $this->categorias = new CategoriaRepository();
    }

    /** @return array Categorías con la forma que usa categoria/listado.php */
    public function listarParaAdmin()
    {
        $filas = $this->categorias->listar();
        $lista = [];
        foreach ($filas as $fila) {
            $lista[] = [
                'id'          => (int) $fila['id_categoria'],
                'nombre'      => $fila['nombre'],
                'icono'       => (string) $fila['icono'],
                'descripcion' => $fila['descripcion'] !== null ? $fila['descripcion'] : 'Sin descripción.',
                'productos'   => (int) $fila['productos'],
                'marcas'      => (int) $fila['marcas'],
                'artesanal'   => (int) $fila['artesanal'] === 1,
            ];
        }
        return $lista;
    }

    /**
     * Crea o edita una categoría.
     *
     * @param array $datos ['id_categoria', 'nombre', 'descripcion', 'artesanal']
     * @return array ['error' => '', 'mensaje' => '']
     */
    public function guardar($datos)
    {
        $idCategoria = (int) $datos['id_categoria'];
        $nombre = $datos['nombre'];

        if (!Validador::texto($nombre, 3, 40)) {
            return ['error' => 'El nombre de la categoría debe tener entre 3 y 40 caracteres.', 'mensaje' => ''];
        }
        if (!Validador::texto($datos['descripcion'], 0, 120)) {
            return ['error' => 'La descripción es muy larga (máximo 120 caracteres).', 'mensaje' => ''];
        }

        $repetida = $this->categorias->obtenerPorNombre($nombre);
        if ($repetida !== null && (int) $repetida['id_categoria'] !== $idCategoria) {
            return ['error' => 'Ya existe una categoría llamada ' . $nombre . '.', 'mensaje' => ''];
        }

        $categoria = new Categoria();
        $categoria->setIdCategoria($idCategoria);
        $categoria->setNombre($nombre);
        $categoria->setDescripcion($datos['descripcion'] !== '' ? $datos['descripcion'] : 'Sin descripción.');
        $categoria->setArtesanal($datos['artesanal'] === '1' ? 1 : 0);
        $categoria->setIcono('destellos');

        if ($idCategoria === 0) {
            $this->categorias->crear($categoria);
            return ['error' => '', 'mensaje' => 'La categoría ' . $nombre . ' se agregó al catálogo.'];
        }

        if ($this->categorias->obtener($idCategoria) === null) {
            return ['error' => 'Esa categoría no existe.', 'mensaje' => ''];
        }
        $this->categorias->actualizar($categoria);
        return ['error' => '', 'mensaje' => 'Los cambios de ' . $nombre . ' se guardaron.'];
    }

    /**
     * Elimina una categoría solo si no tiene productos.
     *
     * @param int $idCategoria
     * @return array ['error' => '', 'mensaje' => '']
     */
    public function eliminar($idCategoria)
    {
        $fila = $this->categorias->obtener($idCategoria);
        if ($fila === null) {
            return ['error' => 'Esa categoría no existe.', 'mensaje' => ''];
        }
        if ((int) $fila['productos'] > 0) {
            return ['error' => 'No se puede eliminar ' . $fila['nombre'] . ' porque tiene ' . $fila['productos'] . ' productos. Primero hay que moverlos a otra categoría.', 'mensaje' => ''];
        }

        $this->categorias->eliminar($idCategoria);
        return ['error' => '', 'mensaje' => 'La categoría ' . $fila['nombre'] . ' se eliminó.'];
    }
}
