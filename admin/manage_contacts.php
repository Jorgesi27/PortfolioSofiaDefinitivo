<?php
include '../includes/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['token_dashboard']) || !isset($_GET['token']) || $_GET['token'] !== $_SESSION['token_dashboard']) {
    // Token inválido o inexistente: denegar acceso o redirigir
    header("Location: login.php");
    exit;
}

// Generar token y guardarlo en sesión si no existe
if (empty($_SESSION['token_dashboard'])) {
    $_SESSION['token_dashboard'] = generate_token($_SESSION['user_id'], SECRET_KEY);
}

$token_dashboard = $_SESSION['token_dashboard'];

// Obtener todos los contactos
try {
    $stmt = $conn->query("SELECT * FROM contacts ORDER BY id ASC");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error loading contacts: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Manage Contacts</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        .contacts-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .contacts-table th, .contacts-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .contacts-table th {
            background-color: #f4f4f4;
        }
        .action-links a {
            margin-right: 10px;
            text-decoration: none;
        }
        .add-new-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 15px;
            background: #4CAF50;
            color: white;
            border-radius: 4px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Manage Contacts</h1>
        
        <a href="forms/add_contact.php" class="add-new-btn">Add New Contact</a>
        
        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="alert success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="alert error"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>
        
        <table class="contacts-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($contacts as $contact): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($contact['type']); ?></td>
                        <td><?php echo htmlspecialchars($contact['value']); ?></td>
                        <td class="action-links">
                           <a href="forms/edit_contact.php?id=<?= $project['id'] ?>&token=<?= $token_dashboard ?>">Edit</a>
                            <a href="forms/delete_contact.php?id=<?php echo $contact['id']; ?>" class="delete-btn" 
                               onclick="return confirm('Are you sure you want to delete this contact?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>