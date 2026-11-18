<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * RegistroService
 * Registro de clientes y vendedores, inicio de sesión y recuperación de contraseña
 * (RF-01, RF-02, RF-04, RF-07).
 */

class RegistroService
{
    /** @var UsuarioRepository */
    private $usuarios;
    /** @var VendedorRepository */
    private $vendedores;
    /** @var CorreoService */
    private $correo;

    public function __construct()
    {
        $this->usuarios   = new UsuarioRepository();
        $this->vendedores = new VendedorRepository();
        $this->correo     = new CorreoService();
    }

    /**
     * Revisa correo y contraseña.
     *
     * @param string $correo
     * @param string $contrasena
     * @return array ['usuario' => fila o null, 'errores' => ['campo' => 'mensaje']]
     */
    public function iniciarSesion($correo, $contrasena)
    {
        $errores = [];

        if (!Validador::correo($correo)) {
            $errores['correo'] = 'Escribí un correo válido.';
        }
        if ($contrasena === '') {
            $errores['contrasena'] = 'Escribí tu contraseña.';
        }
        if (count($errores) > 0) {
            return ['usuario' => null, 'errores' => $errores];
        }

        $usuario = $this->usuarios->obtenerPorCorreo($correo);

        // Mismo mensaje si el correo no existe o la contraseña está mal (no damos pistas)
        if ($usuario === null || !password_verify($contrasena, $usuario['contrasena'])) {
            return ['usuario' => null, 'errores' => ['general' => 'El correo o la contraseña no coinciden.']];
        }

        if ($usuario['estado'] !== 'activo') {
            return ['usuario' => null, 'errores' => ['general' => 'Tu cuenta está suspendida. Escribinos a soporte@ventica.cr.']];
        }

        if ($usuario['nombre_rol'] === ROL_EMPRENDEDOR) {
            if ($usuario['estado_verificacion'] === 'pendiente') {
                return ['usuario' => null, 'errores' => ['general' => 'Tu solicitud todavía está en revisión. Te avisaremos por correo cuando la aprobemos.']];
            }
            if ($usuario['estado_verificacion'] === 'rechazado') {
                return ['usuario' => null, 'errores' => ['general' => 'Tu solicitud no fue aprobada. Revisá el correo que te enviamos con el motivo.']];
            }
        }

        return ['usuario' => $usuario, 'errores' => []];
    }

    /**
     * Valida los datos personales comunes de los dos registros.
     *
     * @param array $datos
     * @return array Errores encontrados
     */
    private function validarDatosPersonales($datos)
    {
        $errores = [];

        if (!Validador::cedula($datos['cedula'])) {
            $errores['cedula'] = 'Escribí tu cédula solo con números (9 dígitos).';
        } elseif ($this->usuarios->obtenerPorCedula($datos['cedula']) !== null) {
            $errores['cedula'] = 'Ya existe una cuenta con esta cédula.';
        }
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
        } elseif ($this->usuarios->obtenerPorCorreo($datos['correo']) !== null) {
            $errores['correo'] = 'Ya existe una cuenta con este correo.';
        }
        if (!Validador::telefono($datos['telefono'])) {
            $errores['telefono'] = 'El teléfono debe tener 8 dígitos.';
        }
        if (!Validador::contrasena($datos['contrasena'])) {
            $errores['contrasena'] = 'Mínimo 8 caracteres, con un número y una mayúscula.';
        }
        if ($datos['terminos'] !== '1') {
            $errores['terminos'] = 'Tenés que aceptar los términos.';
        }

