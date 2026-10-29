<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * CarritoController
 * Carrito de compras: agregar, quitar y ver productos (RF-14).
 */

class CarritoController extends Controller
{
    /** @var CarritoService */
    private $carrito;

    public function __construct()
    {
        $this->carrito = new CarritoService();
    }

    /** GET carrito */
    public function ver()
    {
        $contenido = $this->carrito->contenido();

        // Datos de entrega: los del último intento (si hubo error) o los guardados en el perfil
        $datosEntrega = [];
        if (isset($_SESSION['datos_entrega'])) {
            $datosEntrega = $_SESSION['datos_entrega'];
            unset($_SESSION['datos_entrega']);
        } elseif (Sesion::usuario() !== null) {
            $usuario = (new UsuarioService())->obtener(Sesion::idUsuario());
            $datosEntrega = [
                'nombre'    => $usuario['nombre'],
                'telefono'  => (string) $usuario['telefono'],
                'provincia' => (string) $usuario['provincia'],
                'direccion' => (string) $usuario['direccion'],
            ];
        }

        $errores = [];
        if (isset($_SESSION['errores_carrito'])) {
            $errores = $_SESSION['errores_carrito'];
            unset($_SESSION['errores_carrito']);
        }

        $this->vista('carrito/carrito', [
            'grupos'       => $contenido['grupos'],
            'costoEnvio'   => COSTO_ENVIO,
            'recomendados' => $this->carrito->recomendados(),
            'datosEntrega' => $datosEntrega,
            'errores'      => $errores,
        ]);
    }

    /** POST carrito/agregar */
    public function agregar()
    {
        $this->validarCsrf('catalogo');

        $idProducto = (int) $this->entrada('id_producto', '0');
        $cantidad = (int) $this->entrada('cantidad', '1');
        $error = $this->carrito->agregar($idProducto, $cantidad);

        if ($error !== '') {
            Sesion::mensaje('aviso', $error);
        } else {
            Sesion::mensaje('exito', 'Producto agregado al carrito.');
        }
        $this->volver('carrito');
    }

    /** POST carrito/quitar */
    public function quitar()
    {
        $this->validarCsrf('carrito');

        $this->carrito->quitar((int) $this->entrada('id_producto', '0'));
        Sesion::mensaje('info', 'Quitamos el producto de tu carrito.');
        $this->redirigir('carrito');
    }
}
