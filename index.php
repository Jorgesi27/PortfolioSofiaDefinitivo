<?php include 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sofia Rodrigues</title>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      font-family: 'Space Grotesk', sans-serif;
      background: #ffffff;
      color: #222;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 40px;
      border-bottom: 1px solid #eee;
    }

    .logo {
      font-size: 1.5rem;
      font-weight: 600;
    }

    nav a {
      margin-left: 20px;
      text-decoration: none;
      color: #444;
      font-weight: 500;
    }

    .hero {
      text-align: center;
      padding: 80px 20px 40px;
    }

    .hero h1 {
      font-size: 3rem;
      margin-bottom: 10px;
    }

    .projects {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
      padding: 40px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .project {
      position: relative;
      overflow: hidden;
      border-radius: 12px;
      cursor: pointer;
      box-shadow: 0 6px 20px rgba(0,0,0,0.05);
      transition: transform 0.3s;
    }

    .project:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .project img {
      width: 100%;
      height: 250px;
      object-fit: cover;
      display: block;
      transition: transform 0.4s;
    }

    .project:hover img {
      transform: scale(1.05);
    }

    .project-title {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(255, 255, 255, 0.9);
      padding: 15px;
      font-weight: 600;
    }

    .project-year {
      display: block;
      font-size: 0.9rem;
      color: #666;
      margin-top: 5px;
    }

    .section-title {
      text-align: center;
      margin: 60px 0 30px;
      font-size: 2rem;
    }

    @media (max-width: 600px) {
      .hero h1 {
        font-size: 2rem;
      }

      nav a {
        margin-left: 10px;
        font-size: 0.95rem;
      }
      
      .projects {
        grid-template-columns: 1fr;
        padding: 20px;
      }
    }
  </style>
</head>
<body>

  <div class="hero">
    <h1>Portfolio</h1>
    <p>Welcome to my Portfolio</p>
  </div>

  <?php
  // Obtener los 3 proyectos más recientes
  try {
      $stmt = $conn->query("SELECT * FROM projects ORDER BY year DESC, id DESC LIMIT 3");
      $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
      
      if(count($projects) > 0): ?>
          <h2 class="section-title">Latest Projects</h2>
          <div class="projects">
              <?php foreach($projects as $project): ?>
                  <a class="project" href="project.php?slug=<?php echo htmlspecialchars($project['slug']); ?>">
                      <?php if(!empty($project['main_image_url'])): ?>
                          <img src="<?php echo htmlspecialchars($project['main_image_url']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                      <?php else: ?>
                          <img src="img/placeholder.jpg" alt="Project placeholder">
                      <?php endif; ?>
                      <div class="project-title">
                          <?php echo htmlspecialchars($project['title']); ?>
                          <span class="project-year"><?php echo htmlspecialchars($project['year']); ?></span>
                      </div>
                  </a>
              <?php endforeach; ?>
          </div>
      <?php endif;
  } catch(PDOException $e) {
      echo "<p>Error loading projects: " . $e->getMessage() . "</p>";
  }
  ?>

</body>
</html>

<?php include 'includes/footer.php'; ?>