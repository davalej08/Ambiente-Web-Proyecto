<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * VendedorService
 * Lógica de los emprendimientos: perfil y pedidos del vendedor (RF-12, RF-14),
 * solicitudes y emprendimientos activos del administrador (RF-03) e Historias de Artesanos.
 */

class VendedorService
{
    /** @var VendedorRepository */
    private $vendedores;
    /** @var PedidoRepository */
    private $pedidos;

    /** Provincias de la Gran Área Metropolitana (entrega GAM Express) */
    private $provinciasGam = ['San José', 'Heredia', 'Alajuela', 'Cartago'];

    public function __construct()
    {
        $this->vendedores = new VendedorRepository();
        $this->pedidos    = new PedidoRepository();
    }

    /**
     * @param int $idEmprendimiento
     * @return array|null Fila completa del emprendimiento
     */
    public function obtener($idEmprendimiento)
    {
        return $this->vendedores->obtener($idEmprendimiento);
    }

    /**
     * Nombre y ubicación para la barra lateral del portal.
     *
     * @param int $idEmprendimiento
     * @return array ['nombre', 'ubicacion', 'provincia']
     */
    public function tienda($idEmprendimiento)
    {
        $fila = $this->vendedores->obtener($idEmprendimiento);
        $ubicacion = $fila['ubicacion'] !== null && $fila['ubicacion'] !== '' ? $fila['ubicacion'] : 'Costa Rica';
        return [
            'id'        => $idEmprendimiento,
            'nombre'    => $fila['nombre_negocio'],
            'ubicacion' => $ubicacion,
            'provincia' => $ubicacion,
        ];
    }

    // ---------------------------------------------------------------
    // Portal del vendedor
    // ---------------------------------------------------------------

    /**
     * Datos de "Mi Perfil" del vendedor.
     *
     * @param int $idEmprendimiento
     * @return array
     */
    public function perfil($idEmprendimiento)
    {
        $fila = $this->vendedores->obtener($idEmprendimiento);
        $imagen = $fila['logo'];
        if ($imagen === null || $imagen === '') {
            $productos = (new ProductoRepository())->listarPorEmprendimiento($idEmprendimiento, false);
            $imagen = count($productos) > 0 ? $productos[0]['imagen'] : '';
        }

        return [
            'nombre'      => $fila['nombre_negocio'],
            'categoria'   => $fila['categoria_principal'] !== null ? $fila['categoria_principal'] : 'Emprendimiento local',
            'verificado'  => $fila['estado_verificacion'] === 'aprobado',
            'descripcion' => (string) $fila['descripcion'],
            'ubicacion'    => (string) $fila['ubicacion'],
            'id_provincia' => $fila['id_provincia'],
            'id_canton'    => $fila['id_canton'],
            'id_distrito'  => $fila['id_distrito'],
            'otras_senas'  => (string) $fila['otras_senas'],
            'telefono'     => (string) $fila['telefono'],
            'pagos'        => explode(',', $fila['metodos_pago']),
            'imagen'      => imagenO($imagen),
        ];
    }

