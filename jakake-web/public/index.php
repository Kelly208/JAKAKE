<?php
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
    ],
    'POST' => [
        '/login' => 'AuthController@login',
    ]
];
function executeRoute($controller, $action) {
    $controllerClass = "App\\Controllers\\$controller";
    if (!class_exists($controllerClass)) die("Controlador no encontrado: $controller");
    $instance = new $controllerClass();
    if (!method_exists($instance, $action)) die("Método no encontrado: $action");
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
