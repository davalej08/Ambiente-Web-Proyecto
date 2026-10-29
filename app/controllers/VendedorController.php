<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * VendedorController
 * Portal del vendedor: perfil, productos y pedidos (RF-12, RF-14).
 * Todas las pantallas piden la sesión de un Emprendedor aprobado.
 */

class VendedorController extends Controller
{
    /** @var VendedorService */
    private $vendedores;
    /** @var ProductoService */
    private $productos;
    /** @var int Emprendimiento del vendedor en sesión */
    private $idEmprendimiento = 0;

    public function __construct()
    {
        $this->vendedores = new VendedorService();
        $this->productos  = new ProductoService();
    }

    /** Revisa el rol y guarda el id del emprendimiento del vendedor. */
    private function entrarAlPortal()
    {
        $this->requiereRol(ROL_EMPRENDEDOR);
        $this->idEmprendimiento = Sesion::idEmprendimiento();
    }

    /**
     * Datos que comparten todas las pantallas del portal (barra lateral y campana).
     *
     * @return array
     */
    private function datosPortal()
    {
        $resumen = (new ReporteService())->inicioVendedor($this->idEmprendimiento);
        return [
            'tienda'         => $this->vendedores->tienda($this->idEmprendimiento),
            'notificaciones' => $resumen['por_confirmar'],
        ];
    }

    /** GET vendedor/dashboard */
    public function dashboard()
    {
        $this->entrarAlPortal();

        $datos = $this->datosPortal();
        $datos['resumen'] = (new ReporteService())->inicioVendedor($this->idEmprendimiento);
        $datos['pedidosRecientes'] = $this->vendedores->pedidosPorConfirmar($this->idEmprendimiento, 3);
        $this->vista('constante/dashboard_vendedor', $datos);
    }

    /** GET vendedor/productos */
    public function productos()
    {
        $this->entrarAlPortal();

        $productos = $this->productos->productosDelVendedor($this->idEmprendimiento);
        $datos = $this->datosPortal();
        $datos['productos'] = $productos;
        $datos['totalProductos'] = count($productos);
        $datos['paginaActual'] = 1;
        $datos['totalPaginas'] = 1;
        $this->vista('producto/gestion', $datos);
    }

    /** GET vendedor/productos/nuevo */
    public function nuevoProducto()
    {
        $this->entrarAlPortal();

        $datos = $this->datosPortal();
        $datos['categorias'] = $this->productos->categoriasSelect();
        $this->vista('producto/formulario', $datos);
    }

    /** GET vendedor/productos/editar?id= */
    public function editarProducto()
    {
        $this->entrarAlPortal();

        $producto = $this->productos->productoParaEditar($this->idEmprendimiento, (int) $this->parametro('id', '0'));
        if ($producto === null) {
            Sesion::mensaje('error', 'Ese producto no existe o no es de tu emprendimiento.');
            $this->redirigir('vendedor/productos');
        }

        $datos = $this->datosPortal();
        $datos['categorias'] = $this->productos->categoriasSelect();
        $datos['producto'] = $producto;
        $this->vista('producto/formulario', $datos);
    }

    /** POST vendedor/productos/guardar  y  vendedor/productos/actualizar */
    public function guardarProducto()
    {
        $this->entrarAlPortal();
        $this->validarCsrf('vendedor/productos');

        $datos = [
            'id_producto'  => $this->entrada('id_producto', '0'),
            'nombre'       => $this->entrada('nombre'),
            'id_categoria' => $this->entrada('id_categoria'),
            'precio'       => $this->entrada('precio'),
            'stock'        => $this->entrada('stock'),
            'descripcion'  => $this->entrada('descripcion'),
        ];
        $resultado = $this->productos->guardar($this->idEmprendimiento, $datos);

        if (count($resultado['errores']) > 0) {
            // Se vuelve a mostrar el formulario con lo que escribió y los errores
            $producto = [
                'nombre'       => $datos['nombre'],
                'id_categoria' => (int) $datos['id_categoria'],
                'precio'       => $datos['precio'],
                'stock'        => $datos['stock'],
                'descripcion'  => $datos['descripcion'],
            ];
            if ((int) $datos['id_producto'] > 0) {
                $anterior = $this->productos->productoParaEditar($this->idEmprendimiento, (int) $datos['id_producto']);
                if ($anterior !== null) {
                    $producto['id'] = $anterior['id'];
                    $producto['imagen'] = $anterior['imagen'];
                }
            }

            $vista = $this->datosPortal();
            $vista['categorias'] = $this->productos->categoriasSelect();
            $vista['producto'] = $producto;
            $vista['errores'] = $resultado['errores'];
            $vista['mensaje'] = ['tipo' => 'error', 'texto' => 'Revisá los campos marcados en rojo.'];
            $this->vista('producto/formulario', $vista);
            return;
        }

        $esNuevo = (int) $datos['id_producto'] === 0;
        Sesion::mensaje('exito', $esNuevo ? 'Producto publicado en tu vitrina.' : 'Cambios guardados.');
        $this->redirigir('vendedor/productos');
    }

