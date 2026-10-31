<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * ClienteController
 * Perfil del cliente: datos, pedidos y favoritos.
 */

class ClienteController extends Controller
{
    /** @var UsuarioService */
    private $usuarios;

    public function __construct()
    {
        $this->usuarios = new UsuarioService();
    }

    /** GET usuario/perfil */
    public function perfil()
    {
        $this->requiereRol(ROL_CLIENTE);
        $this->mostrarPerfil([], []);
    }

    /**
     * Carga la pantalla de perfil con todo lo que necesita.
     *
     * @param array $datosFormulario Lo que escribió la persona (si hubo errores)
     * @param array $errores
     */
    private function mostrarPerfil($datosFormulario, $errores)
    {
        $idUsuario = Sesion::idUsuario();
        $usuario = $this->usuarios->datosPerfil($idUsuario);

        // Si hubo errores se vuelve a mostrar lo que la persona escribió
        foreach ($datosFormulario as $campo => $valor) {
            $usuario[$campo] = $valor;
        }

        $ubicacion = new UbicacionService();
        $this->vista('usuario/perfil', [
            'usuario'          => $usuario,
            'ubicaciones'      => $ubicacion->opciones(),
            'ubicacionElegida' => $ubicacion->elegidos(count($datosFormulario) > 0 ? $datosFormulario : $usuario),
            'pedidos'   => $this->usuarios->pedidosDelCliente($idUsuario),
            'favoritos' => $this->usuarios->favoritos($idUsuario),
            'errores'   => $errores,
        ]);
    }

    /** POST usuario/perfil/guardar */
    public function guardarPerfil()
    {
        $this->requiereRol(ROL_CLIENTE);
        $this->validarCsrf('usuario/perfil');

        $datos = [
            'nombre'           => $this->entrada('nombre'),
            'primer_apellido'  => $this->entrada('primer_apellido'),
            'segundo_apellido' => $this->entrada('segundo_apellido'),
            'correo'           => $this->entrada('correo'),
            'telefono'         => $this->entrada('telefono'),
            'provincia'        => $this->entrada('provincia'),
            'canton'           => $this->entrada('canton'),
            'distrito'         => $this->entrada('distrito'),
            'direccion'        => $this->entrada('direccion'),
        ];
        $errores = $this->usuarios->actualizarPerfil(Sesion::idUsuario(), $datos);

        if (count($errores) > 0) {
            $this->mostrarPerfil($datos, $errores);
            return;
        }

        Sesion::actualizarNombre(trim($datos['nombre'] . ' ' . $datos['primer_apellido'] . ' ' . $datos['segundo_apellido']));
        Sesion::mensaje('exito', 'Tus datos se guardaron.');
        $this->redirigir('usuario/perfil');
    }

    /** POST favoritos/alternar  (corazón del catálogo y de la vitrina) */
    public function alternarFavorito()
    {
        $this->validarCsrf('catalogo');

        if (Sesion::rol() !== ROL_CLIENTE) {
            $_SESSION['volver_a'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ruta('catalogo');
            Sesion::mensaje('info', 'Iniciá sesión con tu cuenta de cliente para guardar favoritos.');
            $this->redirigir('auth/login');
        }

        $idProducto = (int) $this->entrada('id_producto', '0');
        if ((new ProductoService())->obtener($idProducto) === null) {
            $this->volver('catalogo');
        }

        $quedoFavorito = $this->usuarios->alternarFavorito(Sesion::idUsuario(), $idProducto);
        Sesion::mensaje('exito', $quedoFavorito ? 'Guardado en tus favoritos.' : 'Quitado de tus favoritos.');
        $this->volver('catalogo');
    }

    /** POST usuario/favoritos/quitar  (desde Mi perfil) */
    public function quitarFavorito()
    {
        $this->requiereRol(ROL_CLIENTE);
        $this->validarCsrf('usuario/perfil');

        $this->usuarios->quitarFavorito(Sesion::idUsuario(), (int) $this->entrada('id_producto', '0'));
        Sesion::mensaje('info', 'Quitamos el producto de tus favoritos.');
        $this->redirigir('usuario/perfil#favoritos');
    }
}
