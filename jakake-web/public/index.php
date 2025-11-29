<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Utils\Session;
use App\Utils\Middleware;

Session::start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Redirigir raíz a dashboard si está autenticado, si no a login
if ($uri === '/') {
    if (Session::get('user_id')) {
        header('Location: /dashboard');
    } else {
        header('Location: /login');
    }
    exit;
}

// Definir rutas protegidas que requieren autenticación
$publicRoutes = ['/login'];

// Definir rutas que solo pueden acceder administradores
$adminRoutes = [
    '/usuarios',
    '/usuarios/crear',
];

// Verificar si la ruta requiere protección
if (!in_array($uri, $publicRoutes) && strpos($uri, '/login') === false) {
    // Verificar autenticación en todas las rutas no públicas
    Middleware::requireAuth();
    
    // Verificar estado de la cuenta
    Middleware::checkUserStatus();
    
    // Verificar permisos de administrador para rutas admin
    foreach ($adminRoutes as $adminRoute) {
        if (strpos($uri, $adminRoute) === 0) {
            Middleware::requireAdmin();
            break;
        }
    }
}

$routes = [
    'GET' => [
        '/' => 'DashboardController@index',
        '/dashboard' => 'DashboardController@index',
        '/login' => 'AuthController@showLogin',
        '/logout' => 'AuthController@logout',
        '/productos' => 'ProductoController@index',
        '/productos/crear' => 'ProductoController@crear',
        '/clientes' => 'ClienteController@index',
        '/clientes/crear' => 'ClienteController@crear',
        '/proveedores' => 'ProveedorController@index',
        '/proveedores/crear' => 'ProveedorController@crear',
        '/ventas' => 'VentaController@index',
        '/ventas/nueva' => 'VentaController@nueva',
        '/ventas/validar-bono' => 'VentaController@validarBono',
        '/devoluciones' => 'DevolucionController@index',
        '/devoluciones/nueva' => 'DevolucionController@nueva',
        '/devoluciones/buscar-venta' => 'DevolucionController@buscarVenta',
        '/reportes' => 'ReporteController@index',
        '/reportes/ventas' => 'ReporteController@ventas',
        '/reportes/productos' => 'ReporteController@productos',
        '/reportes/clientes' => 'ReporteController@clientes',
        '/auditoria' => 'AuditoriaController@index',
        
        // Usuarios (Admin)
        '/usuarios' => 'UsuarioController@index',
        '/usuarios/crear' => 'UsuarioController@crear',
    ],
    'POST' => [
        '/login' => 'AuthController@login',
        '/productos/crear' => 'ProductoController@crear',
        '/clientes/crear' => 'ClienteController@crear',
        '/proveedores/crear' => 'ProveedorController@crear',
        '/ventas/nueva' => 'VentaController@nueva',
        '/devoluciones/nueva' => 'DevolucionController@nueva',
        '/usuarios/crear' => 'UsuarioController@crear',
    ]
];
function executeRoute($controller, $action) {
    $controllerClass = "App\\Controllers\\$controller";
    if (!class_exists($controllerClass)) die("Controlador no encontrado: $controller");
    $instance = new $controllerClass();
    if (!method_exists($instance, $action)) die("Método no encontrado: $action");
    return $instance->$action();
}
// Manejar rutas con parámetros (editar producto)
if (preg_match('#^/productos/editar/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $controller = new App\Controllers\ProductoController();
    $controller->editar($id);
    exit;
}

// Manejar rutas con parámetros (editar cliente)
if (preg_match('#^/clientes/editar/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $controller = new App\Controllers\ClienteController();
    $controller->editar($id);
    exit;
}

// Manejar rutas con parámetros (historial cliente)
if (preg_match('#^/clientes/historial/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $controller = new App\Controllers\ClienteController();
    $controller->historial($id);
    exit;
}

// Manejar rutas con parámetros (detalle venta)
if (preg_match('#^/ventas/detalle/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $controller = new App\Controllers\VentaController();
    $controller->detalle($id);
    exit;
}

// Manejar rutas con parámetros (detalle devolución)
if (preg_match('#^/devoluciones/detalle/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $controller = new App\Controllers\DevolucionController();
    $controller->detalle($id);
    exit;
}

// Manejar rutas con parámetros (editar proveedor)
if (preg_match('#^/proveedores/editar/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $controller = new App\Controllers\ProveedorController();
    $controller->editar($id);
    exit;
}

// Manejar rutas con parámetros (eliminar proveedor)
if (preg_match('#^/proveedores/eliminar/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $controller = new App\Controllers\ProveedorController();
    $controller->eliminar($id);
    exit;
}

// Manejar rutas con parámetros (editar usuario)
if (preg_match('#^/usuarios/editar/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $controller = new App\Controllers\UsuarioController();
    
    if ($method === 'POST') {
        $controller->editar($id);
    } else {
        $controller->editar($id);
    }
    exit;
}

// Manejar rutas con parámetros (reset password usuario)
if (preg_match('#^/usuarios/reset-password/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];
    $controller = new App\Controllers\UsuarioController();
    $controller->resetPassword($id);
    exit;
}

if (isset($routes[$method][$uri])) {
    $route = $routes[$method][$uri];
    list($controller, $action) = explode('@', $route);
    executeRoute($controller, $action);
} else {
    http_response_code(404);
    require_once __DIR__ . '/../views/errors/404.php';
}