    /**
     * Guarda la vitrina del vendedor (nombre, historia, ubicación, teléfono SINPE, pagos y logo).
     *
     * @param int $idEmprendimiento
     * @param int $idUsuario
     * @param array $datos ['nombre', 'descripcion', 'distrito', 'otras_senas', 'telefono', 'pagos' => [...]]
     * @return array Errores ['campo' => 'mensaje']
     */
    public function guardarPerfil($idEmprendimiento, $idUsuario, $datos)
    {
        $errores = [];
        $permitidos = ['sinpe', 'tarjeta', 'efectivo'];

        if (!Validador::texto($datos['nombre'], 3, 120)) {
            $errores['nombre'] = 'El nombre debe tener al menos 3 letras.';
        }
        if (!Validador::texto($datos['descripcion'], 20, 400)) {
            $errores['descripcion'] = 'Contá tu historia en 20 a 400 caracteres.';
        }
        if ((new UbicacionService())->distrito($datos['distrito']) === null) {
            $errores['distrito'] = 'Elegí la provincia, el cantón y el distrito.';
        }
        if (!Validador::texto($datos['otras_senas'], 0, 255)) {
            $errores['otras_senas'] = 'Las señas son muy largas.';
        }
        if (!Validador::telefono($datos['telefono'])) {
            $errores['telefono'] = 'El teléfono debe tener 8 dígitos.';
        }

        $pagos = [];
        for ($i = 0; $i < count($datos['pagos']); $i++) {
            if (in_array($datos['pagos'][$i], $permitidos, true)) {
                $pagos[] = $datos['pagos'][$i];
            }
        }
        if (count($pagos) === 0) {
            $errores['pagos'] = 'Escogé al menos una forma de pago.';
        }

        $archivos = new ArchivoService();
        $rutaLogo = '';
        if (count($errores) === 0 && $archivos->seSubio('logo')) {
            $resultado = $archivos->guardarImagen('logo', 'logos', 'logo' . $idEmprendimiento);
            if ($resultado['ok']) {
                $rutaLogo = $resultado['ruta'];
            } else {
                $errores['logo'] = $resultado['error'];
            }
        }

        if (count($errores) > 0) {
            return $errores;
        }

        $vendedor = new Vendedor();
        $vendedor->setIdEmprendimiento($idEmprendimiento);
        $vendedor->setNombreNegocio($datos['nombre']);
        $vendedor->setDescripcion($datos['descripcion']);
        $vendedor->setIdDistrito((int) $datos['distrito']);
        $vendedor->setOtrasSenas($datos['otras_senas']);
        $vendedor->setLogo($rutaLogo);
        $vendedor->setMetodosPago(implode(',', $pagos));
        $this->vendedores->actualizar($vendedor);

        // El teléfono es el número SINPE y se guarda en el usuario
        (new UsuarioService())->actualizarTelefono($idUsuario, $datos['telefono']);

        return [];
    }

    /**
     * Estado que muestra la pantalla de pedidos del vendedor:
     *   aprobacion = pagó por SINPE y falta confirmar el pago
     *   pendiente  = falta confirmar (tarjeta o efectivo)
     *   confirmado = listo para despachar
     *   entregado  = ya se entregó
     *
     * @param array $fila
     * @return string '' si todas las líneas fueron rechazadas
     */
    private function estadoParaVendedor($fila)
    {
        if ((int) $fila['pendientes'] > 0) {
            return $fila['metodo_pago'] === 'sinpe' ? 'aprobacion' : 'pendiente';
        }
        if ((int) $fila['confirmados'] > 0) {
            return 'confirmado';
        }
        if ((int) $fila['entregados'] > 0) {
            return 'entregado';
        }
        return '';
    }

    /**
     * Pedidos del emprendimiento con la forma que usa vendedor/pedidos.php.
     *
     * @param int $idEmprendimiento
     * @return array
     */
    public function pedidos($idEmprendimiento)
    {
        $filas = $this->pedidos->listarPorEmprendimiento($idEmprendimiento, 100);
        $hoy = date('Y-m-d');
        $lista = [];

        foreach ($filas as $fila) {
            $estado = $this->estadoParaVendedor($fila);
            if ($estado === '') {
                continue;
            }

            $esHoy = date('Y-m-d', strtotime($fila['fecha_pedido'])) === $hoy;
            $momento = $esHoy ? 'Hoy a las ' . horaCorta($fila['fecha_pedido']) : fechaCorta($fila['fecha_pedido']) . ' · ' . horaCorta($fila['fecha_pedido']);

            $metodo = $fila['metodo_pago'];
            if ($metodo === 'sinpe') {
                $textoPago = 'SINPE Móvil · ' . substr((string) $fila['telefono_cliente'], 0, 4) . '-****';
            } elseif ($metodo === 'tarjeta') {
                $textoPago = 'Tarjeta débito / crédito';
            } else {
                $textoPago = 'Efectivo contra entrega';
            }

            $unidades = (int) $fila['unidades'];
            $lista[] = [
                'id'         => (int) $fila['id_pedido'],
                'cliente'    => $fila['cliente'],
                'ubicacion'  => $fila['canton'] . ', ' . $fila['provincia'],
                'momento'    => $momento,
                'producto'   => $fila['productos'],
                'cantidad'   => $unidades . ($unidades === 1 ? ' unidad' : ' unidades'),
                'detalle'    => (string) $fila['direccion_exacta'],
                'imagen'     => imagenO($fila['imagen']),
                'total'      => (float) $fila['total'],
                'pago'       => $metodo,
                'pago_texto' => $textoPago,
                'entrega'    => in_array($fila['provincia'], $this->provinciasGam, true) ? 'gam' : 'correos',
                'hoy'        => $esHoy,
                'estado'     => $estado,
                'nota'       => $estado === 'entregado' ? 'Entregado y notificado al cliente' : '',
                'recibido'   => ($esHoy ? 'Hoy' : haceCuanto($fila['fecha_pedido'])) . ', ' . horaCorta($fila['fecha_pedido']),
            ];
        }
        return $lista;
    }

