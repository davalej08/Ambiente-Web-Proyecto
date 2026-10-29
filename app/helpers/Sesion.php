<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Sesion
 * Manejo de sesión, rol del usuario y cierre por inactividad.
 *
 * Lo que se guarda en $_SESSION:
 *   usuario     ['id', 'nombre', 'correo', 'rol', 'id_emprendimiento']   (navbar.php lo lee)
 *   carrito     [id_producto => cantidad]
 *   csrf_token  código que llevan todos los formularios POST
 *   mensaje     ['tipo', 'texto'] que se muestra una sola vez (mensajes.php)
 */

class Sesion
{
    /**
     * Abre la sesión, la cierra si pasó el tiempo de inactividad (RF-07)
     * y crea el código CSRF de los formularios.
     */
    public static function iniciar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['ultima_actividad']) && isset($_SESSION['usuario'])) {
            if (time() - $_SESSION['ultima_actividad'] > SESION_INACTIVIDAD) {
                self::cerrar();
                session_start();
                self::mensaje('aviso', 'Tu sesión se cerró por inactividad. Ingresá de nuevo.');
            }
        }
        $_SESSION['ultima_actividad'] = time();

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    /**
     * Guarda en la sesión los datos del usuario que inició sesión.
     *
     * @param array $usuario Fila de sp_usuario_obtener_por_correo
     */
    public static function iniciarUsuario($usuario)
    {
        // Nuevo id de sesión para evitar robo de sesión
        session_regenerate_id(true);

        $_SESSION['usuario'] = [
            'id'                => (int) $usuario['id_usuario'],
            'nombre'            => $usuario['nombre'],
            'correo'            => $usuario['correo'],
            'rol'               => $usuario['nombre_rol'],
            'id_emprendimiento' => $usuario['id_emprendimiento'] !== null ? (int) $usuario['id_emprendimiento'] : 0,
        ];
    }

    /** Cierra la sesión y borra todos sus datos. */
    public static function cerrar()
    {
        $_SESSION = [];
        session_destroy();
    }

    /**
     * Datos del usuario en sesión o null si nadie ha iniciado sesión.
     *
     * @return array|null
     */
    public static function usuario()
    {
        if (isset($_SESSION['usuario'])) {
            return $_SESSION['usuario'];
        } else {
            return null;
        }
    }

    /** @return int Id del usuario en sesión (0 si no hay) */
    public static function idUsuario()
    {
        $usuario = self::usuario();
        return $usuario !== null ? $usuario['id'] : 0;
    }

    /** @return string Rol del usuario en sesión ('' si no hay) */
    public static function rol()
    {
        $usuario = self::usuario();
        return $usuario !== null ? $usuario['rol'] : '';
    }

    /** @return int Id del emprendimiento del vendedor en sesión (0 si no tiene) */
    public static function idEmprendimiento()
    {
        $usuario = self::usuario();
        return $usuario !== null ? $usuario['id_emprendimiento'] : 0;
    }

    /** Actualiza el nombre que se muestra en la barra (después de editar el perfil). */
    public static function actualizarNombre($nombre)
    {
        if (isset($_SESSION['usuario'])) {
            $_SESSION['usuario']['nombre'] = $nombre;
        }
    }

    /**
     * Revisa que el código CSRF que llegó en el formulario sea el de la sesión.
     *
     * @param string $token
     * @return bool
     */
    public static function csrfValido($token)
    {
        return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Mensaje que se muestra una vez en la siguiente pantalla (mensajes.php).
     *
     * @param string $tipo  exito | error | aviso | info
     * @param string $texto
     */
    public static function mensaje($tipo, $texto)
    {
        $_SESSION['mensaje'] = ['tipo' => $tipo, 'texto' => $texto];
    }
}
