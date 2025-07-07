<?php 
include 'includes/config.php';
include 'includes/header.php';

// Obtener datos del about
try {
    $stmt = $conn->query("SELECT * FROM about LIMIT 1");
    $about = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error loading about data: " . $e->getMessage());
}
?>

<section class="about-section">
    <?php if($about): ?>
        <div class="about-content">
            <div class="about-text">
                <h1>About Me</h1>
                <p><?php echo nl2br(htmlspecialchars($about['about_text'])); ?></p>
            </div>
            <?php if(!empty($about['about_photo'])): ?>
                <div class="about-photo">
                    <img src="<?php echo htmlspecialchars($about['about_photo']); ?>" alt="About Me Photo">
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p class="no-content">No about information available.</p>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>