    /**
     * Pedidos que esperan confirmación (para el inicio del portal).
     *
     * @param int $idEmprendimiento
     * @param int $cantidad
     * @return array
     */
    public function pedidosPorConfirmar($idEmprendimiento, $cantidad)
    {
        $todos = $this->pedidos($idEmprendimiento);
        $lista = [];
        for ($i = 0; $i < count($todos) && count($lista) < $cantidad; $i++) {
            if ($todos[$i]['estado'] === 'aprobacion' || $todos[$i]['estado'] === 'pendiente') {
                $todos[$i]['detalle'] = $todos[$i]['producto'];
                $lista[] = $todos[$i];
            }
        }
        return $lista;
    }

    /**
     * Confirma o marca como entregado un pedido (solo las líneas del vendedor).
     *
     * @param int $idEmprendimiento
     * @param int $idPedido
     * @param string $estado confirmado | entregado
     * @return bool true si cambió algo
     */
    public function cambiarEstadoPedido($idEmprendimiento, $idPedido, $estado)
    {
        return $this->pedidos->cambiarEstado($idPedido, $idEmprendimiento, $estado) > 0;
    }

    /**
     * Comprobante imprimible de un pedido (solo con las líneas del vendedor).
     *
     * @param int $idEmprendimiento
     * @param int $idPedido
     * @return array|null
     */
    public function comprobante($idEmprendimiento, $idPedido)
    {
        $encabezado = $this->pedidos->obtener($idPedido, 0);
        if ($encabezado === null) {
            return null;
        }

        $lineas = [];
        $total = 0;
        $todas = $this->pedidos->listarDetalle($idPedido);
        foreach ($todas as $linea) {
            if ((int) $linea['id_emprendimiento'] === $idEmprendimiento) {
                $lineas[] = $linea;
                $total = $total + $linea['subtotal'];
            }
        }
        if (count($lineas) === 0) {
            return null;
        }

        return ['encabezado' => $encabezado, 'lineas' => $lineas, 'total' => $total];
    }

    // ---------------------------------------------------------------
    // Administrador
    // ---------------------------------------------------------------

    /**
     * Revisa qué tan completo está el expediente de una solicitud.
     *
     * @param array $fila
     * @return array ['puntaje' => 0..100, 'faltantes' => [textos]]
     */
    private function revisarExpediente($fila)
    {
        $faltantes = [];
        if ($fila['logo'] === null || $fila['logo'] === '') {
            $faltantes[] = 'logo';
        }
        if (mb_strlen((string) $fila['descripcion']) < 60) {
            $faltantes[] = 'historia del emprendimiento';
        }
        if ((int) $fila['total_productos'] === 0) {
            $faltantes[] = 'productos de muestra';
        }
        if ($fila['telefono'] === null || $fila['telefono'] === '') {
            $faltantes[] = 'teléfono SINPE';
        }
        return ['puntaje' => 100 - count($faltantes) * 15, 'faltantes' => $faltantes];
    }

    /** @return string Ej: #CR-2026-009 */
    private function expediente($fila)
    {
        return '#CR-' . date('Y', strtotime($fila['fecha_solicitud'])) . '-' . str_pad($fila['id_emprendimiento'], 3, '0', STR_PAD_LEFT);
    }

