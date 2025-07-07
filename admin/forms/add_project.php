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
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token. Please try again.";
    } else {
        $title = clean_input($_POST['title']);
        $year = clean_input($_POST['year']);
        $description = clean_input($_POST['description']);
        $slug = create_slug($title);

        $current_year = date('Y');
        if ($year > $current_year) {
            $error = "The year cannot be greater than the current year ($current_year)";
        } else {
            $main_image_url = '';
            if(isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
                $target_dir = "../../assets/img/projects/";
                $target_file = $target_dir . basename($_FILES['main_image']['name']);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                if(getimagesize($_FILES['main_image']['tmp_name'])) {
                    $new_filename = $slug . '-main.' . $imageFileType;
                    if(move_uploaded_file($_FILES['main_image']['tmp_name'], $target_dir . $new_filename)) {
                        $main_image_url = "assets/img/projects/" . $new_filename;
                    }
                }
            }

            try {
                $stmt = $conn->prepare("INSERT INTO projects (title, year, description, slug, main_image_url) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $year, $description, $slug, $main_image_url]);

                $project_id = $conn->lastInsertId();

                if(!empty($_FILES['additional_images']['name'][0])) {
                    foreach($_FILES['additional_images']['tmp_name'] as $key => $tmp_name) {
                        if($_FILES['additional_images']['error'][$key] == 0) {
                            $target_dir = "../../assets/img/projects/";
                            $original_name = basename($_FILES['additional_images']['name'][$key]);
                            $imageFileType = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                            $new_filename = $slug . '-' . ($key+1) . '.' . $imageFileType;

                            if(move_uploaded_file($tmp_name, $target_dir . $new_filename)) {
                                $stmt = $conn->prepare("INSERT INTO project_images (project_id, image_url, display_order) VALUES (?, ?, ?)");
                                $stmt->execute([$project_id, "assets/img/projects/" . $new_filename, $key+1]);
                            }
                        }
                    }
                }

                $_SESSION['success_message'] = "Project added successfully!";
                header("Location: ../manage_projects.php");
                exit;
            } catch(PDOException $e) {
                $error = "Error adding project: " . $e->getMessage();
            }
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
    <title>Add New Project</title>
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
            display: none;
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
            <h1>Add New Project</h1>
            
            <?php if(isset($error)): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="year">Year:</label>
                    <input type="number" id="year" name="year" min="1900" max="<?php echo date('Y'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="main_image">Main Image:</label>
                    <input type="file" id="main_image" name="main_image" accept="image/*" required>
                    <img id="main_image_preview" class="image-preview">
                </div>
                
                <div class="form-group">
                    <label for="additional_images">Additional Images:</label>
                    <input type="file" id="additional_images" name="additional_images[]" multiple accept="image/*">
                    <div id="additional_previews" class="preview-container"></div>
                </div>
                
                <button type="submit" class="btn">Save Project</button>
                <a href="../manage_projects.php" class="btn cancel">Cancel</a>
            </form>
        </div>
    </div>

    <script>
        // Preview para imagen principal
        document.getElementById('main_image').addEventListener('change', function(e) {
            const preview = document.getElementById('main_image_preview');
            if(this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

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