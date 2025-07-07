<?php
include '../../includes/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['token']) || $_GET['token'] !== $_SESSION['token_dashboard']) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php");
    exit;
}

$contact_id = (int)$_GET['id'];

// Obtener datos del contacto
try {
    $stmt = $conn->prepare("SELECT * FROM contacts WHERE id = ?");
    $stmt->execute([$contact_id]);
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$contact) {
        header("Location: ../manage_contacts.php");  // Cambiado a manage_contacts
        exit;
    }
} catch(PDOException $e) {
    die("Error loading contact: " . $e->getMessage());
}

// Procesar formulario
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = clean_input($_POST['type']);
    $value = clean_input($_POST['value']);
    
    try {
        $stmt = $conn->prepare("UPDATE contacts SET type = ?, value = ? WHERE id = ?");
        $stmt->execute([$type, $value, $contact_id]);
        
        $_SESSION['success_message'] = "Contact updated successfully!";
        header("Location: ../manage_contacts.php");  // Cambiado a manage_contacts
        exit;
    } catch(PDOException $e) {
        $error = "Error updating contact: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Edit Contact</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">  <!-- Ruta corregida -->
</head>
<body>
    <div class="admin-container">
        <h1>Edit Contact</h1>
        
        <?php if(isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="type">Type:</label>
                <input type="text" id="type" name="type" value="<?php echo htmlspecialchars($contact['type']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="value">Value:</label>
                <input type="text" id="value" name="value" value="<?php echo htmlspecialchars($contact['value']); ?>" required>
            </div>
            
            <div class="form-group" style="margin-top: 40px;">
                <button type="submit" class="btn">Save Changes</button>
                <a href="../dashboard.php" class="btn cancel">Back to Dashboard</a>
            </div>
        </form>
    </div>
</body>
</html>