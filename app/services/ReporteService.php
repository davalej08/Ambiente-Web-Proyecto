<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * ReporteService
 * Cálculo de reportes de ventas (RF-13): portal del vendedor y panel del administrador.
 */

class ReporteService
{
    /** @var ReporteRepository */
    private $reportes;

    public function __construct()
    {
        $this->reportes = new ReporteRepository();
    }

    /**
     * Porcentaje de cambio entre dos montos. Ej: de 100 a 120 = 20
     *
     * @param float $actual
     * @param float $anterior
     * @return int
     */
    private function variacion($actual, $anterior)
    {
        if ($anterior <= 0) {
            return $actual > 0 ? 100 : 0;
        }
        return (int) round(($actual - $anterior) * 100 / $anterior);
    }

    /**
     * Suma las ventas diarias que caen entre dos fechas.
     *
     * @param array $ventasPorDia ['2026-10-01' => 15000, ...]
     * @param string $desde
     * @param string $hasta
     * @return float
     */
    private function sumarEntre($ventasPorDia, $desde, $hasta)
    {
        $suma = 0;
        foreach ($ventasPorDia as $fecha => $monto) {
            if ($fecha >= $desde && $fecha <= $hasta) {
                $suma = $suma + $monto;
            }
        }
        return $suma;
    }

    // ---------------------------------------------------------------
    // Vendedor
    // ---------------------------------------------------------------

    /**
     * Números del inicio del portal del vendedor.
     *
     * @param int $idEmprendimiento
     * @return array ['ventas_mes', 'variacion', 'meta', 'por_confirmar', 'catalogo', 'stock_bajo']
     */
    public function inicioVendedor($idEmprendimiento)
    {
        $inicioMes = date('Y-m-01');
        $hoy = date('Y-m-d');
        $inicioMesAnterior = date('Y-m-01', strtotime('first day of last month'));
        $finMesAnterior = date('Y-m-t', strtotime('first day of last month'));

        $actual = $this->reportes->resumenVendedor($idEmprendimiento, $inicioMes, $hoy);
        $anterior = $this->reportes->resumenVendedor($idEmprendimiento, $inicioMesAnterior, $finMesAnterior);
        $todo = $this->reportes->resumenVendedor($idEmprendimiento, '2000-01-01', $hoy);
        $inventario = $this->reportes->inventarioVendedor($idEmprendimiento);

        // Meta del mes: 20% más de lo que vendió el mes pasado (mínimo ₡100,000)
        $meta = max(100000, ceil((float) $anterior['ventas'] * 1.2 / 10000) * 10000);

        return [
            'ventas_mes'    => (float) $actual['ventas'],
            'variacion'     => $this->variacion((float) $actual['ventas'], (float) $anterior['ventas']),
            'meta'          => $meta,
            'por_confirmar' => (int) $todo['pendientes'],
            'catalogo'      => (int) $inventario['catalogo'],
            'stock_bajo'    => (int) $inventario['stock_bajo'],
        ];
    }

