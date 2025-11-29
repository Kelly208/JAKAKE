@echo off
echo ========================================
echo Ejecutando Scripts SQL - Papeleria JAKAKE
echo ========================================
echo.

cd /d "%~dp0database"

echo [1/9] Creando tablas...
mysql -u root -p2409 < 01_crear_tablas.sql
if errorlevel 1 goto error

echo [2/9] Creando triggers de auditoria...
mysql -u root -p2409 papeleria_jakake < 02_triggers_auditoria.sql
if errorlevel 1 goto error

echo [3/9] Creando triggers de inventario...
mysql -u root -p2409 papeleria_jakake < 03_triggers_inventario.sql
if errorlevel 1 goto error

echo [4/9] Creando triggers de seguridad...
mysql -u root -p2409 papeleria_jakake < 04_triggers_seguridad.sql
if errorlevel 1 goto error

echo [5/9] Creando triggers de bonos...
mysql -u root -p2409 papeleria_jakake < 05_trigger_bonos.sql
if errorlevel 1 goto error

echo [6/9] Creando procedimientos almacenados...
mysql -u root -p2409 papeleria_jakake < 06_procedimientos.sql
if errorlevel 1 goto error

echo [7/9] Creando funciones...
mysql -u root -p2409 papeleria_jakake < 07_funciones.sql
if errorlevel 1 goto error

echo [8/9] Creando indices...
mysql -u root -p2409 papeleria_jakake < 08_indices.sql
if errorlevel 1 goto error

echo [9/9] Insertando datos de prueba...
mysql -u root -p2409 papeleria_jakake < 09_datos_prueba.sql
if errorlevel 1 goto error

echo.
echo ========================================
echo COMPLETADO EXITOSAMENTE
echo ========================================
echo La base de datos esta lista para usar
echo.
pause
exit

:error
echo.
echo ========================================
echo ERROR AL EJECUTAR SCRIPTS
echo ========================================
echo Revisa el error anterior
echo.
pause
exit /b 1
