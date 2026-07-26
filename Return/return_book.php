<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

// Start session if not already started to support flash messages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'create') {
    $name  = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name !== '') {
        $stmt = $conn->prepare("INSERT INTO members (name, phone, email) VALUES (:name, :phone, :email)");
        $stmt->execute([
            ':name'  => $name,
            ':phone' => $phone,
            ':email' => $email
        ]);

        $_SESSION['flash_message'] = 'Member added successfully.';
        $_SESSION['flash_type']    = 'success';
    }
} elseif ($action === 'update') {
    $id    = (int)($_POST['id'] ?? 0);
    $name  = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($id > 0 && $name !== '') {
        $stmt = $conn->prepare("UPDATE members SET name = :name, phone = :phone, email = :email WHERE id = :id");
        $stmt->execute([
            ':name'  => $name,
            ':phone' => $phone,
            ':email' => $email,
            ':id'    => $id
        ]);

        $_SESSION['flash_message'] = 'Member updated successfully.';
        $_SESSION['flash_type']    = 'success';
    }
}

// Redirect back to members index page
header("Location: index.php");
exit;