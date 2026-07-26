<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

// Check if an ID was passed in the URL
$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $sql = "DELETE FROM members WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        // Log or handle foreign key errors if member has active borrowings
        // error_log($e->getMessage());
    }
}

// Redirect back to the member list
header("Location: index.php");
exit;