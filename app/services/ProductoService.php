<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * ProductoService
 * Lógica de productos y filtros del catálogo.
 * Catálogo público (RF-09, RF-11), vitrina del emprendimiento y gestión de productos del vendedor (RF-12).
 */

class ProductoService
{
    /** @var ProductoRepository */
    private $productos;
    /** @var CategoriaRepository */
    private $categorias;

    public function __construct()
    {
        $this->productos  = new ProductoRepository();
        $this->categorias = new CategoriaRepository();
    }

    /**
     * Lee los filtros que llegan por la URL (?q=&provincia=&categoria[]=...) y los deja limpios.
     *
     * @param array $get Normalmente $_GET
     * @return array
     */
    public function leerFiltros($get)
    {
        $categorias = [];
        if (isset($get['categoria'])) {
            // Puede llegar una sola (?categoria=3 desde el inicio) o varias (?categoria[]=1&categoria[]=3)
            $lista = is_array($get['categoria']) ? $get['categoria'] : [$get['categoria']];
            for ($i = 0; $i < count($lista); $i++) {
                if (ctype_digit((string) $lista[$i])) {
                    $categorias[] = (int) $lista[$i];
                }
            }
        }

        $ordenes = ['recomendados', 'recientes', 'precio_asc', 'precio_desc'];
        $orden = isset($get['orden']) && in_array($get['orden'], $ordenes, true) ? $get['orden'] : 'recomendados';

        $pagos = ['sinpe', 'tarjeta', 'efectivo'];
        $pago = isset($get['pago']) && in_array($get['pago'], $pagos, true) ? $get['pago'] : '';

        $provincia = isset($get['provincia']) && Validador::provincia($get['provincia']) ? $get['provincia'] : '';

        return [
            'texto'      => isset($get['q']) && is_string($get['q']) ? mb_substr(trim($get['q']), 0, 100) : '',
            'provincia'  => $provincia,
            'categorias' => implode(',', $categorias),
            'minimo'     => isset($get['min']) && is_numeric($get['min']) ? max(0, (float) $get['min']) : 0,
            'maximo'     => isset($get['max']) && is_numeric($get['max']) ? max(0, (float) $get['max']) : 0,
            'pago'       => $pago,
            'orden'      => $orden,
        ];
    }

    /**
     * Página del catálogo con los productos que cumplen los filtros.
     *
     * @param array $filtros Resultado de leerFiltros()
     * @param int $pagina
     * @return array ['productos', 'totalProductos', 'paginaActual', 'totalPaginas']
     */
    public function catalogo($filtros, $pagina)
    {
        $total = $this->productos->contarBusqueda($filtros);
        $totalPaginas = max(1, (int) ceil($total / PRODUCTOS_POR_PAGINA));
        $pagina = min(max(1, $pagina), $totalPaginas);

        $filas = $this->productos->buscar($filtros, PRODUCTOS_POR_PAGINA, ($pagina - 1) * PRODUCTOS_POR_PAGINA);

        $productos = [];
        foreach ($filas as $fila) {
            $productos[] = [
                'id'             => (int) $fila['id_producto'],
                'nombre'         => $fila['nombre'],
                'emprendimiento' => $fila['nombre_negocio'],
                'ubicacion'      => $fila['ubicacion'],
                'precio'         => (float) $fila['precio'],
                'etiqueta'       => $fila['categoria'],
                'sinpe'          => strpos($fila['metodos_pago'], 'sinpe') !== false,
                'imagen'         => imagenO($fila['imagen']),
            ];
        }

        return [
            'productos'      => $productos,
            'totalProductos' => $total,
            'paginaActual'   => $pagina,
            'totalPaginas'   => $totalPaginas,
        ];
    }

