<?php
define('BASE_PATH', dirname(__DIR__)); 
require_once BASE_PATH . '/Database/db.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $name = trim($_POST['name'] ?? '');
    if ($name !== '') {
        $stmt = $conn->prepare("INSERT INTO authors (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $_SESSION['flash_message'] = 'Author added successfully.';
        $_SESSION['flash_type'] = 'success';
    }
} elseif ($action === 'update') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    if ($id > 0 && $name !== '') {
        $stmt = $conn->prepare("UPDATE authors SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        $_SESSION['flash_message'] = 'Author updated successfully.';
        $_SESSION['flash_type'] = 'success';
    }
}

header("Location: index.php");
exit;
