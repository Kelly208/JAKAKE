# Sistema de Permisos - Papelería JAKAKE

## Arquitectura de Permisos

### 1. Middleware (utils/Middleware.php)
Sistema centralizado de control de acceso con los siguientes métodos:

#### Métodos de Verificación:
- `requireAuth()` - Verifica que el usuario esté autenticado
- `requireAdmin()` - Verifica rol de administrador
- `requireRole($roles)` - Verifica roles específicos (array o string)
- `checkUserStatus()` - Verifica que la cuenta esté activa

#### Métodos de Consulta (retornan booleanos):
- `isAdmin()` - ¿Es administrador?
- `isCajero()` - ¿Es cajero?
- `canEdit()` - ¿Puede editar? (solo admin)
- `canDelete()` - ¿Puede eliminar? (solo admin)
- `canViewReports()` - ¿Puede ver reportes?
- `canManageUsers()` - ¿Puede gestionar usuarios? (solo admin)

### 2. Protección de Rutas (public/index.php)

#### Rutas Públicas:
```php
$publicRoutes = ['/login'];
```

#### Rutas Admin:
```php
$adminRoutes = [
    '/usuarios',
    '/usuarios/crear',
];
```

#### Flujo de Verificación:
1. Si no es ruta pública → `requireAuth()`
2. Verificar estado de cuenta → `checkUserStatus()`
3. Si es ruta admin → `requireAdmin()`

### 3. Mejoras en Session (utils/Session.php)

Nuevos métodos agregados:
- `delete($key)` - Eliminar variable de sesión
- `isAdmin()` - Verificar si es administrador
- `isCajero()` - Verificar si es cajero
- `isAuthenticated()` - Verificar si está autenticado

### 4. Vistas de Error

#### 403 - Acceso Denegado
- Diseño profesional con animación bounce
- Botones: Dashboard, Cerrar Sesión
- Mensaje: "No tienes permisos para acceder a esta sección"

#### 404 - Página No Encontrada
- Código 404 grande y visible
- Botones: Dashboard, Volver atrás
- Mensaje: "La página que buscas no existe"

## Matriz de Permisos

| Módulo | Administrador | Cajero |
|--------|--------------|---------|
| Dashboard | ✅ Completo | ✅ Básico |
| Productos | ✅ CRUD | 🔍 Solo lectura |
| Clientes | ✅ CRUD | 🔍 Solo lectura |
| Ventas | ✅ Todas | ✅ Todas |
| Devoluciones | ✅ Todas | ✅ Todas |
| Reportes | ✅ Completos | ✅ Básicos |
| Usuarios | ✅ Gestión | ❌ Sin acceso |

## Uso en Controladores

### Antes (Código Duplicado):
```php
private function verificarAuth() {
    Session::start();
    if (!Session::get('user_id')) {
        header('Location: /login');
        exit;
    }
}
```

### Ahora (Centralizado):
El middleware se aplica automáticamente en `index.php` antes de ejecutar cualquier controlador.

## Seguridad Implementada

1. ✅ **Autenticación obligatoria** en todas las rutas (excepto /login)
2. ✅ **Verificación de estado** de cuenta en cada petición
3. ✅ **Control de rol** para secciones administrativas
4. ✅ **Protección de usuarios** - No puedes modificarte a ti mismo
5. ✅ **Validación de email único** al crear/editar usuarios
6. ✅ **Hash seguro** de contraseñas con password_hash()
7. ✅ **Mensajes informativos** con redirección apropiada

## Próximas Mejoras Sugeridas

- [ ] Auditoría de accesos denegados
- [ ] Sistema de tokens para APIs (si se necesita)
- [ ] Rate limiting para prevenir ataques de fuerza bruta
- [ ] Permisos granulares por módulo (RBAC avanzado)
