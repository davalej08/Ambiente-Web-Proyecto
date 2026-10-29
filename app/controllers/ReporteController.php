<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * ReporteController
 * Reportes de ventas del vendedor y del administrador (RF-13).
 */

class ReporteController extends Controller
{
    /** @var ReporteService */
    private $reportes;

    public function __construct()
    {
        $this->reportes = new ReporteService();
    }

    /**
     * Datos del reporte del vendedor para el período pedido.
     *
     * @return array
     */
    private function datosVendedor()
    {
        $this->requiereRol(ROL_EMPRENDEDOR);
        $idEmprendimiento = Sesion::idEmprendimiento();

        $periodos = ['semana', '30dias', 'mes', 'anio'];
        $periodo = in_array($this->parametro('periodo'), $periodos, true) ? $this->parametro('periodo') : '30dias';
        $inicio = $this->reportes->inicioVendedor($idEmprendimiento);

        return [
            'periodo'        => $periodo,
            'reporte'        => $this->reportes->reporteVendedor($idEmprendimiento, $periodo),
            'tienda'         => (new VendedorService())->tienda($idEmprendimiento),
            'notificaciones' => $inicio['por_confirmar'],
        ];
    }

    /** GET vendedor/reportes?periodo= */
    public function vendedor()
    {
        $this->vista('vendedor/reportes', $this->datosVendedor());
    }

    /** GET vendedor/reportes/pdf?periodo=  (abre el reporte y la ventana de imprimir / guardar PDF) */
    public function vendedorImprimir()
    {
        $datos = $this->datosVendedor();
        $datos['imprimir'] = true;
        $this->vista('vendedor/reportes', $datos);
    }

    /** GET admin/reportes?periodo=7|30|90 */
    public function admin()
    {
        $this->requiereRol(ROL_ADMIN);

        $dias = (int) $this->parametro('periodo', '30');
        if ($dias !== 7 && $dias !== 90) {
            $dias = 30;
        }

        $reporte = $this->reportes->reporteAdmin($dias);
        $this->vista('consultas/reportes_admin', [
            'indicadores'     => $reporte['indicadores'],
            'ventasCategoria' => $reporte['ventasCategoria'],
            'impacto'         => $reporte['impacto'],
            'cobertura'       => $reporte['cobertura'],
            'admin'           => AdminController::datosAdmin(),
        ]);
    }
}
