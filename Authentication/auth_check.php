<?php
// 1. Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Safely include db.php regardless of which subfolder calls this script
require_once __DIR__ . '/../Database/db.php';

// 3. Enforce auth check and redirect if unauthenticated
if (empty($_SESSION['user_id'])) {
    $inAuthFolder = strpos($_SERVER['SCRIPT_NAME'], '/Authentication/') !== false;
    $targetPage   = $inAuthFolder ? 'login.php' : '../Authentication/login.php';

    header('Location: ' . $targetPage);
    exit;
}