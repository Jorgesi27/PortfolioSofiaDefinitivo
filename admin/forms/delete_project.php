<?php
include '../../includes/config.php';

if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../login.php");
    exit;
}

// Validar que id y token existan y sean correctos
if(!isset($_GET['id'], $_GET['token'])) {
    header("Location: ../manage_projects.php");
    exit;
}

$project_id = (int)$_GET['id'];
$token = $_GET['token'];

// Validar token
if($token !== generate_token($project_id, SECRET_KEY)) {
    $_SESSION['error_message'] = "Acceso no autorizado.";
    header("Location: ../manage_projects.php");
    exit;
}

try {
    // Obtener información del proyecto para eliminar imágenes
    $stmt = $conn->prepare("SELECT main_image_url FROM projects WHERE id = ?");
    $stmt->execute([$project_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Obtener imágenes adicionales
    $stmtImages = $conn->prepare("SELECT image_url FROM project_images WHERE project_id = ?");
    $stmtImages->execute([$project_id]);
    $images = $stmtImages->fetchAll(PDO::FETCH_ASSOC);
    
    // Eliminar imágenes del servidor
    if($project && !empty($project['main_image_url']) && file_exists("../../" . $project['main_image_url'])) {
        unlink("../../" . $project['main_image_url']);
    }
    
    foreach($images as $image) {
        if(!empty($image['image_url']) && file_exists("../../" . $image['image_url'])) {
            unlink("../../" . $image['image_url']);
        }
    }
    
    // Eliminar registros de la base de datos
    $conn->beginTransaction();
    
    $stmt = $conn->prepare("DELETE FROM project_images WHERE project_id = ?");
    $stmt->execute([$project_id]);
    
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$project_id]);
    
    $conn->commit();
    
    $_SESSION['success_message'] = "Project deleted successfully!";
} catch(PDOException $e) {
    $conn->rollBack();
    $_SESSION['error_message'] = "Error deleting project: " . $e->getMessage();
}

header("Location: ../manage_projects.php");
exit;
?>