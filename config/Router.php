<?php
/**
 * Router — Sistema de routing dinámico basado en whitelist.
 *
 * Reemplaza el bloque if/elseif de ~70 comparaciones en plantilla.php
 * por un mecanismo mantenible: agregar una ruta nueva es solo añadir
 * una cadena al array en config/routes.php.
 *
 * Seguridad: in_array() con comparación estricta (tercer parámetro true)
 * previene path traversal y cualquier acceso a archivos arbitrarios.
 *
 * Uso (desde vistas/plantilla.php):
 *   require_once '../config/Router.php';
 *   $router = new Router(require '../config/routes.php');
 *
 *   if (isset($_GET["ruta"])) {
 *       if ($router->isAllowed($_GET["ruta"])) {
 *           include_once "modulos/" . $_GET["ruta"] . ".php";
 *       } else {
 *           include_once "modulos/error404.php";
 *       }
 *   }
 */
class Router
{
    /** @var string[]  Lista de slugs permitidos */
    private $routes;

    /**
     * @param string[] $routes  Array de rutas permitidas (ver config/routes.php)
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Indica si el slug es una ruta registrada y permitida.
     * La comparación es estricta para evitar type-juggling.
     *
     * @param  string $ruta  Valor de $_GET["ruta"]
     * @return bool
     */
    public function isAllowed($ruta)
    {
        return in_array($ruta, $this->routes, true);
    }

    /**
     * Retorna la lista completa de rutas registradas.
     * Útil para debugging o generación de menús.
     *
     * @return string[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}
