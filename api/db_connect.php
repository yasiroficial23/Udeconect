<?php
// db_connect.php
// Establish database connection using PDO

// Database configuration
$host = 'localhost';
$dbname = 'vitrina_talento';
$username = 'root'; // Default XAMPP MySQL username
$password = '';     // Default XAMPP MySQL password (empty unless changed)
$port = 3306;       // Default MySQL port

try {
    // Create PDO instance
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set PDO attributes for error handling and prepared statements
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Use real prepared statements
} catch (PDOException $e) {
    // Log error or display a generic message (avoid exposing details in production)
    error_log("Database connection failed: " . $e->getMessage());
    die("Error de conexión. Por favor, contacte al administrador.");
}

// Make $pdo available globally (optional, depending on your structure)
global $pdo;
?>