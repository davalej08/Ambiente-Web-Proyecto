<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Controller
 * Controlador base: carga vistas con la plantilla general y redirige.
 * Todos los controladores heredan de esta clase (class XController extends Controller).
 */

class Controller
{
    /**
     * Muestra una vista de app/views con las variables que necesita.
     * Ej: $this->vista('producto/listado', ['productos' => $productos]);
     * dentro de la vista queda disponible la variable $productos.
     *
     * @param string $vista  Ruta dentro de app/views, sin .php
     * @param array $datos   Variables para la vista
     */
    protected function vista($vista, $datos = [])
    {
        // extract() convierte cada llave del arreglo en una variable
        extract($datos);
        require __DIR__ . '/../views/' . $vista . '.php';
    }

    /**
     * Envía al navegador a otra ruta de la aplicación y termina.
     *
     * @param string $ruta Ej: 'auth/login'
     */
    protected function redirigir($ruta)
    {
        header('Location: ' . ruta($ruta));
        exit;
    }

    /**
     * Vuelve a la pantalla anterior (si es de Ventica); si no, a la ruta indicada.
     *
     * @param string $porDefecto
     */
    protected function volver($porDefecto)
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $anterior = $_SERVER['HTTP_REFERER'];
            $servidor = $_SERVER['HTTP_HOST'];
            // Solo se vuelve a páginas del mismo sitio
            if (strpos($anterior, '://' . $servidor . '/') !== false) {
                header('Location: ' . $anterior);
                exit;
            }
        }
        $this->redirigir($porDefecto);
    }

    /** @return bool true si la página se pidió enviando un formulario */
    protected function esPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Lee un campo de $_POST sin espacios al inicio ni al final.
     *
     * @param string $campo
     * @param string $porDefecto
     * @return string
     */
    protected function entrada($campo, $porDefecto = '')
    {
        if (isset($_POST[$campo]) && is_string($_POST[$campo])) {
            return trim($_POST[$campo]);
        } else {
            return $porDefecto;
        }
    }

    /**
     * Lee un campo de $_GET.
     *
     * @param string $campo
     * @param string $porDefecto
     * @return string
     */
    protected function parametro($campo, $porDefecto = '')
    {
        if (isset($_GET[$campo]) && is_string($_GET[$campo])) {
            return trim($_GET[$campo]);
        } else {
            return $porDefecto;
        }
    }

    /**
     * Revisa el código CSRF del formulario. Si no coincide, no se procesa nada.
     *
     * @param string $rutaError A dónde volver si el código no es válido
     */
    protected function validarCsrf($rutaError)
    {
        if (!Sesion::csrfValido($this->entrada('csrf_token'))) {
            Sesion::mensaje('error', 'El formulario venció. Intentalo de nuevo.');
            $this->redirigir($rutaError);
        }
    }

    /** Si nadie ha iniciado sesión, lo manda al login y recuerda a dónde quería ir. */
    protected function requiereSesion()
    {
        if (Sesion::usuario() === null) {
            $_SESSION['volver_a'] = $_SERVER['REQUEST_URI'];
            Sesion::mensaje('info', 'Iniciá sesión para continuar.');
            $this->redirigir('auth/login');
        }
    }

    /**
     * Solo deja pasar al rol indicado (RF-06). Los demás ven la pantalla 403.
     *
     * @param string $rol ROL_CLIENTE | ROL_EMPRENDEDOR | ROL_ADMIN
     */
    protected function requiereRol($rol)
    {
        $this->requiereSesion();

        if (Sesion::rol() !== $rol) {
            http_response_code(403);
            $this->vista('general/error', [
                'codigo'      => 403,
                'tituloError' => 'Esta sección no es para tu tipo de cuenta',
                'textoError'  => 'Ingresaste como ' . Sesion::rol() . '. Volvé al inicio para seguir navegando.',
            ]);
            exit;
        }
    }

    /** Página 404: la ruta no existe. */
    public function noEncontrada()
    {
        http_response_code(404);
        $this->vista('general/error', [
            'codigo'      => 404,
            'tituloError' => 'No encontramos esta página',
            'textoError'  => 'Puede que el enlace esté mal escrito o que la página ya no exista.',
        ]);
    }

    /**
     * Página 500: falló la base de datos o la configuración.
     *
     * @param string $detalle Mensaje técnico (solo se muestra en el computador local)
     */
    public function errorServidor($detalle = '')
    {
        http_response_code(500);
        $esLocal = isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1');

        $this->vista('general/error', [
            'codigo'      => 500,
            'tituloError' => 'Algo salió mal de nuestro lado',
            'textoError'  => 'No pudimos conectar con la base de datos. Probá de nuevo en un momento.',
            'detalle'     => $esLocal ? $detalle : '',
        ]);
    }
}
