<?php
require_once __DIR__ . '/../Authentication/auth_check.php';
require_once __DIR__ . '/book_controller.php';

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) deleteBook($pdo, $id);
header('Location: index.php');
exit;
