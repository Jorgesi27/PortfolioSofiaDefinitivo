<?php
include '../../includes/config.php';

if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../login.php");
    exit;
}

// Validar que id y token existan y sean correctos
if(!isset($_GET['id'], $_GET['token'])) {
    header("Location: ../manage_contacts.php");
    exit;
}

$contact_id = (int)$_GET['id'];
$token = $_GET['token'];

// Validar token
if($token !== generate_token($contact_id, SECRET_KEY)) {
    $_SESSION['error_message'] = "Acceso no autorizado.";
    header("Location: ../manage_contacts.php");
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->execute([$contact_id]);
    
    $_SESSION['success_message'] = "Contact deleted successfully!";
} catch(PDOException $e) {
    $_SESSION['error_message'] = "Error deleting contact: " . $e->getMessage();
}

header("Location: ../manage_contacts.php"); // Cambiado de contacts.php a manage_contacts.php
exit;
?>