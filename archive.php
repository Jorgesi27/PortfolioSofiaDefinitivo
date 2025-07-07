<?php include 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>

<section class="archive-section">
    <h2>Project Archive</h2>
    <div class="projects-grid">
        <?php
        try {
            $stmt = $conn->query("SELECT * FROM projects ORDER BY year DESC, title ASC");
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if(count($projects) > 0) {
                foreach($projects as $project) {
                    echo '<div class="project-card">';
                    echo '<a href="project.php?slug='.htmlspecialchars($project['slug']).'" class="project-link">';
                    if(!empty($project['main_image_url'])) {
                        echo '<img src="'.htmlspecialchars($project['main_image_url']).'" alt="'.htmlspecialchars($project['title']).'">';
                    }
                    echo '<div class="project-info">';
                    echo '<h3>'.htmlspecialchars($project['title']).'</h3>';
                    echo '<span class="year">'.htmlspecialchars($project['year']).'</span>';
                    echo '</div>';
                    echo '</a>';
                    echo '</div>';
                }
            } else {
                echo '<p>No projects available.</p>';
            }
        } catch(PDOException $e) {
            echo "Error loading projects: " . $e->getMessage();
        }
        ?>
    </div>
</section>

<style>
    .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }
    
    .project-card {
        border: 1px solid #eee;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.3s;
    }
    
    .project-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .project-link {
        text-decoration: none;
        color: inherit;
    }
    
    .project-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    
    .project-info {
        padding: 15px;
    }
    
    .project-info h3 {
        margin: 0 0 5px 0;
        color: #222;
    }
    
    .year {
        color: #666;
        font-size: 0.9rem;
    }
</style>

<?php include 'includes/footer.php'; ?>