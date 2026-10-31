<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * UsuarioService
 * Perfil del cliente (datos, pedidos y favoritos) y gestión de usuarios del administrador (RF-05).
 */

class UsuarioService
{
    /** @var UsuarioRepository */
    private $usuarios;
    /** @var PedidoRepository */
    private $pedidos;
    /** @var ClienteRepository */
    private $clientes;

    public function __construct()
    {
        $this->usuarios = new UsuarioRepository();
        $this->pedidos  = new PedidoRepository();
        $this->clientes = new ClienteRepository();
    }

    /**
     * @param int $idUsuario
     * @return array|null Fila completa del usuario
     */
    public function obtener($idUsuario)
    {
        return $this->usuarios->obtenerPorId($idUsuario);
    }

    /**
     * Datos personales para "Mi perfil".
     *
     * @param int $idUsuario
     * @return array
     */
    public function datosPerfil($idUsuario)
    {
        $fila = $this->usuarios->obtenerPorId($idUsuario);
        $marca = strtotime($fila['fecha_registro']);
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

        return [
            'nombre_completo'  => $fila['nombre'],
            'cedula'           => $fila['cedula'],
            'nombre'           => $fila['nombre_pila'],
            'primer_apellido'  => $fila['primer_apellido'],
            'segundo_apellido' => (string) $fila['segundo_apellido'],
            'correo'           => $fila['correo'],
            'telefono'         => (string) $fila['telefono'],
            'provincia'        => (string) $fila['provincia'],
            'canton'           => (string) $fila['canton'],
            'id_provincia'     => $fila['id_provincia'],
            'id_canton'        => $fila['id_canton'],
            'id_distrito'      => $fila['id_distrito'],
            'direccion'        => (string) $fila['direccion'],
            'fecha_registro' => $meses[(int) date('n', $marca) - 1] . ' ' . date('Y', $marca),
        ];
    }

    /**
     * Pedidos del cliente con un solo estado por pedido.
     *
     * @param int $idUsuario
     * @return array
     */
    public function pedidosDelCliente($idUsuario)
    {
        $pago = new PagoService();
        $filas = $this->pedidos->listarPorCliente($idUsuario);
        $lista = [];

        foreach ($filas as $fila) {
            $lineas = (int) $fila['lineas'];

            // Estado general: el más atrasado de las líneas del pedido
            if ((int) $fila['rechazados'] === $lineas) {
                $estado = 'rechazado';
            } elseif ((int) $fila['pendientes'] > 0) {
                $estado = 'pendiente';
            } elseif ((int) $fila['entregados'] + (int) $fila['rechazados'] === $lineas) {
                $estado = 'entregado';
            } elseif ((int) $fila['enviados'] > 0) {
                $estado = 'enviado';
            } else {
                $estado = 'confirmado';
            }

            $unidades = (int) $fila['unidades'];
            $lista[] = [
                'id'             => (int) $fila['id_pedido'],
                'fecha'          => fechaCorta($fila['fecha_pedido']),
                'emprendimiento' => $fila['emprendimientos'],
                'productos'      => $lineas . ($lineas === 1 ? ' producto' : ' productos') . ' · ' . $unidades . ($unidades === 1 ? ' unidad' : ' unidades'),
                'total'          => (float) $fila['total'],
                'metodo'         => $pago->nombreMetodo($fila['metodo_pago']),
                'estado'         => $estado,
                'imagen'         => imagenO($fila['imagen']),
            ];
        }
        return $lista;
    }

    /**
     * @param int $idUsuario
     * @return array Productos favoritos
     */
    public function favoritos($idUsuario)
    {
        $filas = $this->clientes->listarFavoritos($idUsuario);
        $lista = [];
        foreach ($filas as $fila) {
            $lista[] = [
                'id'             => (int) $fila['id_producto'],
                'nombre'         => $fila['nombre'],
                'emprendimiento' => $fila['nombre_negocio'],
                'ubicacion'      => $fila['ubicacion'],
                'precio'         => (float) $fila['precio'],
                'imagen'         => imagenO($fila['imagen']),
            ];
        }
        return $lista;
    }

