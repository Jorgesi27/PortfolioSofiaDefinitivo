<?php
include '../../includes/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['token_dashboard']) || !isset($_GET['token']) || $_GET['token'] !== $_SESSION['token_dashboard']) {
    // Token inválido o inexistente: denegar acceso o redirigir
    header("Location: login.php");
    exit;
}

// Generar token CSRF para el formulario si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = clean_input($_POST['type']);
    $value = clean_input($_POST['value']);
    
    try {
        $stmt = $conn->prepare("INSERT INTO contacts (type, value) VALUES (?, ?)");
        $stmt->execute([$type, $value]);
        
        $_SESSION['success_message'] = "Contact added successfully!";
        header("Location: ../manage_contacts.php"); // Cambiado esta línea
        exit;
    } catch(PDOException $e) {
        $error = "Error adding contact: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Add New Contact</title>
    <link rel="stylesheet" href="../../assets/css/admin.css"> <!-- Corregida la ruta del CSS -->
    <style>
        .admin-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            display: inline-block;
            padding: 8px 15px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            margin-right: 10px;
        }
        .btn.cancel {
            background: #f44336;
        }
        .alert.error {
            color: #721c24;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Add New Contact</h1>
        
        <?php if(isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="type">Type:</label>
                <input type="text" id="type" name="type" required>
            </div>
            
            <div class="form-group">
                <label for="value">Value:</label>
                <input type="text" id="value" name="value" required>
            </div>
            
            <button type="submit" class="btn">Add Contact</button>
            <a href="../manage_contacts.php" class="btn cancel">Cancel</a> <!-- Corregido este enlace -->
        </form>
    </div>
</body>
</html>