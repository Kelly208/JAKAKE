<?php
// Front Controller - Punto de entrada único

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Utils\Session;

Session::start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$routes = [
    'GET' => [
        '/' => 'DashboardController@index',
        '/login' => 'AuthController@login',
        '/logout' => 'AuthController@logout',

        // Productos
        '/productos' => 'ProductoController@index',
        '/productos/crear' => 'ProductoController@crear',

        // Ventas
        '/ventas/registrar' => 'VentaController@registrar',
        '/ventas/periodo' => 'VentaController@periodo',

        // Clientes
        '/clientes/crear' => 'ClienteController@crear',

        // Proveedores
        '/proveedores/crear' => 'ProveedorController@crear',

        // Reportes
        '/reportes/mas-vendidos' => 'ReporteController@topProductos',
        '/reportes/bonos' => 'ReporteController@bonosActivos',
        '/reportes/auditoria' => 'ReporteController@auditoriaReciente',
    ],
    'POST' => [
        '/login' => 'AuthController@login',

        // Productos
        '/productos/crear' => 'ProductoController@crear',

        // Ventas
        '/ventas/procesar' => 'VentaController@procesar',

        // Clientes
        '/clientes/crear' => 'ClienteController@crear',

        // Proveedores
        '/proveedores/crear' => 'ProveedorController@crear',
    ]
];

function executeRoute($controller, $action) {
    $controllerClass = "App\\Controllers\\$controller";
    if (!class_exists($controllerClass)) {
        die("Controlador no encontrado: $controller");
    }
    $instance = new $controllerClass();
    if (!method_exists($instance, $action)) {
        die("Método no encontrado: $action en $controller");
    }
    return $instance->$action();
}

if (isset($routes[$method][$uri])) {
    $route = $routes[$method][$uri];
    list($controller, $action) = explode('@', $route);
    executeRoute($controller, $action);
} else {
    http_response_code(404);
    echo "Página no encontrada: $uri";
}
