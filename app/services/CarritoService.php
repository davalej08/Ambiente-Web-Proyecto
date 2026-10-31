<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * CarritoService
 * Lógica del carrito de compras (RF-14).
 *
 * El carrito vive en la sesión: $_SESSION['carrito'] = [id_producto => cantidad].
 * Si la persona inició sesión, además se guarda en FIDE_CARRITO_TB, así lo
 * encuentra otro día o en otro dispositivo. Lo que tenía antes de iniciar
 * sesión se junta con lo guardado.
 * Los precios NUNCA se guardan: siempre se leen de la base de datos.
 */

class CarritoService
{
    /** @var ProductoRepository */
    private $productos;
    /** @var CarritoRepository */
    private $guardado;

    public function __construct()
    {
        $this->productos = new ProductoRepository();
        $this->guardado = new CarritoRepository();

        if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $this->sincronizar();
    }

    /**
     * La primera vez que se usa el carrito con una sesión iniciada, junta el
     * carrito de la sesión con el guardado en la base de datos.
     */
    private function sincronizar()
    {
        $idUsuario = Sesion::idUsuario();
        if ($idUsuario === 0 || (isset($_SESSION['carrito_de']) && $_SESSION['carrito_de'] === $idUsuario)) {
            return;
        }

        $filas = $this->guardado->listar($idUsuario);
        foreach ($filas as $fila) {
            $idProducto = (int) $fila['id_producto'];
            if (!isset($_SESSION['carrito'][$idProducto])) {
                $_SESSION['carrito'][$idProducto] = (int) $fila['cantidad'];
            }
        }

        // Lo que se agregó antes de iniciar sesión también queda guardado
        foreach ($_SESSION['carrito'] as $idProducto => $cantidad) {
            $this->guardado->guardarProducto($idUsuario, $idProducto, $cantidad);
        }

        $_SESSION['carrito_de'] = $idUsuario;
    }

    /**
     * Guarda el cambio en la base de datos si hay sesión iniciada.
     *
     * @param int $idProducto
     * @param int $cantidad 0 = quitar
     */
    private function guardar($idProducto, $cantidad)
    {
        if (Sesion::idUsuario() > 0) {
            $this->guardado->guardarProducto(Sesion::idUsuario(), $idProducto, $cantidad);
        }
    }

    /**
     * Agrega un producto (o suma a la cantidad que ya tenía).
     *
     * @param int $idProducto
     * @param int $cantidad
     * @return string Mensaje de error o '' si se agregó
     */
    public function agregar($idProducto, $cantidad)
    {
        $producto = $this->productos->obtener($idProducto);

        if ($producto === null || $producto['estado'] !== 'activo' || $producto['estado_verificacion'] !== 'aprobado') {
            return 'Ese producto ya no está disponible.';
        }

        $actual = isset($_SESSION['carrito'][$idProducto]) ? $_SESSION['carrito'][$idProducto] : 0;
        $nueva = $actual + max(1, $cantidad);

        if ($nueva > (int) $producto['cantidad_disponible']) {
            $nueva = (int) $producto['cantidad_disponible'];
            if ($nueva === $actual) {
                return 'Ya tenés en el carrito todas las unidades disponibles de ' . $producto['nombre'] . '.';
            }
        }

        $_SESSION['carrito'][$idProducto] = $nueva;
        $this->guardar($idProducto, $nueva);
        return '';
    }

    /** @param int $idProducto */
    public function quitar($idProducto)
    {
        unset($_SESSION['carrito'][$idProducto]);
        $this->guardar($idProducto, 0);
    }

    /**
     * Guarda las cantidades que la persona dejó en la pantalla del carrito.
     * Los productos que ya no vienen en el formulario se quitan.
     *
     * @param array $cantidades [id_producto => cantidad] que llegan en $_POST['cantidades']
     */
    public function actualizarCantidades($cantidades)
    {
        $nuevo = [];
        foreach ($_SESSION['carrito'] as $idProducto => $cantidadAnterior) {
            if (isset($cantidades[$idProducto]) && Validador::entero($cantidades[$idProducto], 1)) {
                $nuevo[$idProducto] = min(99, (int) $cantidades[$idProducto]);
                $this->guardar($idProducto, $nuevo[$idProducto]);
            } else {
                $this->guardar($idProducto, 0);
            }
        }
        $_SESSION['carrito'] = $nuevo;
    }

