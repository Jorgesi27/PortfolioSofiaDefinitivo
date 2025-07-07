<?php
include '../../includes/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true ||
    !isset($_SESSION['token_dashboard']) ||
    !isset($_GET['token']) ||
    $_GET['token'] !== $_SESSION['token_dashboard']
) {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_GET['id']) || !isset($_GET['project_id'])) {
    header("Location: ../manage_projects.php");
    exit;
}

$image_id = (int)$_GET['id'];
$project_id = (int)$_GET['project_id'];

try {
    // Obtener información de la imagen para eliminarla del servidor
    $stmt = $conn->prepare("SELECT image_url FROM project_images WHERE id = ?");
    $stmt->execute([$image_id]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($image && !empty($image['image_url']) && file_exists("../../" . $image['image_url'])) {
        unlink("../../" . $image['image_url']);
    }
    
    // Eliminar registro de la base de datos
    $stmt = $conn->prepare("DELETE FROM project_images WHERE id = ?");
    $stmt->execute([$image_id]);
    
    $_SESSION['success_message'] = "Image deleted successfully!";
} catch(PDOException $e) {
    $_SESSION['error_message'] = "Error deleting image: " . $e->getMessage();
}

header("Location: edit_project.php?id=" . $project_id . "&token=" . $_SESSION['token_dashboard']);
exit;
?>