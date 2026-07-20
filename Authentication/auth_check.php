<?php
// Include this at the top of any protected page to require login
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: ' . (strpos($_SERVER['SCRIPT_NAME'], '/Authentication/') !== false ? 'login.php' : '../Authentication/login.php'));
    exit;
}
