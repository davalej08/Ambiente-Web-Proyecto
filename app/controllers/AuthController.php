<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * AuthController
 * Inicio de sesión, cierre de sesión, registro y recuperación de contraseña (RF-01, RF-02, RF-04, RF-07).
 */

class AuthController extends Controller
{
    /** @var RegistroService */
    private $registro;

    public function __construct()
    {
        $this->registro = new RegistroService();
    }

    /** Si ya hay sesión, no tiene sentido ver el login: va a su pantalla de inicio. */
    private function siYaInicioSesion()
    {
        if (Sesion::usuario() !== null) {
            $this->redirigir(INICIO_POR_ROL[Sesion::rol()]);
        }
    }

    /** GET auth/login */
    public function login()
    {
        $this->siYaInicioSesion();
        $this->vista('registro/login');
    }

    /** POST auth/login */
    public function procesarLogin()
    {
        $this->validarCsrf('auth/login');

        $datos = [
            'correo'     => $this->entrada('correo'),
            'recordarme' => $this->entrada('recordarme'),
        ];
        $resultado = $this->registro->iniciarSesion($datos['correo'], $this->entrada('contrasena'));

        if (count($resultado['errores']) > 0) {
            $this->vista('registro/login', ['datos' => $datos, 'errores' => $resultado['errores']]);
            return;
        }

        Sesion::iniciarUsuario($resultado['usuario']);
        Sesion::mensaje('exito', '¡Hola, ' . explode(' ', $resultado['usuario']['nombre'])[0] . '! Qué bueno verte de nuevo.');

        // Si venía de una página que pedía sesión (ej. el carrito), vuelve a esa
        if (isset($_SESSION['volver_a']) && Sesion::rol() === ROL_CLIENTE) {
            $destino = $_SESSION['volver_a'];
            unset($_SESSION['volver_a']);
            header('Location: ' . $destino);
            exit;
        }

        $this->redirigir(INICIO_POR_ROL[Sesion::rol()]);
    }

    /** GET auth/logout */
    public function logout()
    {
        Sesion::cerrar();
        Sesion::iniciar();
        Sesion::mensaje('info', 'Cerraste sesión. ¡Pura vida!');
        $this->redirigir('');
    }

    /** GET auth/registro */
    public function registro()
    {
        $this->siYaInicioSesion();
        $this->vista('registro/registro_cliente');
    }

    /** POST auth/registro */
    public function procesarRegistro()
    {
        $this->validarCsrf('auth/registro');

        $datos = [
            'cedula'           => Validador::formatoCedula($this->entrada('cedula')),
            'nombre'           => $this->entrada('nombre'),
            'primer_apellido'  => $this->entrada('primer_apellido'),
            'segundo_apellido' => $this->entrada('segundo_apellido'),
            'correo'     => $this->entrada('correo'),
            'telefono'   => $this->entrada('telefono'),
            'contrasena' => $this->entrada('contrasena'),
            'terminos'   => $this->entrada('terminos'),
        ];
        $resultado = $this->registro->registrarCliente($datos);

        if (count($resultado['errores']) > 0) {
            unset($datos['contrasena']);
            $this->vista('registro/registro_cliente', ['datos' => $datos, 'errores' => $resultado['errores']]);
            return;
        }

        // La cuenta queda lista y con la sesión iniciada
        Sesion::iniciarUsuario($resultado['usuario']);
        Sesion::mensaje('exito', '¡Te damos la bienvenida a Ventica! Tu cuenta quedó lista.');
        $this->redirigir('catalogo');
    }

    /** GET auth/registro-vendedor */
    public function registroVendedor()
    {
        $this->siYaInicioSesion();
        $this->vistaRegistroVendedor([], []);
    }

    /**
     * Pantalla de registro del emprendedor con los selects de provincia, cantón y distrito.
     *
     * @param array $datos
     * @param array $errores
     */
    private function vistaRegistroVendedor($datos, $errores)
    {
        $ubicacion = new UbicacionService();
        $this->vista('registro/registro_vendedor', [
            'datos'            => $datos,
            'errores'          => $errores,
            'ubicaciones'      => $ubicacion->opciones(),
            'ubicacionElegida' => $ubicacion->elegidos($datos),
        ]);
    }

    /** POST auth/registro-vendedor */
    public function procesarRegistroVendedor()
    {
        $this->validarCsrf('auth/registro-vendedor');

        $datos = [
            'cedula'           => Validador::formatoCedula($this->entrada('cedula')),
            'nombre'           => $this->entrada('nombre'),
            'primer_apellido'  => $this->entrada('primer_apellido'),
            'segundo_apellido' => $this->entrada('segundo_apellido'),
            'correo'      => $this->entrada('correo'),
            'telefono'    => $this->entrada('telefono'),
            'contrasena'  => $this->entrada('contrasena'),
            'negocio'     => $this->entrada('negocio'),
            'provincia'   => $this->entrada('provincia'),
            'canton'      => $this->entrada('canton'),
            'distrito'    => $this->entrada('distrito'),
            'otras_senas' => $this->entrada('otras_senas'),
            'descripcion' => $this->entrada('descripcion'),
            'terminos'    => $this->entrada('terminos'),
        ];
        $resultado = $this->registro->registrarVendedor($datos);

        if (count($resultado['errores']) > 0) {
            unset($datos['contrasena']);
            $this->vistaRegistroVendedor($datos, $resultado['errores']);
            return;
        }

        Sesion::mensaje('exito', 'Solicitud enviada. Te avisaremos por correo cuando el equipo de Ventica la apruebe.');
        $this->redirigir('');
    }

    /** GET auth/recuperar */
    public function recuperar()
    {
        $this->vista('registro/recuperar_contrasena');
    }

    /** POST auth/recuperar */
    public function procesarRecuperar()
    {
        $this->validarCsrf('auth/recuperar');

        $datos = ['correo' => $this->entrada('correo')];
        $resultado = $this->registro->solicitarRecuperacion($datos['correo']);

        if (count($resultado['errores']) > 0) {
            $this->vista('registro/recuperar_contrasena', ['datos' => $datos, 'errores' => $resultado['errores']]);
            return;
        }

        // Siempre se muestra "Revisá tu correo" (exista o no la cuenta).
        // En modo de prueba (sin servidor de correo) también se muestra el enlace.
        $this->vista('registro/recuperar_contrasena', [
            'datos'      => $datos,
            'enviado'    => true,
            'enlaceDemo' => CORREO_ACTIVO ? '' : $resultado['enlace'],
        ]);
    }

    /** GET auth/cambiar-contrasena?token=... */
    public function cambiarContrasena()
    {
        $token = $this->parametro('token');
        $errores = [];
        if ($this->registro->usuarioDelToken($token) === null) {
            $errores['general'] = 'El enlace venció o ya se usó. Pedí uno nuevo desde "¿Olvidaste tu contraseña?".';
        }
        $this->vista('registro/cambiar_contrasena', ['token' => $token, 'errores' => $errores]);
    }

    /** POST auth/cambiar-contrasena */
    public function procesarCambiarContrasena()
    {
        $this->validarCsrf('auth/recuperar');

        $token = $this->entrada('token');
        $resultado = $this->registro->cambiarContrasena($token, $this->entrada('contrasena'), $this->entrada('confirmacion'));

        if (count($resultado['errores']) > 0) {
            $this->vista('registro/cambiar_contrasena', ['token' => $token, 'errores' => $resultado['errores']]);
            return;
        }

        $this->vista('registro/cambiar_contrasena', ['token' => '', 'cambiada' => true]);
    }
}
