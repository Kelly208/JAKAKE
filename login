<?php
// index.php - Página principal de JAKAKE Papelería con formulario de login
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JAKAKE - Papelería</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* ======= ESTILOS GENERALES ======= */
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* ======= HEADER ======= */
        header {
            background-color: #007bff;
            width: 100%;
            padding: 40px 0;
            color: white;
            text-align: center;
            box-sizing: border-box;
        }

        h1 {
            margin: 0;
            font-size: 3.5em;
            color: white;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
        }

        .subtitle {
            font-size: 1.2em;
            margin-top: 10px;
        }

        /* ======= BOTONES SUPERIORES ======= */
        .buttons {
            margin: 25px 0;
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        button,
        .link-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        button:hover,
        .link-button:hover {
            background-color: #0056b3;
        }

        /* ======= SECCIÓN LOGIN ======= */
        .login-section {
            width: 100%;
            max-width: 400px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            box-sizing: border-box;
            text-align: center;
        }

        .login-section h2 {
            margin-top: 0;
            margin-bottom: 20px;
        }

        /* ======= CAMPOS DE FORMULARIO ======= */
        form {
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }

        label {
            text-align: left;
            margin-top: 10px;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: 'Montserrat', sans-serif;
        }

        /* ======= OLVIDÓ CONTRASEÑA ======= */
        .forgot-password {
            text-align: right;
            margin-bottom: 10px;
            margin-top: 5px;
        }

        .forgot-password a {
            color: #007bff;
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        /* ======= REGISTRARSE AHORA ======= */
        .register-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.95em;
        }

        .register-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>JAKAKE</h1>
        <p class="subtitle">¡Todo lo que necesitas para que tu proyecto se haga realidad!</p>
    </header>

    <div class="buttons">
        <button>Productos ✏️</button>
        <button>Carrito de compra 📦</button>
        <button>Finalizar pedido 📝</button>
    </div>

    <section class="login-section">
        <h2>Iniciar Sesión</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="codigo">Código de usuario:</label>
            <input type="text" id="codigo" name="codigo" placeholder="Ingresa tu código de usuario" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>

            <div class="forgot-password">
                <a href="#">¿Olvidó la contraseña?</a>
            </div>

            <button type="submit">Ingresar</button>
        </form>

        <!-- Botón movido aquí -->
        <a href="#" class="register-link">¿Aún no tienes cuenta? Regístrate</a>
    </section>

    <?php
    // Lógica básica de procesamiento del formulario (ejemplo simple)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $codigo = htmlspecialchars($_POST['codigo']);
        $password = htmlspecialchars($_POST['password']);
        
        echo "<p>Datos enviados: Código: $codigo, Contraseña: $password</p>";
    }
    ?>
</body>
</html>
