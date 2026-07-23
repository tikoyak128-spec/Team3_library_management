<?php
require_once __DIR__ . '/../Authentication/auth_check.php';
require_once __DIR__ . '/category_controller.php';
include './Database/db.php';
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) deleteCategory($conn, $id);
header('Location: index.php');
exit;
