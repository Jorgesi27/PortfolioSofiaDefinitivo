<?php
session_start();

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'portfolio');

// Conexión a la base de datos
try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES 'utf8mb4'");
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Función para limpiar datos de entrada
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generate_token($id, $secret_key) {
    return hash_hmac('sha256', $id, $secret_key);
}
define('SECRET_KEY', 'mi_clave_super_secreta_y_larga');
?>