<?php

// Define Base URL without double slashes
define('BASE_URL', 'http://localhost/php%20+%20larevel/TEAM3-library_management/');

$servername = "localhost";
$username   = 'root';
$db_name    = 'library_management';
$password   = '';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$db_name;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // DO NOT echo anything here so AJAX / JSON responses stay clean!
} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}
?>
