<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sofía Portfolio</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/menu.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header fijo en todas las páginas -->
    <header>
        <a href="index.php" class="logo">Sofia Rodrigues</a>
    </header>

    <!-- Menú derecho -->
    <nav class="right-nav">
        <ul>
            <?php 
            $current_page = basename($_SERVER['PHP_SELF']);
            ?>
            
            <li><a href="about.php" <?php echo ($current_page == 'about.php') ? 'class="current-page"' : ''; ?>>About</a></li>
            <li><a href="contact.php" <?php echo ($current_page == 'contact.php') ? 'class="current-page"' : ''; ?>>Contact</a></li>
            <li><a href="archive.php" <?php echo ($current_page == 'archive.php') ? 'class="current-page"' : ''; ?>>Work Archive</a></li>
            
            <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <li><a href="admin/dashboard.php" <?php echo ($current_page == 'dashboard.php') ? 'class="current-page"' : ''; ?>>Admin</a></li>
                <li><a href="admin/logout.php">Logout</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    
    <main>