<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * ReporteRepository
 * Consultas de reportes del vendedor y del administrador (RF-13).
 * Las fechas se mandan como 'AAAA-MM-DD'.
 */

class ReporteRepository extends Repository
{
    /**
     * @param int $idEmprendimiento
     * @param string $desde
     * @param string $hasta
     * @return array Filas ['fecha', 'monto'] de cada día con ventas
     */
    public function ventasDiariasVendedor($idEmprendimiento, $desde, $hasta)
    {
        return $this->consultar('CALL sp_reporte_vendedor_ventas_diarias(?, ?, ?)', [$idEmprendimiento, $desde, $hasta]);
    }

    /** @return array ['ventas', 'pedidos', 'lineas', 'entregadas', 'pendientes'] */
    public function resumenVendedor($idEmprendimiento, $desde, $hasta)
    {
        return $this->consultarUno('CALL sp_reporte_vendedor_resumen(?, ?, ?)', [$idEmprendimiento, $desde, $hasta]);
    }

    /** @return array|null ['nombre', 'unidades'] del producto más vendido */
    public function productoTopVendedor($idEmprendimiento, $desde, $hasta)
    {
        return $this->consultarUno('CALL sp_reporte_vendedor_producto_top(?, ?, ?)', [$idEmprendimiento, $desde, $hasta]);
    }

    /** @return array ['catalogo', 'stock_bajo'] */
    public function inventarioVendedor($idEmprendimiento)
    {
        return $this->consultarUno('CALL sp_reporte_vendedor_inventario(?)', [$idEmprendimiento]);
    }

    /** @return array ['ventas', 'pedidos', 'pedidos_sinpe'] de toda la plataforma */
    public function resumenAdmin($desde, $hasta)
    {
        return $this->consultarUno('CALL sp_reporte_admin_resumen(?, ?)', [$desde, $hasta]);
    }

    /** @return array Filas ['categoria', 'monto'] */
    public function ventasPorCategoria($desde, $hasta)
    {
        return $this->consultar('CALL sp_reporte_admin_ventas_categoria(?, ?)', [$desde, $hasta]);
    }

    /** @return array Filas ['provincia', 'pedidos'] */
    public function pedidosPorProvincia($desde, $hasta)
    {
        return $this->consultar('CALL sp_reporte_admin_ventas_provincia(?, ?)', [$desde, $hasta]);
    }

    /** @return array Números del inicio del panel del administrador */
    public function inicioAdmin()
    {
        return $this->consultarUno('CALL sp_reporte_admin_inicio()');
    }
}
