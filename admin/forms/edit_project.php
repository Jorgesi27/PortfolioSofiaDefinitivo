<?php
include '../../includes/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true
    || !isset($_SESSION['token_dashboard'])
    || !isset($_GET['token'])
    || $_GET['token'] !== $_SESSION['token_dashboard']
) {
    header("Location: ../../login.php");
    exit;
}

$project_id = (int)$_GET['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$project_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$project) {
        header("Location: ../manage_projects.php");
        exit;
    }
    
    $stmtImages = $conn->prepare("SELECT * FROM project_images WHERE project_id = ? ORDER BY display_order ASC");
    $stmtImages->execute([$project_id]);
    $images = $stmtImages->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error loading project: " . $e->getMessage());
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = clean_input($_POST['title']);
    $year = clean_input($_POST['year']);
    $description = clean_input($_POST['description']);
    $slug = create_slug($title);
    $current_year = date('Y');
    
    // Validación del año
    if ($year > $current_year) {
        $error = "The year cannot be greater than the current year ($current_year)";
    } else {
        $main_image_url = $project['main_image_url'];
        if(isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
            $target_dir = "../../assets/img/projects/";
            $target_file = $target_dir . basename($_FILES['main_image']['name']);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            
            if(getimagesize($_FILES['main_image']['tmp_name'])) {
                if(!empty($main_image_url) && file_exists("../../" . $main_image_url)) {
                    unlink("../../" . $main_image_url);
                }
                
                $new_filename = $slug . '-main.' . $imageFileType;
                if(move_uploaded_file($_FILES['main_image']['tmp_name'], $target_dir . $new_filename)) {
                    $main_image_url = "assets/img/projects/" . $new_filename;
                }
            }
        }
        
        try {
            $stmt = $conn->prepare("UPDATE projects SET title = ?, year = ?, description = ?, slug = ?, main_image_url = ? WHERE id = ?");
            $stmt->execute([$title, $year, $description, $slug, $main_image_url, $project_id]);
            
            if(!empty($_FILES['additional_images']['name'][0])) {
                $stmtOrder = $conn->prepare("SELECT MAX(display_order) as max_order FROM project_images WHERE project_id = ?");
                $stmtOrder->execute([$project_id]);
                $result = $stmtOrder->fetch(PDO::FETCH_ASSOC);
                $current_order = $result['max_order'] ?: 0;
                
                foreach($_FILES['additional_images']['tmp_name'] as $key => $tmp_name) {
                    if($_FILES['additional_images']['error'][$key] == 0) {
                        $target_dir = "../../assets/img/projects/";
                        $original_name = basename($_FILES['additional_images']['name'][$key]);
                        $imageFileType = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                        $new_filename = $slug . '-' . ($current_order + $key + 1) . '.' . $imageFileType;
                        
                        if(move_uploaded_file($tmp_name, $target_dir . $new_filename)) {
                            $stmt = $conn->prepare("INSERT INTO project_images (project_id, image_url, display_order) VALUES (?, ?, ?)");
                            $stmt->execute([$project_id, "assets/img/projects/" . $new_filename, $current_order + $key + 1]);
                        }
                    }
                }
            }
            
            $_SESSION['success_message'] = "Project updated successfully!";
            header("Location: ../manage_projects.php");
            exit;
        } catch(PDOException $e) {
            $error = "Error updating project: " . $e->getMessage();
        }
    }
}

function create_slug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', "-", $slug);
    return $slug;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Edit Project</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        input[type="text"], textarea, input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        textarea {
            min-height: 200px;
        }
        .image-preview {
            max-width: 200px;
            margin-top: 15px;
            border-radius: 4px;
        }
        .existing-images {
            margin: 30px 0;
        }
        .image-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 4px;
        }
        .image-item img {
            max-width: 150px;
            margin-right: 20px;
            border-radius: 4px;
        }
        .preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }
        .btn {
            padding: 12px 25px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="form-container">
            <h1>Edit Project</h1>
            
            <?php if(isset($error)): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($project['title']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="year">Year:</label>
                    <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($project['year']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($project['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="main_image">Main Image:</label>
                    <?php if(!empty($project['main_image_url'])): ?>
                        <img src="../../<?php echo htmlspecialchars($project['main_image_url']); ?>" class="image-preview">
                    <?php endif; ?>
                    <input type="file" id="main_image" name="main_image" accept="image/*">
                    <small>Leave empty to keep current image</small>
                </div>
                
                <div class="existing-images">
                    <h3>Additional Images</h3>
                    <?php foreach($images as $image): ?>
                        <div class="image-item">
                            <img src="../../<?php echo htmlspecialchars($image['image_url']); ?>">
                            <div>
                                <a href="delete_image.php?id=<?php echo $image['id']; ?>&project_id=<?php echo $project_id; ?>&token=<?php echo $_SESSION['token_dashboard']; ?>" class="btn delete-btn" onclick="return confirm('Are you sure?')">Delete</a>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="form-group">
                    <label for="additional_images">Add More Images:</label>
                    <input type="file" id="additional_images" name="additional_images[]" multiple accept="image/*">
                    <div id="additional_previews" class="preview-container"></div>
                </div>
                
                <button type="submit" class="btn">Update Project</button>
                <a href="../manage_projects.php" class="btn cancel">Cancel</a>
            </form>
        </div>
    </div>

    <script>
        // Preview para imágenes adicionales
        document.getElementById('additional_images').addEventListener('change', function(e) {
            const container = document.getElementById('additional_previews');
            container.innerHTML = '';
            
            if(this.files) {
                Array.from(this.files).forEach(file => {
                    if(file.type.match('image.*')) {
                        const reader = new FileReader();
                        const div = document.createElement('div');
                        const img = document.createElement('img');
                        img.className = 'image-preview';
                        img.style.display = 'block';
                        
                        reader.onload = function(e) {
                            img.src = e.target.result;
                            div.appendChild(img);
                            container.appendChild(div);
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
</body>
</html>