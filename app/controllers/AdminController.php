<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * AdminController
 * Gestión de vendedores, clientes y solicitudes de registro (RF-03, RF-05).
 */

class AdminController extends Controller
{
    /** @var VendedorService */
    private $vendedores;
    /** @var UsuarioService */
    private $usuarios;

    public function __construct()
    {
        $this->vendedores = new VendedorService();
        $this->usuarios   = new UsuarioService();
    }

    /**
     * Nombre e iniciales del administrador para la barra lateral.
     *
     * @return array
     */
    public static function datosAdmin()
    {
        $usuario = Sesion::usuario();
        $pendientes = (new VendedorRepository())->listarPorEstado('pendiente');
        return [
            'pendientes' => count($pendientes),
            'nombre'    => $usuario['nombre'],
            'correo'    => $usuario['correo'],
            'iniciales' => iniciales($usuario['nombre']),
        ];
    }

    /** GET admin/dashboard */
    public function dashboard()
    {
        $this->requiereRol(ROL_ADMIN);
        $this->vista('constante/dashboard_admin', [
            'resumen' => (new ReporteService())->inicioAdmin(),
            'admin'   => self::datosAdmin(),
        ]);
    }

    /** GET admin/solicitudes */
    public function solicitudes()
    {
        $this->requiereRol(ROL_ADMIN);
        $this->vista('usuario_rol/solicitudes', [
            'solicitudes' => $this->vendedores->solicitudesPendientes(),
            'admin'       => self::datosAdmin(),
        ]);
    }

    /** GET admin/solicitudes/detalle?id= */
    public function detalleSolicitud()
    {
        $this->requiereRol(ROL_ADMIN);

        $solicitud = $this->vendedores->detalleSolicitud((int) $this->parametro('id', '0'));
        if ($solicitud === null) {
            $this->noEncontrada();
            return;
        }

        $this->vista('usuario_rol/solicitud_detalle', [
            'solicitud' => $solicitud,
            'tokenCsrf' => $_SESSION['csrf_token'],
            'admin'     => self::datosAdmin(),
        ]);
    }

    /** POST admin/solicitudes/resolver  (aprobar o rechazar) */
    public function resolverSolicitud()
    {
        $this->requiereRol(ROL_ADMIN);
        $this->validarCsrf('admin/solicitudes');

        $idEmprendimiento = (int) $this->entrada('id_solicitud', '0');
        $resultado = $this->vendedores->resolverSolicitud($idEmprendimiento, $this->entrada('decision'), $this->entrada('observaciones'));

        if ($resultado['error'] !== '') {
            Sesion::mensaje('error', $resultado['error']);
            $this->redirigir('admin/solicitudes/detalle?id=' . $idEmprendimiento);
        }

        Sesion::mensaje('exito', $resultado['mensaje']);
        $this->redirigir('admin/solicitudes');
    }

    /** GET admin/emprendimientos */
    public function emprendimientos()
    {
        $this->requiereRol(ROL_ADMIN);

        $emprendimientos = $this->vendedores->emprendimientosAprobados();
        $activos = 0;
        foreach ($emprendimientos as $emprendimiento) {
            if ($emprendimiento['activo']) {
                $activos++;
            }
        }

        $this->vista('usuario_rol/vendedores', [
            'emprendimientos' => $emprendimientos,
            'totalActivos'    => $activos,
            'busquedaInicial' => $this->parametro('q'),
            'admin'           => self::datosAdmin(),
        ]);
    }

    /** POST admin/emprendimientos/estado  (suspender o reactivar) */
    public function estadoEmprendimiento()
    {
        $this->requiereRol(ROL_ADMIN);
        $this->validarCsrf('admin/emprendimientos');

        $mensaje = $this->vendedores->alternarEstado((int) $this->entrada('id_emprendimiento', '0'));
        Sesion::mensaje('exito', $mensaje);
        $this->redirigir('admin/emprendimientos');
    }

    /** GET admin/usuarios */
    public function usuarios()
    {
        $this->requiereRol(ROL_ADMIN);
        $this->vista('usuario_rol/clientes', [
            'usuarios' => $this->usuarios->listarParaAdmin(),
            'totales'  => $this->usuarios->totalesParaAdmin(),
            'admin'    => self::datosAdmin(),
        ]);
    }

    /** POST admin/usuarios/guardar  (invitar o editar) */
    public function guardarUsuario()
    {
        $this->requiereRol(ROL_ADMIN);
        $this->validarCsrf('admin/usuarios');

        $resultado = $this->usuarios->guardarDesdeAdmin([
            'id_usuario' => $this->entrada('id_usuario', '0'),
            'cedula'     => Validador::formatoCedula($this->entrada('cedula')),
            'nombre'     => $this->entrada('nombre'),
            'correo'     => $this->entrada('correo'),
            'rol'        => $this->entrada('rol'),
            'contacto'   => $this->entrada('contacto'),
            'contrasena' => $this->entrada('contrasena'),
        ]);

        if (count($resultado['errores']) > 0) {
            // Se muestra el primer error (el formulario está en un panel que se abre con JS)
            $primero = array_values($resultado['errores']);
            Sesion::mensaje('error', $primero[0]);
        } else {
            Sesion::mensaje('exito', $resultado['mensaje']);
        }
        $this->redirigir('admin/usuarios');
    }

    /** POST admin/usuarios/estado  (suspender o reactivar) */
    public function estadoUsuario()
    {
        $this->requiereRol(ROL_ADMIN);
        $this->validarCsrf('admin/usuarios');

        Sesion::mensaje('exito', $this->usuarios->alternarEstado((int) $this->entrada('id_usuario', '0')));
        $this->redirigir('admin/usuarios');
    }

    /** GET admin/buscar?q=  (buscador de la barra superior) */
    public function buscar()
    {
        $this->requiereRol(ROL_ADMIN);
        $this->redirigir('admin/emprendimientos?q=' . urlencode($this->parametro('q')));
    }
}