    /** POST vendedor/productos/disponibilidad  (interruptor Activo / Pausado) */
    public function disponibilidad()
    {
        $this->entrarAlPortal();
        $this->validarCsrf('vendedor/productos');

        $activo = $this->entrada('activo') === '1';
        $this->productos->cambiarDisponibilidad($this->idEmprendimiento, (int) $this->entrada('id_producto', '0'), $activo);
        Sesion::mensaje('exito', $activo ? 'El producto vuelve a estar a la venta.' : 'Producto pausado: ya no aparece en el catálogo.');
        $this->redirigir('vendedor/productos');
    }

    /** GET vendedor/pedidos */
    public function pedidos()
    {
        $this->entrarAlPortal();

        $datos = $this->datosPortal();
        $datos['pedidos'] = $this->vendedores->pedidos($this->idEmprendimiento);
        $this->vista('vendedor/pedidos', $datos);
    }

    /**
     * Cambia el estado del pedido y vuelve a la pantalla anterior.
     *
     * @param string $estado
     * @param string $textoExito
     */
    private function cambiarEstadoPedido($estado, $textoExito)
    {
        $this->entrarAlPortal();
        $this->validarCsrf('vendedor/pedidos');

        $idPedido = (int) $this->entrada('id_pedido', '0');
        if ($this->vendedores->cambiarEstadoPedido($this->idEmprendimiento, $idPedido, $estado)) {
            Sesion::mensaje('exito', str_replace('#', '#CR-' . $idPedido, $textoExito));
        } else {
            Sesion::mensaje('aviso', 'Ese pedido ya estaba actualizado.');
        }
        $this->volver('vendedor/pedidos');
    }

    /** POST vendedor/pedidos/confirmar */
    public function confirmarPedido()
    {
        $this->cambiarEstadoPedido('confirmado', 'Pedido # confirmado. Ya podés prepararlo para el envío.');
    }

    /** POST vendedor/pedidos/entregar */
    public function entregarPedido()
    {
        $this->cambiarEstadoPedido('entregado', 'Pedido # marcado como entregado.');
    }

    /** GET vendedor/pedidos/imprimir?id=  (comprobante para imprimir o guardar en PDF) */
    public function imprimirPedido()
    {
        $this->entrarAlPortal();

        $comprobante = $this->vendedores->comprobante($this->idEmprendimiento, (int) $this->parametro('id', '0'));
        if ($comprobante === null) {
            $this->noEncontrada();
            return;
        }

        $comprobante['tienda'] = $this->vendedores->tienda($this->idEmprendimiento);
        $this->vista('vendedor/comprobante', $comprobante);
    }

    /** GET vendedor/perfil */
    public function perfil()
    {
        $this->entrarAlPortal();

        $datos = $this->datosPortal();
        $datos['perfil'] = $this->vendedores->perfil($this->idEmprendimiento);
        $this->vista('vendedor/perfil', $datos);
    }

    /** POST vendedor/perfil/guardar */
    public function guardarPerfil()
    {
        $this->entrarAlPortal();
        $this->validarCsrf('vendedor/perfil');

        $datos = [
            'nombre'      => $this->entrada('nombre'),
            'descripcion' => $this->entrada('descripcion'),
            'ubicacion'   => $this->entrada('ubicacion'),
            'telefono'    => $this->entrada('telefono'),
            'pagos'       => isset($_POST['pagos']) && is_array($_POST['pagos']) ? $_POST['pagos'] : [],
        ];
        $errores = $this->vendedores->guardarPerfil($this->idEmprendimiento, Sesion::idUsuario(), $datos);

        if (count($errores) > 0) {
            $vista = $this->datosPortal();
            $perfil = $this->vendedores->perfil($this->idEmprendimiento);
            $perfil['nombre'] = $datos['nombre'];
            $perfil['descripcion'] = $datos['descripcion'];
            $perfil['ubicacion'] = $datos['ubicacion'];
            $perfil['telefono'] = $datos['telefono'];
            $perfil['pagos'] = $datos['pagos'];
            $vista['perfil'] = $perfil;
            $vista['errores'] = $errores;
            $vista['mensaje'] = ['tipo' => 'error', 'texto' => 'Revisá los campos marcados en rojo.'];
            $this->vista('vendedor/perfil', $vista);
            return;
        }

        Sesion::mensaje('exito', 'Tu vitrina se actualizó.');
        $this->redirigir('vendedor/perfil');
    }
}