    /** Vacía el carrito (después de confirmar el pedido). */
    public function vaciar()
    {
        $_SESSION['carrito'] = [];
        if (Sesion::idUsuario() > 0) {
            $this->guardado->vaciar(Sesion::idUsuario());
        }
    }

    /** @return array Ids de los productos en el carrito */
    public function ids()
    {
        return array_keys($_SESSION['carrito']);
    }

    /**
     * Contenido del carrito agrupado por emprendimiento, con precios de la base de datos.
     * Si un producto se agotó o se desactivó, se quita del carrito.
     *
     * @return array ['grupos' => [...], 'subtotal' => total de productos]
     */
    public function contenido()
    {
        $filas = $this->productos->listarPorIds($this->ids());
        $grupos = [];
        $subtotal = 0;
        $vigentes = [];

        foreach ($filas as $fila) {
            $idProducto = (int) $fila['id_producto'];
            $stock = (int) $fila['cantidad_disponible'];

            if ($fila['estado'] !== 'activo' || $stock === 0) {
                continue;
            }

            $cantidad = min($_SESSION['carrito'][$idProducto], $stock);
            $vigentes[$idProducto] = $cantidad;

            $idGrupo = (int) $fila['id_emprendimiento'];
            if (!isset($grupos[$idGrupo])) {
                $grupos[$idGrupo] = [
                    'id'        => $idGrupo,
                    'nombre'    => $fila['nombre_negocio'],
                    'ubicacion' => $fila['ubicacion'],
                    'dueno'     => $fila['dueno'],
                    'imagen'    => imagenO($fila['logo'] !== null ? $fila['logo'] : $fila['imagen']),
                    'sinpe'     => (string) $fila['telefono'],
                    'productos' => [],
                ];
            }

            $grupos[$idGrupo]['productos'][] = [
                'id'       => $idProducto,
                'nombre'   => $fila['nombre'],
                'nota'     => mb_strlen($fila['descripcion']) > 90 ? mb_substr($fila['descripcion'], 0, 90) . '…' : $fila['descripcion'],
                'etiqueta' => $fila['categoria'],
                'detalle'  => $stock <= 5 ? 'Últimas ' . $stock . ' unidades' : 'Hecho a mano',
                'precio'   => (float) $fila['precio'],
                'cantidad' => $cantidad,
                'stock'    => $stock,
                'imagen'   => imagenO($fila['imagen']),
            ];
            $subtotal = $subtotal + $fila['precio'] * $cantidad;
        }

        // El carrito queda solo con lo que todavía se puede comprar (también el guardado)
        foreach ($_SESSION['carrito'] as $idProducto => $cantidadAnterior) {
            if (!isset($vigentes[$idProducto])) {
                $this->guardar($idProducto, 0);
            } elseif ($vigentes[$idProducto] !== $cantidadAnterior) {
                $this->guardar($idProducto, $vigentes[$idProducto]);
            }
        }
        $_SESSION['carrito'] = $vigentes;

        // array_values() quita las llaves (id) para que la vista recorra 0, 1, 2…
        return ['grupos' => array_values($grupos), 'subtotal' => $subtotal];
    }

    /**
     * Productos sugeridos que no están en el carrito.
     *
     * @return array
     */
    public function recomendados()
    {
        $filas = $this->productos->recomendados(4, $this->ids());
        $lista = [];
        foreach ($filas as $fila) {
            $lista[] = [
                'id'             => (int) $fila['id_producto'],
                'nombre'         => $fila['nombre'],
                'emprendimiento' => $fila['nombre_negocio'] . ' • ' . trim(explode(',', $fila['ubicacion'])[0]),
                'precio'         => (float) $fila['precio'],
                'imagen'         => imagenO($fila['imagen']),
            ];
        }
        return $lista;
    }
}