    /**
     * Reporte de ventas del vendedor para un período.
     *
     * @param int $idEmprendimiento
     * @param string $periodo semana | 30dias | mes | anio
     * @return array Con la forma que usa vendedor/reportes.php
     */
    public function reporteVendedor($idEmprendimiento, $periodo)
    {
        $hoy = date('Y-m-d');
        $puntos = [];
        $dias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        $diasLargos = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $mesesLargos = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        // 1. Rango de fechas del período y del período anterior (para la variación)
        if ($periodo === 'semana') {
            $desde = date('Y-m-d', strtotime('-6 days'));
            $anteriorDesde = date('Y-m-d', strtotime('-13 days'));
            $anteriorHasta = date('Y-m-d', strtotime('-7 days'));
            $subtitulo = 'Evolución de ventas en colones (CRC) durante los últimos 7 días';
        } elseif ($periodo === 'mes') {
            $desde = date('Y-m-01');
            $anteriorDesde = date('Y-m-01', strtotime('first day of last month'));
            $anteriorHasta = date('Y-m-t', strtotime('first day of last month'));
            $subtitulo = 'Evolución de ventas en colones (CRC) durante las semanas del mes';
        } elseif ($periodo === 'anio') {
            $desde = date('Y-01-01');
            $anteriorDesde = (date('Y') - 1) . '-01-01';
            $anteriorHasta = (date('Y') - 1) . date('-m-d');
            $subtitulo = 'Evolución de ventas en colones (CRC) mes a mes durante el año';
        } else {
            $desde = date('Y-m-d', strtotime('-29 days'));
            $anteriorDesde = date('Y-m-d', strtotime('-59 days'));
            $anteriorHasta = date('Y-m-d', strtotime('-30 days'));
            $subtitulo = 'Evolución de ventas en colones (CRC) durante las últimas 4 semanas';
        }

        // 2. Ventas por día del período: ['2026-10-01' => 15000, ...]
        $ventasPorDia = [];
        $filas = $this->reportes->ventasDiariasVendedor($idEmprendimiento, $desde, $hoy);
        foreach ($filas as $fila) {
            $ventasPorDia[$fila['fecha']] = (float) $fila['monto'];
        }

        // 3. Puntos de la gráfica
        if ($periodo === 'semana') {
            for ($i = 6; $i >= 0; $i--) {
                $fecha = date('Y-m-d', strtotime('-' . $i . ' days'));
                $numeroDia = (int) date('w', strtotime($fecha));
                $puntos[] = [
                    'etiqueta' => $dias[$numeroDia],
                    'rango'    => $diasLargos[$numeroDia] . ' ' . date('j', strtotime($fecha)),
                    'monto'    => $this->sumarEntre($ventasPorDia, $fecha, $fecha),
                ];
            }
        } elseif ($periodo === 'anio') {
            for ($mes = 1; $mes <= (int) date('n'); $mes++) {
                $inicio = date('Y') . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01';
                $fin = date('Y-m-t', strtotime($inicio));
                $puntos[] = ['etiqueta' => $meses[$mes - 1], 'rango' => $mesesLargos[$mes - 1], 'monto' => $this->sumarEntre($ventasPorDia, $inicio, $fin)];
            }
        } else {
            // Semanas de 7 días desde el inicio del período
            $inicio = $desde;
            $numero = 1;
            while ($inicio <= $hoy) {
                $fin = date('Y-m-d', strtotime($inicio . ' +6 days'));
                if ($fin > $hoy) {
                    $fin = $hoy;
                }
                $mesFin = mesCorto(date('n', strtotime($fin)));
                $puntos[] = [
                    'etiqueta' => 'Semana ' . $numero,
                    'rango'    => 'Sem ' . $numero . ' (' . date('j', strtotime($inicio)) . ' - ' . date('j', strtotime($fin)) . ' ' . $mesFin . ')',
                    'monto'    => $this->sumarEntre($ventasPorDia, $inicio, $fin),
                ];
                $inicio = date('Y-m-d', strtotime($fin . ' +1 day'));
                $numero++;
            }
        }

        // 4. Totales del período
        $resumen = $this->reportes->resumenVendedor($idEmprendimiento, $desde, $hoy);
        $anterior = $this->reportes->resumenVendedor($idEmprendimiento, $anteriorDesde, $anteriorHasta);
        $top = $this->reportes->productoTopVendedor($idEmprendimiento, $desde, $hoy);
        $lineas = (int) $resumen['lineas'];

        return [
            'ventas'     => (float) $resumen['ventas'],
            'variacion'  => $this->variacion((float) $resumen['ventas'], (float) $anterior['ventas']),
            'pedidos'    => (int) $resumen['pedidos'],
            'entregados' => $lineas > 0 ? (int) round((int) $resumen['entregadas'] * 100 / $lineas) : 0,
            'producto'   => $top !== null ? $top['nombre'] : 'Sin ventas todavía',
            'unidades'   => $top !== null ? (int) $top['unidades'] : 0,
            'subtitulo'  => $subtitulo,
            'puntos'     => $puntos,
        ];
    }

    // ---------------------------------------------------------------
    // Administrador
    // ---------------------------------------------------------------

