<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * ResenaService
 * HU-04: el cliente califica (1 a 5 estrellas) y comenta los productos que ya recibió.
 * Solo se puede reseñar un producto de una línea de pedido ENTREGADA, una vez por
 * producto (si vuelve a enviar el formulario, se actualiza la reseña).
 */

class ResenaService
{
    /** @var ClienteRepository */
    private $clientes;

    public function __construct()
    {
        $this->clientes = new ClienteRepository();
    }

    /**
     * Productos entregados de un pedido para la pantalla "Calificá tu compra".
     *
     * @param int $idPedido
     * @param int $idUsuario
     * @return array Lista de productos ['id', 'nombre', 'imagen', 'emprendimiento', 'calificacion', 'comentario']
     */
    public function productosDelPedido($idPedido, $idUsuario)
    {
        $filas = $this->clientes->listarParaResena($idPedido, $idUsuario);
        $lista = [];

        foreach ($filas as $fila) {
            $lista[] = [
                'id'             => (int) $fila['id_producto'],
                'nombre'         => $fila['nombre'],
                'imagen'         => imagenO($fila['imagen']),
                'emprendimiento' => $fila['nombre_negocio'],
                'calificacion'   => $fila['calificacion'] === null ? 0 : (int) $fila['calificacion'],
                'comentario'     => (string) $fila['comentario'],
            ];
        }
        return $lista;
    }

    /**
     * Valida y guarda una reseña.
     *
     * @param int $idUsuario
     * @param array $datos ['id_producto', 'calificacion', 'comentario']
     * @return string Mensaje de error, o '' si se guardó
     */
    public function guardar($idUsuario, $datos)
    {
        if (!Validador::entero($datos['id_producto'], 1)) {
            return 'No encontramos ese producto.';
        }
        if (!Validador::entero($datos['calificacion'], 1) || (int) $datos['calificacion'] > 5) {
            return 'Elegí de 1 a 5 estrellas.';
        }
        if (!Validador::texto($datos['comentario'], 0, 500)) {
            return 'El comentario puede tener hasta 500 caracteres.';
        }

        $guardada = $this->clientes->guardarResena(
            $idUsuario,
            (int) $datos['id_producto'],
            (int) $datos['calificacion'],
            trim($datos['comentario'])
        );

        if (!$guardada) {
            return 'Solo podés calificar productos que ya recibiste.';
        }
        return '';
    }
}
