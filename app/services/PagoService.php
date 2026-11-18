<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * PagoService
 * Validación del pago y creación del pedido (RF-15).
 *
 * Ventica no cobra comisión: el cliente le paga directo a cada emprendedor
 * (SINPE Móvil, tarjeta o efectivo contra entrega). Aquí se valida, se
 * guarda el pedido con sus líneas y su dirección, y se rebaja el inventario.
 */

class PagoService
{
    /** @var PedidoRepository */
    private $pedidos;

    /** Métodos de pago que acepta la tabla pedido */
    private $metodos = ['sinpe', 'tarjeta', 'efectivo'];

    /** Texto de cada método para las pantallas */
    private $nombresMetodo = ['sinpe' => 'SINPE Móvil', 'tarjeta' => 'Tarjeta', 'efectivo' => 'Efectivo contra entrega'];

    public function __construct()
    {
        $this->pedidos = new PedidoRepository();
    }

    /**
     * Valida los datos de entrega del formulario del carrito.
     *
     * @param array $datos ['nombre', 'telefono', 'distrito', 'direccion', 'pago'] (distrito = id)
     * @return array Errores ['campo' => 'mensaje']
     */
    public function validarEntrega($datos)
    {
        $errores = [];

        if (!Validador::texto($datos['nombre'], 5, 100)) {
            $errores['nombre'] = 'Escribí el nombre completo de quien recibe.';
        }
        if (!Validador::telefono($datos['telefono'])) {
            $errores['telefono'] = 'El teléfono debe tener 8 dígitos.';
        }
        if ((new UbicacionService())->distrito($datos['distrito']) === null) {
            $errores['distrito'] = 'Elegí la provincia, el cantón y el distrito.';
        }
        if (!Validador::texto($datos['direccion'], 15, 255)) {
            $errores['direccion'] = 'Escribí señas claras (mínimo 15 caracteres).';
        }
        if (!in_array($datos['pago'], $this->metodos, true)) {
            $errores['pago'] = 'Escogé una forma de pago.';
        }

        return $errores;
    }

    /**
     * Guarda el pedido completo en una transacción (todo o nada).
     *
     * @param int $idUsuario
     * @param array $grupos   Contenido del carrito (CarritoService::contenido)
     * @param array $datos    Datos de entrega ya validados
     * @return int Id del pedido nuevo
     */
    public function crearPedido($idUsuario, $grupos, $datos)
    {
        $subtotal = 0;
        foreach ($grupos as $grupo) {
            foreach ($grupo['productos'] as $item) {
                $subtotal = $subtotal + $item['precio'] * $item['cantidad'];
            }
        }

        $entrega = new Cliente();
        $entrega->setNombreReceptor($datos['nombre']);
        $entrega->setTelefono(Validador::formatoTelefono($datos['telefono']));
        $entrega->setIdDistrito((int) $datos['distrito']);
        $entrega->setDireccionExacta($datos['direccion']);

        $pedido = new Pedido();
        $pedido->setIdUsuario($idUsuario);
        $pedido->setMetodoPago($datos['pago']);
        $pedido->setNombreReceptor($entrega->getNombreReceptor());
        $pedido->setTelefonoReceptor($entrega->getTelefono());
        $pedido->setTotal($subtotal + COSTO_ENVIO);

        $this->pedidos->iniciarTransaccion();
        try {
            // Primero la dirección de entrega (FIDE_DIRECCION_TB), después el pedido que la usa
            $idDireccion = (new UbicacionRepository())->crearDireccion($entrega->getIdDistrito(), $entrega->getDireccionExacta());
            $pedido->setIdDireccion($idDireccion);
            $idPedido = $this->pedidos->crear($pedido);

            foreach ($grupos as $grupo) {
                foreach ($grupo['productos'] as $item) {
                    $detalle = new DetallePedido();
                    $detalle->setIdPedido($idPedido);
                    $detalle->setIdProducto($item['id']);
                    $detalle->setCantidad($item['cantidad']);
                    $detalle->setPrecioUnitario($item['precio']);
                    $this->pedidos->agregarDetalle($detalle);
                }
            }

            $this->pedidos->confirmarTransaccion();
        } catch (Exception $error) {
            $this->pedidos->deshacerTransaccion();
            throw $error;
        }

        return $idPedido;
    }

    /**
     * Datos para la pantalla de confirmación, agrupados por emprendimiento.
     *
     * @param int $idPedido
     * @param int $idUsuario Solo se muestra si el pedido es de esta persona
     * @return array|null
     */
    public function confirmacion($idPedido, $idUsuario)
    {
        $encabezado = $this->pedidos->obtener($idPedido, $idUsuario);
        if ($encabezado === null) {
            return null;
        }

        $grupos = [];
        $lineas = $this->pedidos->listarDetalle($idPedido);
        foreach ($lineas as $linea) {
            $idGrupo = (int) $linea['id_emprendimiento'];
            if (!isset($grupos[$idGrupo])) {
                $grupos[$idGrupo] = [
                    'emprendimiento' => $linea['nombre_negocio'],
                    'dueno'          => $linea['dueno'],
                    'sinpe'          => (string) $linea['telefono'],
                    'productos'      => [],
                ];
            }
            $grupos[$idGrupo]['productos'][] = [
                'nombre'   => $linea['nombre'],
                'cantidad' => (int) $linea['cantidad'],
                'precio'   => (float) $linea['precio_unitario'],
                'imagen'   => imagenO($linea['imagen']),
            ];
        }

        $productos = 0;
        foreach ($lineas as $linea) {
            $productos = $productos + $linea['subtotal'];
        }

        return [
            'id'      => (int) $encabezado['id_pedido'],
            'fecha'   => fechaCorta($encabezado['fecha_pedido']),
            'metodo'  => $encabezado['metodo_pago'],
            'envio'   => (float) $encabezado['total'] - $productos,
            'grupos'  => array_values($grupos),
            'entrega' => [
                'nombre'    => (string) $encabezado['nombre_receptor'],
                'telefono'  => (string) $encabezado['telefono'],
                'direccion' => $encabezado['distrito'] . ', ' . $encabezado['canton'] . ', ' . $encabezado['provincia'] . '. ' . $encabezado['direccion_exacta'],
            ],
        ];
    }

    /**
     * @param string $metodo sinpe | tarjeta | efectivo
     * @return string Texto para mostrar. Ej: SINPE Móvil
     */
    public function nombreMetodo($metodo)
    {
        return isset($this->nombresMetodo[$metodo]) ? $this->nombresMetodo[$metodo] : $metodo;
    }
}
