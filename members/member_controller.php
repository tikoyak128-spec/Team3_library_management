<?Php include '../includes/header.php'; ?>
<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name !== '') {
        $stmt = $conn->prepare("INSERT INTO members (name, phone, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $phone, $email);
        $stmt->execute();
        $_SESSION['flash_message'] = 'Member added successfully.';
        $_SESSION['flash_type'] = 'success';
    }
} elseif ($action === 'update') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($id > 0 && $name !== '') {
        $stmt = $conn->prepare("UPDATE members SET name = ?, phone = ?, email = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $phone, $email, $id);
        $stmt->execute();
        $_SESSION['flash_message'] = 'Member updated successfully.';
        $_SESSION['flash_type'] = 'success';
    }
}

header("Location: index.php");
exit;