    /** @return array Categorías para los filtros del catálogo ['id', 'nombre', 'cantidad'] */
    public function categoriasFiltro()
    {
        $filas = $this->categorias->listar();
        $lista = [];
        foreach ($filas as $fila) {
            $lista[] = ['id' => (int) $fila['id_categoria'], 'nombre' => $fila['nombre'], 'cantidad' => (int) $fila['productos']];
        }
        return $lista;
    }

    /** @return array Tarjetas de categorías del inicio (landing) */
    public function categoriasInicio()
    {
        $colores = ['teal', 'lima', 'terracota', 'verde', 'azul', 'terracota', 'azul', 'lima'];
        $filas = $this->categorias->listar();
        $lista = [];

        for ($i = 0; $i < count($filas); $i++) {
            $marcas = (int) $filas[$i]['marcas'];
            $lista[] = [
                'id'          => (int) $filas[$i]['id_categoria'],
                'nombre'      => $filas[$i]['nombre'],
                'descripcion' => $filas[$i]['descripcion'],
                'cantidad'    => $marcas === 1 ? '1 emprendimiento' : $marcas . ' emprendimientos',
                'icono'       => $filas[$i]['icono'] !== null && $filas[$i]['icono'] !== '' ? $filas[$i]['icono'] : 'destellos',
                'color'       => $colores[$i % count($colores)],
            ];
        }
        return $lista;
    }

    /**
     * Texto corto para las tarjetas. Ej: los primeros 90 caracteres y "…"
     *
     * @param string $texto
     * @param int $largo
     * @return string
     */
    private function resumir($texto, $largo)
    {
        $texto = trim((string) $texto);
        if (mb_strlen($texto) <= $largo) {
            return $texto;
        }
        return rtrim(mb_substr($texto, 0, $largo)) . '…';
    }

    /**
     * Datos de la vitrina pública de un emprendimiento.
     *
     * @param array $emprendimiento Fila de sp_emprendimiento_obtener
     * @return array ['emprendimiento', 'productos', 'resenas', 'filtrosCatalogo']
     */
    public function vitrina($emprendimiento)
    {
        $idEmprendimiento = (int) $emprendimiento['id_emprendimiento'];
        $provincia = provinciaDe($emprendimiento['ubicacion']);
        $partesUbicacion = explode(',', $emprendimiento['ubicacion']);
        $aceptaSinpe = strpos($emprendimiento['metodos_pago'], 'sinpe') !== false;

        $filas = $this->productos->listarPorEmprendimiento($idEmprendimiento, true);

        // Fotos: el logo o, si no tiene, la del primer producto
        $imagen = $emprendimiento['logo'];
        if (($imagen === null || $imagen === '') && count($filas) > 0) {
            $imagen = $filas[0]['imagen'];
        }

        $sellos = ['Hecho en ' . ($provincia !== '' ? $provincia : 'Costa Rica'), 'Emprendimiento verificado por Ventica'];
        if ($aceptaSinpe) {
            $sellos[] = 'Pago directo por SINPE Móvil';
        }

        $datos = [
            'id'              => $idEmprendimiento,
            'nombre'          => $emprendimiento['nombre_negocio'],
            'provincia'       => $provincia,
            'ubicacion'       => $emprendimiento['ubicacion'],
            'altitud'         => $emprendimiento['categoria_principal'] !== null ? $emprendimiento['categoria_principal'] : 'Emprendimiento local',
            'descripcion'     => $this->resumir($emprendimiento['descripcion'], 180),
            'historia_titulo' => 'Hecho a mano en ' . trim($partesUbicacion[0]) . ' desde ' . date('Y', strtotime($emprendimiento['fecha_solicitud'])),
            'historia'        => (string) $emprendimiento['descripcion'],
            'sellos'          => $sellos,
            'calificacion'    => $emprendimiento['calificacion'] !== null ? (float) $emprendimiento['calificacion'] : 0,
            'total_resenas'   => (int) $emprendimiento['total_resenas'],
            'telefono'        => (string) $emprendimiento['telefono'],
            'imagen'          => imagenO($imagen),
        ];

        // Productos y los botones para filtrarlos por categoría
        $productos = [];
        $filtros = ['todos' => 'Todos los productos'];
        foreach ($filas as $fila) {
            $claveCategoria = 'cat-' . $fila['id_categoria'];
            $filtros[$claveCategoria] = $fila['categoria'];

            $stock = (int) $fila['cantidad_disponible'];
            if ($stock === 0) {
                $estado = 'Agotado';
            } elseif ($stock <= 5) {
                $estado = 'Últimas ' . $stock;
            } else {
                $estado = 'En stock';
            }

            $productos[] = [
                'id'        => (int) $fila['id_producto'],
                'nombre'    => $fila['nombre'],
                'nota'      => $this->resumir($fila['descripcion'], 90),
                'categoria' => $claveCategoria,
                'etiqueta'  => $fila['categoria'],
                'detalle'   => 'Hecho a mano',
                'estado'    => $estado,
                'precio'    => (float) $fila['precio'],
                'sinpe'     => $aceptaSinpe,
                'imagen'    => imagenO($fila['imagen']),
            ];
        }

        // Reseñas de compradores
        $resenas = [];
        $filasResenas = $this->productos->listarResenas($idEmprendimiento, 6);
        foreach ($filasResenas as $fila) {
            $resenas[] = [
                'autor'     => $fila['autor'],
                'lugar'     => $fila['provincia'] !== null ? $fila['provincia'] : 'Costa Rica',
                'hace'      => haceCuanto($fila['fecha']),
                'estrellas' => (int) $fila['calificacion'],
                'texto'     => $fila['comentario'] !== null ? $fila['comentario'] : 'Compra recomendada.',
            ];
        }

        return [
            'emprendimiento'  => $datos,
            'productos'       => $productos,
            'resenas'         => $resenas,
            'filtrosCatalogo' => $filtros,
        ];
    }