    /**
     * Ids de los favoritos (para pintar los corazones del catálogo).
     *
     * @param int $idUsuario
     * @return array
     */
    public function idsFavoritos($idUsuario)
    {
        $ids = [];
        if ($idUsuario === 0) {
            return $ids;
        }
        $filas = $this->clientes->listarFavoritos($idUsuario);
        foreach ($filas as $fila) {
            $ids[] = (int) $fila['id_producto'];
        }
        return $ids;
    }

    /**
     * Agrega o quita un favorito.
     *
     * @param int $idUsuario
     * @param int $idProducto
     * @return bool true si quedó como favorito
     */
    public function alternarFavorito($idUsuario, $idProducto)
    {
        if (in_array($idProducto, $this->idsFavoritos($idUsuario), true)) {
            $this->clientes->quitarFavorito($idUsuario, $idProducto);
            return false;
        }
        $this->clientes->agregarFavorito($idUsuario, $idProducto);
        return true;
    }

    /**
     * @param int $idUsuario
     * @param int $idProducto
     */
    public function quitarFavorito($idUsuario, $idProducto)
    {
        $this->clientes->quitarFavorito($idUsuario, $idProducto);
    }

    /**
     * Guarda los cambios de "Mi perfil".
     *
     * @param int $idUsuario
     * @param array $datos ['nombre', 'primer_apellido', 'segundo_apellido', 'correo', 'telefono', 'distrito', 'direccion']
     *                      distrito = '' si no quiere guardar dirección
     * @return array Errores ['campo' => 'mensaje']
     */
    public function actualizarPerfil($idUsuario, $datos)
    {
        $errores = [];

        if (!Validador::texto($datos['nombre'], 2, 60)) {
            $errores['nombre'] = 'Escribí tu nombre.';
        }
        if (!Validador::texto($datos['primer_apellido'], 2, 40)) {
            $errores['primer_apellido'] = 'Escribí tu primer apellido.';
        }
        if (!Validador::texto($datos['segundo_apellido'], 0, 40)) {
            $errores['segundo_apellido'] = 'El segundo apellido es muy largo.';
        }
        if (!Validador::correo($datos['correo'])) {
            $errores['correo'] = 'Escribí un correo válido.';
        } else {
            $otro = $this->usuarios->obtenerPorCorreo($datos['correo']);
            if ($otro !== null && (int) $otro['id_usuario'] !== $idUsuario) {
                $errores['correo'] = 'Ese correo ya lo usa otra cuenta.';
            }
        }
        if (!Validador::telefono($datos['telefono'])) {
            $errores['telefono'] = 'El teléfono debe tener 8 dígitos.';
        }
        if ($datos['distrito'] !== '' && (new UbicacionService())->distrito($datos['distrito']) === null) {
            $errores['distrito'] = 'Elegí la provincia, el cantón y el distrito.';
        }
        if (!Validador::texto($datos['direccion'], 0, 255)) {
            $errores['direccion'] = 'La dirección es muy larga.';
        }

        if (count($errores) > 0) {
            return $errores;
        }

        $usuario = new Usuario();
        $usuario->setIdUsuario($idUsuario);
        $usuario->setNombre($datos['nombre']);
        $usuario->setPrimerApellido($datos['primer_apellido']);
        $usuario->setSegundoApellido($datos['segundo_apellido']);
        $usuario->setCorreo($datos['correo']);
        $usuario->setTelefono(Validador::formatoTelefono($datos['telefono']));
        $usuario->setIdDistrito($datos['distrito'] !== '' ? (int) $datos['distrito'] : 0);
        $usuario->setDireccion($datos['direccion']);
        $this->usuarios->actualizarPerfil($usuario);

        return [];
    }

    /**
     * Guarda la dirección principal del usuario (la que se ofrece al pagar).
     *
     * @param int $idUsuario
     * @param int $idDistrito
     * @param string $otrasSenas
     */
    public function guardarDireccion($idUsuario, $idDistrito, $otrasSenas)
    {
        $this->usuarios->guardarDireccion($idUsuario, $idDistrito, $otrasSenas);
    }

    /**
     * Cambia solo el teléfono (el vendedor lo edita desde su perfil).
     *
     * @param int $idUsuario
     * @param string $telefono
     */
    public function actualizarTelefono($idUsuario, $telefono)
    {
        $fila = $this->usuarios->obtenerPorId($idUsuario);
        $usuario = new Usuario($fila);
        $usuario->setTelefono(Validador::formatoTelefono($telefono));
        $this->usuarios->actualizarPerfil($usuario);
    }