        return $errores;
    }

    /**
     * Arma el objeto Usuario con la contraseña cifrada.
     *
     * @param array $datos
     * @param string $nombreRol
     * @return Usuario
     */
    private function nuevoUsuario($datos, $nombreRol)
    {
        $rol = $this->usuarios->obtenerRol($nombreRol);

        $usuario = new Usuario();
        $usuario->setIdRol($rol->getIdRol());
        $usuario->setCedula($datos['cedula']);
        $usuario->setNombre($datos['nombre']);
        $usuario->setPrimerApellido($datos['primer_apellido']);
        $usuario->setSegundoApellido($datos['segundo_apellido']);
        $usuario->setCorreo($datos['correo']);
        $usuario->setTelefono(Validador::formatoTelefono($datos['telefono']));
        // La contraseña NUNCA se guarda tal cual: password_hash() la cifra
        $usuario->setContrasena(password_hash($datos['contrasena'], PASSWORD_DEFAULT));

        return $usuario;
    }

    /**
     * Crea una cuenta de cliente (RF-01).
     *
     * @param array $datos ['cedula', 'nombre', 'primer_apellido', 'segundo_apellido', 'correo', 'telefono', 'contrasena', 'terminos']
     * @return array ['errores' => [...], 'usuario' => fila del usuario nuevo]
     */
    public function registrarCliente($datos)
    {
        $errores = $this->validarDatosPersonales($datos);
        if (count($errores) > 0) {
            return ['errores' => $errores, 'usuario' => null];
        }

        $idUsuario = $this->usuarios->crear($this->nuevoUsuario($datos, ROL_CLIENTE));
        $this->correo->enviarBienvenida($datos['correo'], $datos['nombre'] . ' ' . $datos['primer_apellido'], false);

        return ['errores' => [], 'usuario' => $this->usuarios->obtenerPorId($idUsuario)];
    }

    /**
     * Registra al emprendedor y su emprendimiento, que queda pendiente (RF-02).
     *
     * @param array $datos Los del cliente + ['negocio', 'distrito', 'otras_senas', 'descripcion']
     * @return array ['errores' => [...]]
     */
    public function registrarVendedor($datos)
    {
        $errores = $this->validarDatosPersonales($datos);

        if (!Validador::texto($datos['negocio'], 3, 120)) {
            $errores['negocio'] = 'Escribí el nombre de tu emprendimiento.';
        }
        if ((new UbicacionService())->distrito($datos['distrito']) === null) {
            $errores['distrito'] = 'Elegí la provincia, el cantón y el distrito.';
        }
        if (!Validador::texto($datos['otras_senas'], 0, 255)) {
            $errores['otras_senas'] = 'Las señas son muy largas.';
        }
        if (!Validador::texto($datos['descripcion'], 20, 300)) {
            $errores['descripcion'] = 'Contanos de tu emprendimiento (20 a 300 caracteres).';
        }

        // El logo es opcional; si viene, se valida y se guarda
        $archivos = new ArchivoService();
        $rutaLogo = '';
        if (count($errores) === 0 && $archivos->seSubio('logo')) {
            $resultado = $archivos->guardarImagen('logo', 'logos', 'logo');
            if ($resultado['ok']) {
                $rutaLogo = $resultado['ruta'];
            } else {
                $errores['logo'] = $resultado['error'];
            }
        }

        if (count($errores) > 0) {
            return ['errores' => $errores];
        }

        // Usuario y emprendimiento se guardan juntos: si uno falla, no queda ninguno
        $this->usuarios->iniciarTransaccion();
        try {
            $idUsuario = $this->usuarios->crear($this->nuevoUsuario($datos, ROL_EMPRENDEDOR));

            $vendedor = new Vendedor();
            $vendedor->setIdUsuario($idUsuario);
            $vendedor->setNombreNegocio($datos['negocio']);
            $vendedor->setDescripcion($datos['descripcion']);
            $vendedor->setIdDistrito((int) $datos['distrito']);
            $vendedor->setOtrasSenas($datos['otras_senas']);
            $vendedor->setLogo($rutaLogo);
            $vendedor->setMetodosPago('sinpe');
            $this->vendedores->crear($vendedor);

            $this->usuarios->confirmarTransaccion();
        } catch (Exception $error) {
            $this->usuarios->deshacerTransaccion();
            throw $error;
        }

        $this->correo->enviarBienvenida($datos['correo'], $datos['nombre'] . ' ' . $datos['primer_apellido'], true);
        return ['errores' => []];
    }

    /**
     * Código secreto para firmar los enlaces de "cambiar contraseña".
     * Sale de la contraseña de la base de datos, que solo conoce el servidor.
     *
     * @return string
     */
    private function claveSecreta()
    {
        if (!defined('DB_CLAVE')) {
            require_once __DIR__ . '/../../config/database.php';
        }
        return hash('sha256', 'ventica-' . DB_CLAVE . '-' . DB_HOST);
    }

    /**
     * Crea el código del enlace: id del usuario + hora de vencimiento + firma.
     * La firma usa la contraseña actual, así el enlace deja de servir apenas se cambia.
     *
     * @param array $usuario
     * @return string
     */
    private function crearToken($usuario)
    {
        $vence = time() + VIGENCIA_ENLACE_CONTRASENA;
        $firma = hash_hmac('sha256', $usuario['id_usuario'] . '|' . $vence . '|' . $usuario['contrasena'], $this->claveSecreta());
        return $usuario['id_usuario'] . '-' . $vence . '-' . $firma;
    }

    /**
     * Revisa que el código del enlace sea válido y no haya vencido.
     *
     * @param string $token
     * @return array|null El usuario dueño del enlace o null
     */
    public function usuarioDelToken($token)
    {
        $partes = explode('-', $token);
        if (count($partes) !== 3 || !ctype_digit($partes[0]) || !ctype_digit($partes[1])) {
            return null;
        }
        if ((int) $partes[1] < time()) {
            return null;
        }

        $usuario = $this->usuarios->obtenerPorId((int) $partes[0]);
        if ($usuario === null) {
            return null;
        }

        $firmaCorrecta = hash_hmac('sha256', $usuario['id_usuario'] . '|' . $partes[1] . '|' . $usuario['contrasena'], $this->claveSecreta());
        if (!hash_equals($firmaCorrecta, $partes[2])) {
            return null;
        }

        return $usuario;
    }

    /**
     * Envía el enlace para cambiar la contraseña (RF-04).
     * Si el correo no existe no se avisa (para no revelar quién tiene cuenta).
     *
     * @param string $correo
     * @return array ['errores' => [...], 'enlace' => enlace generado o '']
     */
    public function solicitarRecuperacion($correo)
    {
        if (!Validador::correo($correo)) {
            return ['errores' => ['correo' => 'Escribí un correo válido.'], 'enlace' => ''];
        }

        $usuario = $this->usuarios->obtenerPorCorreo($correo);
        if ($usuario === null) {
            return ['errores' => [], 'enlace' => ''];
        }

        $enlace = urlCompleta('auth/cambiar-contrasena') . '?token=' . urlencode($this->crearToken($usuario));
        $this->correo->enviarRecuperacion($usuario['correo'], $usuario['nombre'], $enlace);

        return ['errores' => [], 'enlace' => $enlace];
    }

    /**
     * Guarda la contraseña nueva si el enlace es válido.
     *
     * @param string $token
     * @param string $contrasena
     * @param string $confirmacion
     * @return array ['errores' => [...]]
     */
    public function cambiarContrasena($token, $contrasena, $confirmacion)
    {
        $usuario = $this->usuarioDelToken($token);
        if ($usuario === null) {
            return ['errores' => ['general' => 'El enlace venció o ya se usó. Pedí uno nuevo.']];
        }

        $errores = [];
        if (!Validador::contrasena($contrasena)) {
            $errores['contrasena'] = 'Mínimo 8 caracteres, con un número y una mayúscula.';
        }
        if ($contrasena !== $confirmacion) {
            $errores['confirmacion'] = 'Las contraseñas no coinciden.';
        }
        if (count($errores) > 0) {
            return ['errores' => $errores];
        }

        $this->usuarios->actualizarContrasena($usuario['id_usuario'], password_hash($contrasena, PASSWORD_DEFAULT));
        return ['errores' => []];
    }
}
