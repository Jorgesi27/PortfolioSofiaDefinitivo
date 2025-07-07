<?php 
include 'includes/config.php';

if(!isset($_GET['slug'])) {
    header("Location: archive.php");
    exit;
}

$slug = clean_input($_GET['slug']);

try {
    $stmt = $conn->prepare("SELECT * FROM projects WHERE slug = ?");
    $stmt->execute([$slug]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$project) {
        header("Location: archive.php");
        exit;
    }
    
    $stmtImages = $conn->prepare("SELECT * FROM project_images WHERE project_id = ? ORDER BY display_order ASC");
    $stmtImages->execute([$project['id']]);
    $images = $stmtImages->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Error loading project: " . $e->getMessage());
}

include 'includes/header.php'; 
?>

<section class="project-detail">
    <h1><?php echo htmlspecialchars($project['title']); ?></h1>
    <span class="project-meta"><?php echo htmlspecialchars($project['year']); ?></span>
    
    <div class="project-description">
        <?php echo nl2br(htmlspecialchars($project['description'])); ?>
    </div>
    
    <div class="project-gallery">
        <?php if(!empty($project['main_image_url'])): ?>
            <div class="main-image">
                <img src="<?php echo htmlspecialchars($project['main_image_url']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
            </div>
        <?php endif; ?>
        
        <?php foreach($images as $image): ?>
            <div class="project-image">
                <img src="<?php echo htmlspecialchars($image['image_url']); ?>" alt="<?php echo htmlspecialchars($project['title'].' - Image '.$image['display_order']); ?>">
            </div>
        <?php endforeach; ?>
    </div>
</section>

<style>
    .project-detail {
        max-width: 1000px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    
    .project-meta {
        display: block;
        color: #666;
        margin-bottom: 30px;
        font-size: 1.1rem;
    }
    
    .project-description {
        line-height: 1.8;
        margin-bottom: 40px;
        font-size: 1.1rem;
        color: #444;
    }
    
    .project-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
    }
    
    .main-image, .project-image {
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .main-image img, .project-image img {
        width: 100%;
        height: auto;
        display: block;
        transition: transform 0.3s;
    }
    
    .main-image:hover img, .project-image:hover img {
        transform: scale(1.03);
    }
</style>

<?php include 'includes/footer.php'; ?>