    // ---------------------------------------------------------------
    // Administrador · Gestión de Usuarios
    // ---------------------------------------------------------------

    /** @return array Usuarios con la forma que usa usuario_rol/clientes.php */
    public function listarParaAdmin()
    {
        $filas = $this->usuarios->listarParaAdmin();
        $lista = [];
        foreach ($filas as $fila) {
            $esEmprendedor = $fila['nombre_rol'] === ROL_EMPRENDEDOR;
            $lista[] = [
                'id'       => (int) $fila['id_usuario'],
                // Para emprendedores se muestra el negocio y entre paréntesis la persona a cargo
                'nombre'   => $esEmprendedor && $fila['nombre_negocio'] !== null ? $fila['nombre_negocio'] : $fila['nombre'],
                'contacto' => $esEmprendedor ? $fila['nombre'] : '',
                'rol'      => $esEmprendedor ? 'emprendedor' : 'cliente',
                'correo'   => $fila['correo'],
                'registro' => date('Y-m-d', strtotime($fila['fecha_registro'])),
                'estado'   => $fila['estado'] === 'activo' ? 'activo' : 'suspendido',
            ];
        }
        return $lista;
    }

    /** @return array ['todos', 'clientes', 'emprendedores', 'suspendidos'] */
    public function totalesParaAdmin()
    {
        $fila = $this->usuarios->contar();
        return [
            'todos'         => (int) $fila['todos'],
            'clientes'      => (int) $fila['clientes'],
            'emprendedores' => (int) $fila['emprendedores'],
            'suspendidos'   => (int) $fila['suspendidos'],
        ];
    }

    /**
     * Crea (invita) o edita un usuario desde el panel del administrador.
     *
     * @param array $datos ['id_usuario', 'cedula', 'nombre', 'correo', 'rol', 'contacto', 'contrasena']
     * @return array ['errores' => [...], 'mensaje' => texto de éxito]
     */
    public function guardarDesdeAdmin($datos)
    {
        $idUsuario = (int) $datos['id_usuario'];
        $esEmprendedor = $datos['rol'] === 'emprendedor';
        $errores = [];

        if (!Validador::texto($datos['nombre'], 3, 100)) {
            $errores['nombre'] = 'Escribí el nombre.';
        }
        if ($idUsuario === 0 && !Validador::cedula($datos['cedula'])) {
            $errores['cedula'] = 'Escribí la cédula solo con números (9 a 12).';
        } elseif ($idUsuario === 0 && $this->usuarios->obtenerPorCedula($datos['cedula']) !== null) {
            $errores['cedula'] = 'Ya existe una cuenta con esa cédula.';
        }
        if (!Validador::correo($datos['correo'])) {
            $errores['correo'] = 'Escribí un correo válido.';
        } else {
            $otro = $this->usuarios->obtenerPorCorreo($datos['correo']);
            if ($otro !== null && (int) $otro['id_usuario'] !== $idUsuario) {
                $errores['correo'] = 'Ese correo ya lo usa otra cuenta.';
            }
        }
        if ($esEmprendedor && !Validador::texto($datos['contacto'], 3, 100)) {
            $errores['contacto'] = 'Escribí la persona a cargo del emprendimiento.';
        }
        if ($idUsuario === 0 && strlen($datos['contrasena']) < 8) {
            $errores['contrasena'] = 'Generá una contraseña temporal.';
        }
        if (count($errores) > 0) {
            return ['errores' => $errores, 'mensaje' => ''];
        }

        // En emprendedores: nombre = negocio y contacto = persona
        $nombrePersona = $esEmprendedor ? $datos['contacto'] : $datos['nombre'];
        $partes = $this->partirNombre($nombrePersona);
        if ($partes === null) {
            $campo = $esEmprendedor ? 'contacto' : 'nombre';
            return ['errores' => [$campo => 'Escribí el nombre y al menos un apellido.'], 'mensaje' => ''];
        }
        $vendedores = new VendedorRepository();

        if ($idUsuario > 0) {
            $actual = $this->usuarios->obtenerPorId($idUsuario);
            if ($actual === null) {
                return ['errores' => ['general' => 'Ese usuario no existe.'], 'mensaje' => ''];
            }
            // Si el nombre completo no cambió se dejan las partes como estaban
            // (así no se desacomoda un nombre compuesto como "María José")
            $usuario = new Usuario($actual);
            if ($nombrePersona !== $actual['nombre']) {
                $usuario->setNombre($partes[0]);
                $usuario->setPrimerApellido($partes[1]);
                $usuario->setSegundoApellido($partes[2]);
            }
            $usuario->setCorreo($datos['correo']);
            $this->usuarios->actualizarDesdeAdmin($usuario);

            // Si es emprendedor también se actualiza el nombre del negocio
            if ($actual['id_emprendimiento'] !== null) {
                $vendedor = new Vendedor($vendedores->obtener((int) $actual['id_emprendimiento']));
                $vendedor->setNombreNegocio($datos['nombre']);
                $vendedor->setLogo('');
                $vendedores->actualizar($vendedor);
            }
            return ['errores' => [], 'mensaje' => 'Los cambios de ' . $datos['nombre'] . ' se guardaron.'];
        }

        // Usuario nuevo con contraseña temporal
        $rol = $this->usuarios->obtenerRol($esEmprendedor ? ROL_EMPRENDEDOR : ROL_CLIENTE);
        $usuario = new Usuario();
        $usuario->setIdRol($rol->getIdRol());
        $usuario->setCedula($datos['cedula']);
        $usuario->setNombre($partes[0]);
        $usuario->setPrimerApellido($partes[1]);
        $usuario->setSegundoApellido($partes[2]);
        $usuario->setCorreo($datos['correo']);
        $usuario->setContrasena(password_hash($datos['contrasena'], PASSWORD_DEFAULT));

        $this->usuarios->iniciarTransaccion();
        try {
            $idNuevo = $this->usuarios->crear($usuario);

            // El emprendedor invitado por el admin queda aprobado de una vez
            if ($esEmprendedor) {
                $vendedor = new Vendedor();
                $vendedor->setIdUsuario($idNuevo);
                $vendedor->setNombreNegocio($datos['nombre']);
                $vendedor->setDescripcion('');
                $vendedor->setIdDistrito(0);   // la dirección la completa el emprendedor en su perfil
                $idEmprendimiento = $vendedores->crear($vendedor);
                $vendedores->resolver($idEmprendimiento, 'aprobado', '');
            }
            $this->usuarios->confirmarTransaccion();
        } catch (Exception $error) {
            $this->usuarios->deshacerTransaccion();
            throw $error;
        }

        return ['errores' => [], 'mensaje' => 'Se creó la cuenta de ' . $datos['nombre'] . '. Compartile la contraseña temporal.'];
    }

