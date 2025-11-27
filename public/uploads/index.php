<?php
// Front Controller - Punto de entrada único

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$routes = [
    'GET' => [
        '/' => 'DashboardController@index',
        '/login' => 'AuthController@login',

        '/productos' => 'ProductoController@index',
        '/productos/crear' => 'ProductoController@crear',

        '/ventas/registrar' => 'VentaController@registrar',
        '/ventas/periodo' => 'VentaController@periodo',

        '/clientes/crear' => 'ClienteController@crear',
        '/proveedores/crear' => 'ProveedorController@crear',

        '/reportes/mas-vendidos' => 'ReporteController@topProductos',
        '/reportes/bonos' => 'ReporteController@bonosActivos',
        '/reportes/auditoria' => 'ReporteController@auditoriaReciente',
    ],
    'POST' => [
        '/login' => 'AuthController@login',
        '/ventas/procesar' => 'VentaController@procesar',
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