    /** @return array Solicitudes pendientes con la forma de usuario_rol/solicitudes.php */
    public function solicitudesPendientes()
    {
        $filas = $this->vendedores->listarPorEstado('pendiente');
        $productos = new ProductoRepository();
        $lista = [];

        foreach ($filas as $fila) {
            $revision = $this->revisarExpediente($fila);
            $conObservacion = $fila['observacion_admin'] !== null && $fila['observacion_admin'] !== '';
            $completo = count($revision['faltantes']) === 0;

            $imagen = $fila['logo'];
            if ($imagen === null || $imagen === '') {
                $muestra = $productos->listarPorEmprendimiento((int) $fila['id_emprendimiento'], false);
                $imagen = count($muestra) > 0 ? $muestra[0]['imagen'] : '';
            }

            if ($completo) {
                $nota = 'Expediente completo';
                $semaforo = 'verde';
            } elseif (count($revision['faltantes']) === 1) {
                $nota = 'Falta: ' . $revision['faltantes'][0];
                $semaforo = 'teal';
            } else {
                $nota = 'Faltan ' . count($revision['faltantes']) . ' requisitos';
                $semaforo = 'rojo';
            }

            $lista[] = [
                'id'         => (int) $fila['id_emprendimiento'],
                'tienda'     => $fila['nombre_negocio'],
                'insignia'   => $conObservacion ? 'Con observaciones' : 'Nueva solicitud',
                'tipo'       => $conObservacion ? 'observacion' : 'nueva',
                'expediente' => $this->expediente($fila),
                'categoria'  => $fila['categoria_principal'] !== null ? $fila['categoria_principal'] : 'Sin productos todavía',
                'icono'      => 'tienda',
                'ubicacion'  => (string) $fila['ubicacion'],
                'provincia'  => provinciaDe((string) $fila['ubicacion']),
                'fecha'      => fechaCorta($fila['fecha_solicitud']),
                'estado'     => $completo ? 'ok' : 'alerta',
                'nota'       => $nota,
                'semaforo'   => $semaforo,
                'sello'      => strpos($fila['metodos_pago'], 'sinpe') !== false,
                'imagen'     => imagenO($imagen),
            ];
        }
        return $lista;
    }

    /**
     * Expediente completo de una solicitud con la forma de usuario_rol/solicitud_detalle.php.
     *
     * @param int $idEmprendimiento
     * @return array|null
     */
    public function detalleSolicitud($idEmprendimiento)
    {
        $fila = $this->vendedores->obtener($idEmprendimiento);
        if ($fila === null) {
            return null;
        }

        $revision = $this->revisarExpediente($fila);
        $productos = (new ProductoRepository())->listarPorEmprendimiento($idEmprendimiento, false);

        $muestra = [];
        for ($i = 0; $i < count($productos) && $i < 3; $i++) {
            $muestra[] = [
                'nombre'      => $productos[$i]['nombre'],
                'descripcion' => (string) $productos[$i]['descripcion'],
                'precio'      => (float) $productos[$i]['precio'],
                'sku'         => 'VT-' . str_pad($productos[$i]['id_producto'], 4, '0', STR_PAD_LEFT),
                'medida'      => (int) $productos[$i]['cantidad_disponible'] . ' en stock',
                'rubro'       => $productos[$i]['categoria'],
                'imagen'      => imagenO($productos[$i]['imagen']),
            ];
        }

        $imagen = $fila['logo'];
        if (($imagen === null || $imagen === '') && count($productos) > 0) {
            $imagen = $productos[0]['imagen'];
        }

        $documentos = [];
        if ($fila['logo'] !== null && $fila['logo'] !== '') {
            $documentos[] = basename($fila['logo']);
        }

        $estadosTexto = ['pendiente' => 'Pendiente', 'aprobado' => 'Aprobada', 'rechazado' => 'Rechazada'];

        return [
            'id'            => (int) $fila['id_emprendimiento'],
            'estado'        => $fila['estado_verificacion'],
            'estadoTexto'   => $estadosTexto[$fila['estado_verificacion']],
            'observacion'   => (string) $fila['observacion_admin'],
            'tienda'        => $fila['nombre_negocio'],
            'rubro'         => $fila['categoria_principal'] !== null ? $fila['categoria_principal'] : 'Emprendimiento local',
            'categoria'     => $fila['categoria_principal'] !== null ? $fila['categoria_principal'] : 'Sin productos todavía',
            'zona'          => (string) $fila['ubicacion'],
            'expediente'    => $this->expediente($fila),
            'recibido'      => fechaCorta($fila['fecha_solicitud']),
            'puntaje'       => $revision['puntaje'],
            'imagen'        => imagenO($imagen),
            'cedula'        => 'No aportada',
            'cedulaEstado'  => 'Se valida con el emprendedor',
            'modalidad'     => 'Emprendimiento local',
            'modalidadNota' => (int) $fila['total_productos'] . ' productos registrados',
            'memoria'       => (string) $fila['descripcion'],
            'documentos'    => $documentos,
            'rep'           => [
                'nombre'    => $fila['dueno'],
                'iniciales' => iniciales($fila['dueno']),
                'cedula'    => 'No aportada',
                'rol'       => 'Representante del emprendimiento',
                'telefono'  => '+506 ' . $fila['telefono'],
                'sinpe'     => strpos($fila['metodos_pago'], 'sinpe') !== false,
                'correo'    => $fila['correo'],
                'instagram' => '—',
            ],
            'checklist'     => [
                ['Correo de contacto', $fila['correo']],
                ['Formas de pago', str_replace(',', ', ', $fila['metodos_pago'])],
                ['Expediente', count($revision['faltantes']) === 0 ? 'Completo' : 'Falta: ' . implode(', ', $revision['faltantes'])],
            ],
            'lugar'         => $fila['nombre_negocio'],
            'direccion'     => (string) $fila['ubicacion'],
            'coordenadas'   => 'Provincia de ' . (provinciaDe((string) $fila['ubicacion']) !== '' ? provinciaDe((string) $fila['ubicacion']) : 'Costa Rica'),
            'envio'         => 'Envía vía Correos de Costa Rica',
            'compromiso'    => 'El solicitante se compromete a publicar productos hechos en Costa Rica, con precios y existencias reales, y a responder sus pedidos en un máximo de 48 horas.',
            'productos'     => $muestra,
            'correoDueno'   => $fila['correo'],
            'nombreDueno'   => $fila['dueno'],
        ];
    }