    /**
     * Separa un nombre completo escrito en un solo campo (panel del admin):
     * "Laura Vargas Mora" => ['Laura', 'Vargas', 'Mora'].
     * Si hay más de tres palabras, las que sobran van al segundo apellido.
     *
     * @param string $completo
     * @return array|null [nombre, primer apellido, segundo apellido] o null si falta el apellido
     */
    private function partirNombre($completo)
    {
        $palabras = explode(' ', trim($completo));
        $limpias = [];
        for ($i = 0; $i < count($palabras); $i++) {
            if ($palabras[$i] !== '') {
                $limpias[] = $palabras[$i];
            }
        }

        if (count($limpias) < 2) {
            return null;
        }
        return [$limpias[0], $limpias[1], implode(' ', array_slice($limpias, 2))];
    }

    /**
     * Suspende o reactiva una cuenta (no se borra nada).
     *
     * @param int $idUsuario
     * @return string Mensaje para el administrador
     */
    public function alternarEstado($idUsuario)
    {
        $fila = $this->usuarios->obtenerPorId($idUsuario);
        if ($fila === null || $fila['nombre_rol'] === ROL_ADMIN) {
            return 'Esa cuenta no se puede suspender.';
        }

        $nombre = $fila['nombre_negocio'] !== null ? $fila['nombre_negocio'] : $fila['nombre'];
        if ($fila['estado'] === 'activo') {
            $this->usuarios->cambiarEstado($idUsuario, 'bloqueado');
            return $nombre . ' fue suspendido. Ya no puede iniciar sesión y se conserva su historial.';
        }

        $this->usuarios->cambiarEstado($idUsuario, 'activo');
        return $nombre . ' fue reactivado.';
    }
}