    /**
     * Productos del vendedor para "Mis Productos".
     *
     * @param int $idEmprendimiento
     * @return array
     */
    public function productosDelVendedor($idEmprendimiento)
    {
        $filas = $this->productos->listarPorEmprendimiento($idEmprendimiento, false);
        $lista = [];
        foreach ($filas as $fila) {
            $lista[] = [
                'id'          => (int) $fila['id_producto'],
                'nombre'      => $fila['nombre'],
                'descripcion' => $this->resumir($fila['descripcion'], 60),
                'categoria'   => $fila['categoria'],
                'precio'      => (float) $fila['precio'],
                'stock'       => (int) $fila['cantidad_disponible'],
                'activo'      => $fila['estado'] !== 'inactivo',
                'imagen'      => imagenO($fila['imagen']),
            ];
        }
        return $lista;
    }

    /** @return array Categorías para el select del formulario ['id', 'nombre'] */
    public function categoriasSelect()
    {
        $filas = $this->categorias->listar();
        $lista = [];
        foreach ($filas as $fila) {
            $lista[] = ['id' => (int) $fila['id_categoria'], 'nombre' => $fila['nombre']];
        }
        return $lista;
    }

    /**
     * Producto para el formulario de edición (solo si es del vendedor).
     *
     * @param int $idEmprendimiento
     * @param int $idProducto
     * @return array|null
     */
    public function productoParaEditar($idEmprendimiento, $idProducto)
    {
        $fila = $this->productos->obtener($idProducto);
        if ($fila === null || (int) $fila['id_emprendimiento'] !== $idEmprendimiento) {
            return null;
        }
        return [
            'id'           => (int) $fila['id_producto'],
            'nombre'       => $fila['nombre'],
            'id_categoria' => (int) $fila['id_categoria'],
            'precio'       => (float) $fila['precio'],
            'stock'        => (int) $fila['cantidad_disponible'],
            'descripcion'  => $fila['descripcion'],
            'imagen'       => imagenO($fila['imagen']),
        ];
    }

