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

// Obtener datos actuales
try {
    $stmt = $conn->query("SELECT * FROM about LIMIT 1");
    $about = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error loading about data: " . $e->getMessage());
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $about_text = clean_input($_POST['about_text']);
    
    $about_photo = $about['about_photo'] ?? '';
    if(isset($_FILES['about_photo']) && $_FILES['about_photo']['error'] == 0) {
        $target_dir = "../../assets/img/";
        $target_file = $target_dir . basename($_FILES['about_photo']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        if(getimagesize($_FILES['about_photo']['tmp_name'])) {
            // Eliminar foto anterior si existe
            if(!empty($about_photo) && file_exists("../../" . $about_photo)) {
                unlink("../../" . $about_photo);
            }
            
            $new_filename = 'about-photo.' . $imageFileType;
            if(move_uploaded_file($_FILES['about_photo']['tmp_name'], $target_dir . $new_filename)) {
                $about_photo = "assets/img/" . $new_filename;
            }
        }
    }
    
    try {
        if($about) {
            $stmt = $conn->prepare("UPDATE about SET about_text = ?, about_photo = ?");
            $stmt->execute([$about_text, $about_photo]);
        } else {
            $stmt = $conn->prepare("INSERT INTO about (about_text, about_photo) VALUES (?, ?)");
            $stmt->execute([$about_text, $about_photo]);
        }
        
        $_SESSION['success_message'] = "About section updated successfully!";
        header("Location: ../dashboard.php");
        exit;
    } catch(PDOException $e) {
        $error = "Error updating about: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Edit About Section</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        textarea {
            width: 100%;
            min-height: 200px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 16px;
        }
        
        .image-preview {
            max-width: 200px;
            margin-top: 15px;
            border-radius: 4px;
        }
        
        .btn {
            display: inline-block;
            background: #4a6fa5;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #3a5a8a;
        }
        
        .cancel {
            background: #6c757d;
            margin-left: 10px;
        }
        
        .cancel:hover {
            background: #5a6268;
        }
        
        .actions {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="form-container">
            <h1>Edit About Section</h1>
            
            <?php if(isset($error)): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="about_text">About Text:</label>
                    <textarea id="about_text" name="about_text" required><?php echo isset($about['about_text']) ? htmlspecialchars($about['about_text']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="about_photo">About Photo:</label>
                    <?php if(isset($about['about_photo']) && !empty($about['about_photo'])): ?>
                        <img src="../../<?php echo htmlspecialchars($about['about_photo']); ?>" class="image-preview">
                    <?php endif; ?>
                    <input type="file" id="about_photo" name="about_photo">
                </div>
                
                <div class="actions">
                    <button type="submit" class="btn">Save Changes</button>
                    <a href="../dashboard.php" class="btn cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>