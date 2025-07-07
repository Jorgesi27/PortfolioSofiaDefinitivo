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

try {
    $stmt = $conn->query("SELECT * FROM projects ORDER BY year DESC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error loading projects: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Manage Projects</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .projects-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .projects-table th, .projects-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        .projects-table th {
            background-color: #f4f4f4;
        }
        
        .projects-table img {
            max-width: 100px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Manage Projects</h1>
        
       <a href="forms/add_project.php?token=<?php echo $token_dashboard; ?>" class="btn">Add New Project</a>
        
        <table class="projects-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Year</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($projects as $project): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($project['title']); ?></td>
                        <td><?php echo htmlspecialchars($project['year']); ?></td>
                        <td>
                            <?php if(!empty($project['main_image_url'])): ?>
                                <img src="../<?php echo htmlspecialchars($project['main_image_url']); ?>" alt="Thumbnail">
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="forms/edit_project.php?id=<?= $project['id'] ?>&token=<?= $token_dashboard ?>">Edit</a>
                            <a href="forms/delete_project.php?id=<?= $project['id'] ?>&token=<?= generate_token($project['id'], SECRET_KEY) ?>" class="btn delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
        
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>