    /**
     * Crea o actualiza un producto del vendedor con su foto (RF-12).
     *
     * @param int $idEmprendimiento
     * @param array $datos ['id_producto', 'nombre', 'id_categoria', 'precio', 'stock', 'descripcion']
     * @return array ['errores' => [...], 'id' => id del producto]
     */
    public function guardar($idEmprendimiento, $datos)
    {
        $errores = [];
        $idProducto = (int) $datos['id_producto'];
        $esNuevo = $idProducto === 0;

        if ($esNuevo === false && $this->productoParaEditar($idEmprendimiento, $idProducto) === null) {
            return ['errores' => ['general' => 'Ese producto no existe o no es de tu emprendimiento.'], 'id' => 0];
        }

        if (!Validador::texto($datos['nombre'], 3, 120)) {
            $errores['nombre'] = 'El nombre debe tener al menos 3 letras.';
        }
        if (!ctype_digit($datos['id_categoria']) || $this->categorias->obtener((int) $datos['id_categoria']) === null) {
            $errores['categoria'] = 'Escogé una categoría.';
        }
        if (!Validador::numero($datos['precio'], 100)) {
            $errores['precio'] = 'El precio mínimo es ₡100.';
        }
        if (!Validador::entero($datos['stock'], 0)) {
            $errores['stock'] = 'El inventario debe ser un número entero.';
        }
        if (!Validador::texto($datos['descripcion'], 20, 280)) {
            $errores['descripcion'] = 'La descripción debe tener entre 20 y 280 caracteres.';
        }

        $archivos = new ArchivoService();
        if ($esNuevo && !$archivos->seSubio('imagen')) {
            $errores['imagen'] = 'Subí una foto del producto.';
        }

        if (count($errores) > 0) {
            return ['errores' => $errores, 'id' => $idProducto];
        }

        $producto = new Producto();
        $producto->setIdProducto($idProducto);
        $producto->setIdEmprendimiento($idEmprendimiento);
        $producto->setIdCategoria((int) $datos['id_categoria']);
        $producto->setNombre($datos['nombre']);
        $producto->setDescripcion($datos['descripcion']);
        $producto->setPrecio((float) $datos['precio']);
        $producto->setCantidadDisponible((int) $datos['stock']);

        // Primero se valida la foto (si viene) para no guardar un producto sin imagen
        $rutaImagen = '';
        if ($archivos->seSubio('imagen')) {
            $resultado = $archivos->guardarImagen('imagen', 'productos-nuevos', 'p' . $idEmprendimiento);
            if (!$resultado['ok']) {
                return ['errores' => ['imagen' => $resultado['error']], 'id' => $idProducto];
            }
            $rutaImagen = $resultado['ruta'];
        }

        if ($esNuevo) {
            $idProducto = $this->productos->crear($producto);
        } else {
            $this->productos->actualizar($producto);
        }

        if ($rutaImagen !== '') {
            $this->productos->guardarImagen($idProducto, $rutaImagen);
        }

        return ['errores' => [], 'id' => $idProducto];
    }

    /**
     * Activa o pausa un producto (interruptor de Mis Productos).
     *
     * @param int $idEmprendimiento
     * @param int $idProducto
     * @param bool $activo
     */
    public function cambiarDisponibilidad($idEmprendimiento, $idProducto, $activo)
    {
        $fila = $this->productos->obtener($idProducto);
        if ($fila === null || (int) $fila['id_emprendimiento'] !== $idEmprendimiento) {
            return;
        }

        if ($activo) {
            $estado = (int) $fila['cantidad_disponible'] > 0 ? 'activo' : 'agotado';
        } else {
            $estado = 'inactivo';
        }
        $this->productos->cambiarEstado($idProducto, $idEmprendimiento, $estado);
    }

    /**
     * @param int $idProducto
     * @return array|null Producto con su emprendimiento
     */
    public function obtener($idProducto)
    {
        return $this->productos->obtener($idProducto);
    }
}
