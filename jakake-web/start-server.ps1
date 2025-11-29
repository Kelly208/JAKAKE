# Script para mantener el servidor PHP corriendo
Write-Host "=== Servidor PHP Auto-Restart ===" -ForegroundColor Green
Write-Host "Presiona Ctrl+C para detener" -ForegroundColor Yellow
Write-Host ""

$dir = "c:\Users\kelly\OneDrive\Escritorio\Semestre 5\Bases de datos\JakakeWebSide\jakake-web\public"

while ($true) {
    Write-Host "[$(Get-Date -Format 'HH:mm:ss')] Iniciando servidor en http://localhost:8000" -ForegroundColor Cyan
    
    try {
        # Iniciar servidor PHP
        & "C:\xampp\php\php.exe" -S localhost:8000 -t $dir
        
        # Si llega aquí, el servidor se detuvo
        Write-Host "[$(Get-Date -Format 'HH:mm:ss')] Servidor detenido. Reiniciando en 2 segundos..." -ForegroundColor Yellow
        Start-Sleep -Seconds 2
    }
    catch {
        Write-Host "[$(Get-Date -Format 'HH:mm:ss')] Error: $($_.Exception.Message)" -ForegroundColor Red
        Write-Host "Reiniciando en 2 segundos..." -ForegroundColor Yellow
        Start-Sleep -Seconds 2
    }
}