    /**
     * Aprueba o rechaza una solicitud y le avisa al emprendedor por correo.
     *
     * @param int $idEmprendimiento
     * @param string $decision aprobar | rechazar
     * @param string $observacion Obligatoria al rechazar
     * @return array ['error' => '', 'mensaje' => '']
     */
    public function resolverSolicitud($idEmprendimiento, $decision, $observacion)
    {
        $fila = $this->vendedores->obtener($idEmprendimiento);
        if ($fila === null || $fila['estado_verificacion'] !== 'pendiente') {
            return ['error' => 'Esa solicitud ya fue resuelta o no existe.', 'mensaje' => ''];
        }
        if ($decision !== 'aprobar' && $decision !== 'rechazar') {
            return ['error' => 'Escogé aprobar o rechazar.', 'mensaje' => ''];
        }
        if ($decision === 'rechazar' && mb_strlen($observacion) < 10) {
            return ['error' => 'Para rechazar escribí el motivo (mínimo 10 caracteres).', 'mensaje' => ''];
        }
        if (mb_strlen($observacion) > 500) {
            return ['error' => 'La observación es muy larga (máximo 500 caracteres).', 'mensaje' => ''];
        }

        $aprobada = $decision === 'aprobar';
        $this->vendedores->resolver($idEmprendimiento, $aprobada ? 'aprobado' : 'rechazado', $observacion);
        (new CorreoService())->enviarRespuestaSolicitud($fila['correo'], $fila['dueno'], $fila['nombre_negocio'], $aprobada, $observacion);

        if ($aprobada) {
            return ['error' => '', 'mensaje' => $fila['nombre_negocio'] . ' fue aprobado. Le avisamos al emprendedor por correo.'];
        }
        return ['error' => '', 'mensaje' => 'La solicitud de ' . $fila['nombre_negocio'] . ' fue rechazada. El motivo se le envió por correo.'];
    }

