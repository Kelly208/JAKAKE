<?php
// index.php - Página principal de JAKAKE Papelería con login y registro integrados
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JAKAKE - Papelería</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
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
            padding: 25px 10px 50px 10px;
            color: white;
            text-align: center;
            box-sizing: border-box;
            position: relative;
        }

        .logo {
            position: absolute;
            top: 15px;
            left: 25px;
        }

        .logo img {
            width: 120px;
            height: auto;
        }

        h1 {
            margin: 10px 0 5px;
            font-size: 3em;
            font-weight: 800;
            color: white;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
        }

        .subtitle {
            font-size: 1.2em;
            margin: 0;
        }

        /* ======= BOTONES SUPERIORES ======= */
        .buttons {
            margin: 20px 0;
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
            display: none; /* Oculto por defecto */
        }

        .login-section.show {
            display: block;
        }

        .login-section h2 {
            margin-top: 0;
            margin-bottom: 20px;
        }

        /* ======= SECCIÓN REGISTRO ======= */
        .register-section {
            width: 100%;
            max-width: 850px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 40px;
            box-sizing: border-box;
            text-align: center;
            display: none; /* Oculto por defecto */
            margin-bottom: 40px;
        }

        .register-section.show {
            display: block;
        }

        .register-section h2 {
            margin-top: 0;
            margin-bottom: 25px;
            color: #007bff;
            font-size: 1.8em;
        }

        .register-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px 30px;
            text-align: left;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .full-width {
            grid-column: span 2;
        }

        .data-terms {
            grid-column: span 2;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 8px;
            font-size: 0.9em;
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

        /* ======= ENLACES DE ALTERNANCIA ======= */
        .register-link, .login-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.95em;
        }

        .register-link:hover, .login-link:hover {
            text-decoration: underline;
        }

        /* ======= BOTONES DE FORMULARIO ======= */
        .register-form button, .login-section button {
            width: 100%;
            padding: 12px;
            font-size: 1.1em;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .register-form button:hover, .login-section button:hover {
            background-color: #0056b3;
        }

        /* ======= MENSAJES ======= */
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 700px) {
            .register-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="Logo JAKAKE">
        </div>
        <h1>JAKAKE</h1>
        <p class="subtitle">¡Todo lo que necesitas para que tu proyecto se haga realidad!</p>
    </header>

    <div class="buttons">
        <button>Productos ✏️</button>
        <button>Carrito de compra 📦</button>
        <button>Finalizar pedido 📝</button>
    </div>

    <!-- Sección Login -->
    <section class="login-section <?php echo (!isset($_GET['action']) || $_GET['action'] != 'register') ? 'show' : ''; ?>">
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
        <a href="?action=register" class="register-link">¿Aún no tienes cuenta? Regístrate</a>
    </section>

    <!-- Sección Registro -->
    <section class="register-section <?php echo (isset($_GET['action']) && $_GET['action'] == 'register') ? 'show' : ''; ?>">
        <h2>Registrarse</h2>
        <form class="register-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="nombres">Nombres:</label>
                <input type="text" id="nombres" name="nombres" placeholder="Ingrese sus nombres" required>
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" placeholder="Ingrese sus apellidos" required>
            </div>

            <div class="form-group">
                <label for="cedula">Cédula:</label>
                <input type="text" id="cedula" name="cedula" placeholder="Ejemplo: 123456789" required>
            </div>

            <div class="form-group">
                <label for="fecha">Fecha de nacimiento:</label>
                <input type="date" id="fecha" name="fecha" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" placeholder="Ejemplo: 3001234567" required>
            </div>

            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" placeholder="Ejemplo: Calle 123 #45-67" required>
            </div>

            <div class="form-group full-width">
                <label for="correo">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" placeholder="correo@ejemplo.com" required>
            </div>

            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" placeholder="Crea una contraseña" required>
            </div>

            <div class="form-group">
                <label for="confirmar">Confirmar contraseña:</label>
                <input type="password" id="confirmar" name="confirmar" placeholder="Repite la contraseña" required>
            </div>

            <div class="data-terms">
                <input type="checkbox" id="acepto" name="acepto" required>
                <label for="acepto">
                    Acepto el tratamiento de mis datos personales según la <a href="#">Política de Privacidad</a>.
                </label>
            </div>

            <button type="submit">Registrarse</button>
        </form>
        <a href="index.php" class="login-link">¿Ya tienes cuenta? Inicia sesión</a>
    </section>

    <?php
    // Procesamiento de formularios
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Detectar si es login (tiene 'codigo') o registro (tiene 'nombres')
        if (isset($_POST['codigo'])) {
            // Procesar login
            $codigo = htmlspecialchars(trim($_POST['codigo']));
            $password = htmlspecialchars(trim($_POST['password']));
            
            // Validaciones básicas (en producción, verifica contra DB)
            if (empty($codigo) || empty($password)) {
                echo '<div class="message error">Todos los campos son obligatorios.</div>';
            } else {
                // Ejemplo: Simular verificación (reemplaza con lógica real de DB)
                // if (verificar_usuario($codigo, $password)) { ... }
                echo '<div class="message success">Login exitoso. ¡Bienvenido, ' . $codigo . '!</div>';
            }
        } elseif (isset($_POST['nombres'])) {
            // Procesar registro
            $nombres = htmlspecialchars(trim($_POST['nombres']));
            $apellidos = htmlspecialchars(trim($_POST['apellidos']));
            $cedula = htmlspecialchars(trim($_POST['cedula']));
            $fecha = htmlspecialchars(trim($_POST['fecha']));
            $telefono = htmlspecialchars(trim($_POST['telefono']));
            $direccion = htmlspecialchars(trim($_POST['direccion']));
            $correo = htmlspecialchars(trim($_POST['correo']));
            $contrasena = $_POST['contrasena'];
            $confirmar = $_POST['confirmar'];
            $acepto = isset($_POST['acepto']);

            // Validaciones
            $errors = [];
            if (empty($nombres) || empty($apellidos) || empty($cedula) || empty($fecha) || empty($telefono) || empty($direccion) || empty($correo) || empty($contrasena) || empty($confirmar)) {
                $errors[] = "Todos los campos son obligatorios.";
            }
            if ($contrasena !== $confirmar) {
                $errors[] = "Las contraseñas no coinciden.";
            }
            if (strlen($contrasena) < 6) {
                $errors[] = "La contraseña debe tener al menos 6 caracteres.";
            }
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "El correo electrónico no es válido.";
            }
            if (!$acepto) {
                $errors[] = "Debes aceptar la política de privacidad.";
            }

            if (empty($errors)) {
                // Aquí guarda en DB (ejemplo: hash y insert)
                // $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);
                // insert_into_db($nombres, ...);
                echo '<div class="message success">Registro exitoso. ¡Bienvenido a JAKAKE!</div>';
            } else {
                echo '<div class="message error">' . implode('<br>', $errors) . '</div>';
            }
        }
    }
    ?>

    <script>
        // JavaScript opcional para alternar vistas sin recargar (mejora UX)
        function toggleSections() {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action');
            const loginSection = document.querySelector('.login-section');
            const registerSection = document.querySelector('.register-section');

            if (action === 'register') {
                loginSection.classList.remove('show');
                registerSection.classList.add('show');
            } else {
                registerSection.classList.remove('show');
                loginSection.classList.add('show');
            }
        }
        window.onload = toggleSections;
    </script>
</body>
</html>
