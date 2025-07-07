<?php
include '../includes/config.php';

// Destruir la sesión
$_SESSION = array();
session_destroy();

// Redirigir al login
header("Location: login.php");
exit;
?>