    /**
     * Números del inicio del panel del administrador.
     *
     * @return array Con la forma que usa constante/dashboard_admin.php
     */
    public function inicioAdmin()
    {
        $fila = $this->reportes->inicioAdmin();

        // Provincias donde hay emprendimientos activos
        $provincias = [];
        $artesanales = 0;
        $vendedores = (new VendedorService())->emprendimientosAprobados();
        $categorias = (new CategoriaRepository())->listar();
        $nombresArtesanales = [];
        foreach ($categorias as $categoria) {
            if ((int) $categoria['artesanal'] === 1) {
                $nombresArtesanales[] = $categoria['nombre'];
            }
        }

        $activos = 0;
        foreach ($vendedores as $vendedor) {
            if (!$vendedor['activo']) {
                continue;
            }
            $activos++;
            if ($vendedor['provincia'] !== '' && !in_array($vendedor['provincia'], $provincias, true)) {
                $provincias[] = $vendedor['provincia'];
            }
            if (in_array($vendedor['categoria'], $nombresArtesanales, true)) {
                $artesanales++;
            }
        }

        return [
            'pendientes'      => (int) $fila['pendientes'],
            'activos'         => (int) $fila['activos'],
            'nuevosMes'       => (int) $fila['nuevos_mes'],
            'provincias'      => count($provincias),
            'usuarios'        => (int) $fila['usuarios'],
            'taxonomias'      => (int) $fila['categorias'],
            'selloArtesanal'  => $activos > 0 ? (int) round($artesanales * 100 / $activos) : 0,
            'provinciasLista' => array_slice($provincias, 0, 3),
            'provinciasExtra' => max(0, count($provincias) - 3),
        ];
    }

    /**
     * Reporte general de la plataforma.
     *
     * @param int $dias 7, 30 o 90
     * @return array ['indicadores', 'ventasCategoria', 'impacto', 'cobertura']
     */
    public function reporteAdmin($dias)
    {
        $hoy = date('Y-m-d');
        $desde = date('Y-m-d', strtotime('-' . ($dias - 1) . ' days'));
        $anteriorDesde = date('Y-m-d', strtotime('-' . ($dias * 2 - 1) . ' days'));
        $anteriorHasta = date('Y-m-d', strtotime('-' . $dias . ' days'));

        $actual = $this->reportes->resumenAdmin($desde, $hoy);
        $anterior = $this->reportes->resumenAdmin($anteriorDesde, $anteriorHasta);
        $inicio = $this->reportes->inicioAdmin();

        $ventasCategoria = [];
        $filas = $this->reportes->ventasPorCategoria($desde, $hoy);
        foreach ($filas as $fila) {
            $ventasCategoria[] = ['categoria' => $fila['categoria'], 'monto' => (float) $fila['monto']];
        }

        // Cobertura: provincias con más pedidos y crecimiento contra el período anterior
        $provinciasActual = $this->reportes->pedidosPorProvincia($desde, $hoy);
        $lideres = [];
        $crecimiento = [];
        for ($i = 0; $i < count($provinciasActual); $i++) {
            if ($i < 3) {
                $lideres[] = $provinciasActual[$i]['provincia'];
            } elseif ($i < 5) {
                $crecimiento[] = $provinciasActual[$i]['provincia'];
            }
        }

        $pedidos = (int) $actual['pedidos'];

        return [
            'indicadores' => [
                'ventas'          => (float) $actual['ventas'],
                'variacion'       => $this->variacion((float) $actual['ventas'], (float) $anterior['ventas']),
                'emprendimientos' => (int) $inicio['activos'],
                'pedidos'         => $pedidos,
            ],
            'ventasCategoria' => $ventasCategoria,
            'impacto'         => ['sinpe' => $pedidos > 0 ? (int) round((int) $actual['pedidos_sinpe'] * 100 / $pedidos) : 0],
            'cobertura'       => [
                'lideres'     => count($lideres) > 0 ? $this->unirLista($lideres) : 'Todavía no hay provincias',
                'crecimiento' => count($crecimiento) > 0 ? $this->unirLista($crecimiento) : 'el resto del país',
                'porcentaje'  => $this->variacion($pedidos, (int) $anterior['pedidos']),
            ],
        ];
    }

    /** Une una lista con comas y "y". Ej: ['A', 'B', 'C'] -> "A, B y C" */
    private function unirLista($lista)
    {
        if (count($lista) === 1) {
            return $lista[0];
        }
        $ultimo = array_pop($lista);
        return implode(', ', $lista) . ' y ' . $ultimo;
    }
}
