<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * PagoController
 * Confirmación del pedido y pantalla de pago por SINPE de cada emprendimiento (RF-15).
 */

class PagoController extends Controller
{
    /** @var PagoService */
    private $pago;

    public function __construct()
    {
        $this->pago = new PagoService();
    }

    /** POST carrito/checkout: crea el pedido con lo que hay en el carrito */
    public function checkout()
    {
        $this->validarCsrf('carrito');

        // Para comprar hay que tener cuenta de cliente
        if (Sesion::usuario() === null) {
            $_SESSION['volver_a'] = ruta('carrito');
            Sesion::mensaje('info', 'Iniciá sesión o creá tu cuenta para confirmar el pedido. Tu carrito se guarda.');
            $this->redirigir('auth/login');
        }
        if (Sesion::rol() !== ROL_CLIENTE) {
            Sesion::mensaje('aviso', 'Las compras se hacen con una cuenta de cliente.');
            $this->redirigir('carrito');
        }

        $carrito = new CarritoService();
        $cantidades = isset($_POST['cantidades']) && is_array($_POST['cantidades']) ? $_POST['cantidades'] : [];
        $carrito->actualizarCantidades($cantidades);
        $contenido = $carrito->contenido();

        if (count($contenido['grupos']) === 0) {
            Sesion::mensaje('aviso', 'Tu carrito está vacío.');
            $this->redirigir('carrito');
        }

        $datos = [
            'nombre'    => $this->entrada('nombre'),
            'telefono'  => $this->entrada('telefono'),
            'provincia' => $this->entrada('provincia'),
            'canton'    => $this->entrada('canton'),
            'distrito'  => $this->entrada('distrito'),
            'direccion' => $this->entrada('direccion'),
            'pago'      => $this->entrada('pago'),
        ];

        $errores = $this->pago->validarEntrega($datos);
        if (count($errores) > 0) {
            // Se vuelve al carrito con los datos y los errores
            $_SESSION['datos_entrega'] = $datos;
            $_SESSION['errores_carrito'] = $errores;
            Sesion::mensaje('error', 'Revisá los datos de entrega marcados en rojo.');
            $this->redirigir('carrito');
        }

        $idPedido = $this->pago->crearPedido(Sesion::idUsuario(), $contenido['grupos'], $datos);
        $carrito->vaciar();

        // Si el cliente no tenía dirección guardada, se guarda esta para la próxima compra
        $usuarios = new UsuarioService();
        $usuario = $usuarios->obtener(Sesion::idUsuario());
        if ($usuario['id_direccion'] === null) {
            $usuarios->guardarDireccion(Sesion::idUsuario(), (int) $datos['distrito'], $datos['direccion']);
        }

        $this->redirigir('carrito/confirmacion?id=' . $idPedido);
    }

    /** GET carrito/confirmacion?id= */
    public function confirmacion()
    {
        $this->requiereRol(ROL_CLIENTE);

        $pedido = $this->pago->confirmacion((int) $this->parametro('id', '0'), Sesion::idUsuario());
        if ($pedido === null) {
            $this->noEncontrada();
            return;
        }

        $this->vista('carrito/confirmacion', ['pedido' => $pedido]);
    }
}
