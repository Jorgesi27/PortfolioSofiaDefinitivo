<?php 
include '../includes/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['token_dashboard'])) {
    $_SESSION['token_dashboard'] = bin2hex(random_bytes(32));
}
$token_dashboard = $_SESSION['token_dashboard'];

if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }
        
        .admin-sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }
        
        .admin-section {
            background: #f9f9f9;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #eee;
        }
        
        .admin-section h2 {
            margin-top: 0;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        
        .btn {
            display: inline-block;
            background: #4a6fa5;
            color: white;
            padding: 12px 20px;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 10px;
            margin-bottom: 10px;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #3a5a8a;
        }
        
        .logout {
            background: #d9534f;
        }
        
        .logout:hover {
            background: #c9302c;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Admin Dashboard</h1>
        
        <div class="admin-sections">
            <section class="admin-section">
                <h2>About Section</h2>
                <a href="forms/edit_about.php?token=<?php echo $token_dashboard; ?>" class="btn">Edit About</a>
            </section>
            
            <section class="admin-section">
                <h2>Contacts Management</h2>
               <a href="manage_projects.php?token=<?php echo $token_dashboard; ?>" class="btn">Manage Contacts</a>
            </section>
            
            <section class="admin-section">
                <h2>Projects</h2>
                <a href="manage_projects.php?token=<?php echo $token_dashboard; ?>" class="btn">Manage Projects</a>
                <a href="/AppWebPortfolioDEF/admin/forms/add_project.php?token=<?php echo $token_dashboard; ?>" class="btn">Add New Project</a>
            </section>
        </div>
        
        <div style="margin-top: 40px;">
            <a href="../index.php" class="btn">View Site</a>
            <a href="logout.php" class="btn logout">Logout</a>
        </div>
    </div>
</body>
</html>