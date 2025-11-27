<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada - Papelería JAKAKE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            color: white;
        }
        .error-icon {
            font-size: 6rem;
            margin-bottom: 1rem;
        }
        .error-card {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            margin: 0 auto;
        }
        .error-code {
            font-size: 5rem;
            font-weight: bold;
            color: #667eea;
            line-height: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-card">
            <div class="error-code">404</div>
            <div class="error-icon text-warning">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <h1 class="text-dark mb-3">Página no encontrada</h1>
            <p class="text-muted mb-4">
                La página que buscas no existe o ha sido movida.
            </p>
            <div class="d-grid gap-2">
                <a href="/dashboard" class="btn btn-primary btn-lg">
                    <i class="bi bi-house-door"></i> Volver al Dashboard
                </a>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver atrás
                </a>
            </div>
        </div>
    </div>
</body>
</html>
