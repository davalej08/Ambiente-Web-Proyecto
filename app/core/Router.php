<?php
/**
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Router
 * Resuelve la URL y ejecuta el controlador y método correspondiente.
 *
 * Las rutas se registran en config/rutas.php:
 *     $router->get('catalogo', 'CatalogoController', 'catalogo');
 *     $router->post('carrito/agregar', 'CarritoController', 'agregar');
 */

class Router
{
    /** @var array Rutas registradas: ['GET' => ['catalogo' => ['Controlador', 'metodo']], 'POST' => [...]] */
    private $rutas = ['GET' => [], 'POST' => []];

    /**
     * Registra una ruta que responde a GET (abrir una pantalla).
     *
     * @param string $ruta         Ej: 'producto/detalle'
     * @param string $controlador  Ej: 'CatalogoController'
     * @param string $metodo       Ej: 'detalle'
     */
    public function get($ruta, $controlador, $metodo)
    {
        $this->rutas['GET'][$ruta] = [$controlador, $metodo];
    }

    /**
     * Registra una ruta que responde a POST (enviar un formulario).
     *
     * @param string $ruta
     * @param string $controlador
     * @param string $metodo
     */
    public function post($ruta, $controlador, $metodo)
    {
        $this->rutas['POST'][$ruta] = [$controlador, $metodo];
    }

    /**
     * Devuelve la ruta pedida sin la carpeta base ni barras al inicio o final.
     * Ej: /VenticaProyecto/public/producto/detalle?id=3  ->  producto/detalle
     *
     * @return string
     */
    public function rutaActual()
    {
        // Con Apache (.htaccess) la ruta llega en ?url=
        if (isset($_GET['url'])) {
            return trim($_GET['url'], '/');
        }

        $ruta = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $ruta = rawurldecode($ruta);

        // Se quita la carpeta base (BASE_URL) del inicio
        if (BASE_URL !== '/' && strpos($ruta, BASE_URL) === 0) {
            $ruta = substr($ruta, strlen(BASE_URL));
        }

        $ruta = trim($ruta, '/');

        // index.php también cuenta como inicio
        if ($ruta === 'index.php') {
            $ruta = '';
        }

        return $ruta;
    }

    /**
     * Busca la ruta pedida y ejecuta el método del controlador.
     * Si no existe muestra la página 404.
     */
    public function despachar()
    {
        $ruta   = $this->rutaActual();
        $metodo = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'POST' : 'GET';

        if (isset($this->rutas[$metodo][$ruta])) {
            $nombreControlador = $this->rutas[$metodo][$ruta][0];
            $nombreMetodo      = $this->rutas[$metodo][$ruta][1];
        } else {
            $nombreControlador = 'CatalogoController';
            $nombreMetodo      = 'noEncontrada';
        }

        try {
            $controlador = new $nombreControlador();
            $controlador->$nombreMetodo();
        } catch (Exception $error) {
            // Error de base de datos o de configuración (ej. falta config/db.php):
            // se registra en el log y se muestra una pantalla amable
            error_log('Ventica: ' . $error->getMessage());
            $base = new Controller();
            $base->errorServidor($error->getMessage());
        }
    }
}
