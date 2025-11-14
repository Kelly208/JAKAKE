<?php
// Front Controller - Punto de entrada único

// Configuración de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Cargar Composer
require_once __DIR__ . '/../vendor/autoload.php';

use App\Utils\Session;

// Iniciar sesión
Session::start();

// Ruteo simple
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Mapeo de rutas
$routes = [
    'GET' => [
        '/' => 'DashboardController@index',
        '/login' => 'AuthController@login',
        '/logout' => 'AuthController@logout',
        '/productos' => 'ProductoController@index',
        '/productos/crear' => 'ProductoController@crear',
        '/ventas/registrar' => 'VentaController@registrar',
        '/clientes' => 'ClienteController@listado',
    ],
    'POST' => [
        '/login' => 'AuthController@login',
        '/productos/crear' => 'ProductoController@crear',
        '/ventas/procesar' => 'VentaController@procesar',
    ]
];

// Función para ejecutar controlador
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

// Encontrar ruta
if (isset($routes[$method][$uri])) {
    $route = $routes[$method][$uri];
    list($controller, $action) = explode('@', $route);
    executeRoute($controller, $action);
} else {
    http_response_code(404);
    echo "Página no encontrada: $uri";
} 