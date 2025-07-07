<?php
include '../includes/config.php';

// Verificar si ya está logueado
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: dashboard.php");
    exit;
}

// Procesar el formulario de login
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);
    
    // Validar credenciales (DEBES CAMBIAR ESTO EN PRODUCCIÓN)
    $valid_username = 'admin';  // Cambia esto
    $valid_password = 'password';  // Cambia esto
    
    if($username === $valid_username && $password === $valid_password) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        
        // Redirigir al dashboard o a la página previa
        $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'dashboard.php';
        header("Location: " . $redirect);
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}

// Obtener URL de redirección para después del login
$redirect_url = isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'login.php') === false 
    ? $_SERVER['HTTP_REFERER'] 
    : 'dashboard.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        .alert.error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Admin Login</h1>
        
        <?php if(isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="" method="post">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect_url); ?>">
            
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Entrar</button>
        </form>
        <a href="../index.php" class="back-link">← Volver al Portfolio</a>
    </div>
</body>
</html>