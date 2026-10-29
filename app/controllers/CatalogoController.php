<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * CatalogoController
 * Catálogo público: inicio, búsqueda y filtros (RF-09, RF-11), vitrina del
 * emprendimiento, Historias de Artesanos y páginas informativas.
 */

class CatalogoController extends Controller
{
    /** @var ProductoService */
    private $productos;

    public function __construct()
    {
        $this->productos = new ProductoService();
    }

    /** GET /  (landing) */
    public function inicio()
    {
        $this->vista('general/landing', [
            'categorias' => $this->productos->categoriasInicio(),
        ]);
    }

    /** GET catalogo?q=&provincia=&categoria[]=&min=&max=&pago=&orden=&pagina= */
    public function catalogo()
    {
        $filtros = $this->productos->leerFiltros($_GET);
        $pagina = ctype_digit($this->parametro('pagina')) ? (int) $this->parametro('pagina') : 1;
        $resultado = $this->productos->catalogo($filtros, $pagina);

        $this->vista('producto/listado', [
            'productos'        => $resultado['productos'],
            'totalProductos'   => $resultado['totalProductos'],
            'paginaActual'     => $resultado['paginaActual'],
            'totalPaginas'     => $resultado['totalPaginas'],
            'categoriasFiltro' => $this->productos->categoriasFiltro(),
            'favoritosIds'     => (new UsuarioService())->idsFavoritos(Sesion::rol() === ROL_CLIENTE ? Sesion::idUsuario() : 0),
        ]);
    }

    /**
     * GET producto/detalle?id=<producto>  o  producto/detalle?emprendimiento=<id>
     * Muestra la vitrina del emprendimiento dueño del producto.
     */
    public function detalle()
    {
        $vendedores = new VendedorService();
        $idEmprendimiento = (int) $this->parametro('emprendimiento', '0');

        if ($idEmprendimiento === 0) {
            $producto = $this->productos->obtener((int) $this->parametro('id', '0'));
            if ($producto !== null) {
                $idEmprendimiento = (int) $producto['id_emprendimiento'];
            }
        }

        $emprendimiento = $idEmprendimiento > 0 ? $vendedores->obtener($idEmprendimiento) : null;

        // Solo se muestran vitrinas aprobadas y activas (el admin y el dueño sí pueden verla)
        $puedeVerla = $emprendimiento !== null
            && (($emprendimiento['estado_verificacion'] === 'aprobado' && $emprendimiento['estado_usuario'] === 'activo')
                || Sesion::rol() === ROL_ADMIN
                || Sesion::idEmprendimiento() === $idEmprendimiento);

        if (!$puedeVerla) {
            $this->noEncontrada();
            return;
        }

        $datos = $this->productos->vitrina($emprendimiento);
        $datos['favoritosIds'] = (new UsuarioService())->idsFavoritos(Sesion::rol() === ROL_CLIENTE ? Sesion::idUsuario() : 0);
        $this->vista('producto/detalle', $datos);
    }

    /** GET historias  (Historias de Artesanos) */
    public function historias()
    {
        $this->vista('general/historias', [
            'historias' => (new VendedorService())->historias(),
        ]);
    }

    /** GET plantilla  (guía de componentes del equipo) */
    public function plantilla()
    {
        $this->vista('general/plantilla');
    }

    /** GET terminos */
    public function terminos()
    {
        $this->vista('general/terminos');
    }

    /** GET ayuda  (preguntas frecuentes) */
    public function ayuda()
    {
        $this->vista('general/ayuda');
    }

    /** GET contacto */
    public function contacto()
    {
        $usuario = Sesion::usuario();
        $datos = [];
        if ($usuario !== null) {
            $datos = ['nombre' => $usuario['nombre'], 'correo' => $usuario['correo']];
        }
        $this->vista('general/contacto', ['datos' => $datos]);
    }

    /** POST contacto: valida y guarda el mensaje como correo para el equipo */
    public function enviarContacto()
    {
        $this->validarCsrf('contacto');

        $datos = [
            'nombre'  => $this->entrada('nombre'),
            'correo'  => $this->entrada('correo'),
            'motivo'  => $this->entrada('motivo'),
            'mensaje' => $this->entrada('mensaje'),
        ];
        $motivos = ['compra', 'vender', 'cuenta', 'otro'];
        $errores = [];

        if (!Validador::texto($datos['nombre'], 3, 100)) {
            $errores['nombre'] = 'Escribí tu nombre.';
        }
        if (!Validador::correo($datos['correo'])) {
            $errores['correo'] = 'Escribí un correo válido.';
        }
        if (!in_array($datos['motivo'], $motivos, true)) {
            $errores['motivo'] = 'Escogé un motivo.';
        }
        if (!Validador::texto($datos['mensaje'], 15, 1000)) {
            $errores['mensaje'] = 'Contanos un poco más (mínimo 15 caracteres).';
        }

        if (count($errores) > 0) {
            $this->vista('general/contacto', ['datos' => $datos, 'errores' => $errores]);
            return;
        }

        (new CorreoService())->enviar(CORREO_REMITENTE, 'Contacto: ' . $datos['motivo'], 'contacto', $datos);
        Sesion::mensaje('exito', 'Recibimos tu mensaje. Te respondemos al correo en menos de 24 horas.');
        $this->redirigir('contacto');
    }
}