    /**
     * Emprendimientos aprobados (activos y suspendidos) con sus ventas de los últimos 30 días.
     *
     * @return array Con la forma de usuario_rol/vendedores.php
     */
    public function emprendimientosAprobados()
    {
        $filas = $this->vendedores->listarPorEstado('aprobado');
        $reportes = new ReporteRepository();
        $productos = new ProductoRepository();
        $hoy = date('Y-m-d');
        $hace30 = date('Y-m-d', strtotime('-29 days'));
        $hace60 = date('Y-m-d', strtotime('-59 days'));
        $hace31 = date('Y-m-d', strtotime('-30 days'));
        $lista = [];

        foreach ($filas as $fila) {
            $id = (int) $fila['id_emprendimiento'];
            $actual = (float) $reportes->resumenVendedor($id, $hace30, $hoy)['ventas'];
            $anterior = (float) $reportes->resumenVendedor($id, $hace60, $hace31)['ventas'];
            $variacion = $anterior > 0 ? (int) round(($actual - $anterior) * 100 / $anterior) : 0;

            $imagen = $fila['logo'];
            if ($imagen === null || $imagen === '') {
                $muestra = $productos->listarPorEmprendimiento($id, false);
                $imagen = count($muestra) > 0 ? $muestra[0]['imagen'] : '';
            }

            $lista[] = [
                'id'          => $id,
                'id_usuario'  => (int) $fila['id_usuario'],
                'tienda'      => $fila['nombre_negocio'],
                'ubicacion'   => $fila['ubicacion'] !== null && $fila['ubicacion'] !== '' ? $fila['ubicacion'] : 'Costa Rica',
                'provincia'   => provinciaDe((string) $fila['ubicacion']),
                'descripcion' => 'A cargo de ' . $fila['dueno'] . ' • ' . $fila['correo'],
                'categoria'   => $fila['categoria_principal'] !== null ? $fila['categoria_principal'] : 'Sin productos',
                'productos'   => (int) $fila['total_productos'],
                'ventas'      => $actual,
                'variacion'   => $variacion,
                'activo'      => $fila['estado_usuario'] === 'activo',
                'imagen'      => imagenO($imagen),
            ];
        }
        return $lista;
    }

    /**
     * Suspende o reactiva un emprendimiento (se suspende la cuenta del dueño).
     *
     * @param int $idEmprendimiento
     * @return string Mensaje para el administrador
     */
    public function alternarEstado($idEmprendimiento)
    {
        $fila = $this->vendedores->obtener($idEmprendimiento);
        if ($fila === null) {
            return 'Ese emprendimiento no existe.';
        }

        $usuarios = new UsuarioRepository();
        if ($fila['estado_usuario'] === 'activo') {
            $usuarios->cambiarEstado((int) $fila['id_usuario'], 'bloqueado');
            return $fila['nombre_negocio'] . ' fue suspendido. Su vitrina ya no aparece en el catálogo y se conserva su historial.';
        }

        $usuarios->cambiarEstado((int) $fila['id_usuario'], 'activo');
        return $fila['nombre_negocio'] . ' fue reactivado y su vitrina vuelve a estar visible.';
    }

    /** @return array Tarjetas de "Historias de Artesanos" */
    public function historias()
    {
        $filas = $this->vendedores->listarHistorias();
        $lista = [];
        foreach ($filas as $fila) {
            $partes = explode(',', (string) $fila['ubicacion']);
            $lista[] = [
                'id'           => (int) $fila['id_emprendimiento'],
                'nombre'       => $fila['nombre_negocio'],
                'dueno'        => $fila['dueno'],
                'historia'     => (string) $fila['descripcion'],
                'ubicacion'    => (string) $fila['ubicacion'],
                'canton'       => trim($partes[0]),
                'provincia'    => provinciaDe((string) $fila['ubicacion']),
                'categoria'    => $fila['categoria_principal'] !== null ? $fila['categoria_principal'] : 'Emprendimiento local',
                'productos'    => (int) $fila['total_productos'],
                'calificacion' => $fila['calificacion'] !== null ? (float) $fila['calificacion'] : 0,
                'desde'        => date('Y', strtotime($fila['fecha_solicitud'])),
                'imagen'       => imagenO($fila['logo'] !== null && $fila['logo'] !== '' ? $fila['logo'] : $fila['imagen']),
            ];
        }
        return $lista;
    }
}
