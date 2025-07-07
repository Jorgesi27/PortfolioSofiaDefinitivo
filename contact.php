<?php 
include 'includes/config.php';

// Obtener datos de contacto de la base de datos
try {
    $stmt = $conn->query("SELECT * FROM contacts ORDER BY id ASC");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error loading contacts: " . $e->getMessage());
}

include 'includes/header.php'; 
?>

<section class="contact-section">
    <h1>Contact</h1>
    
    <div class="contact-container">
        <?php foreach($contacts as $contact): ?>
            <div class="contact-item">
                <h3><?php echo htmlspecialchars($contact['type']); ?></h3>
                <p><?php echo htmlspecialchars($contact['value']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>