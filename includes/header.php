<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '.');
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Library Management System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f4f6f9; }
.navbar-brand { font-weight: 700; }
.card { border: none; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?php echo BASE_URL; ?>/index.php">📚 Library Management</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/author/index.php">Authors</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/Books/index.php">Books</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/members/index.php">Members</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/Borrow/index.php">Borrow</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/Borrow/borrow_history.php">Borrow History</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/Return/index.php">Return</a></li>
        </ul>
    </div>
</div>
</nav>
<div class="container">
<?php
if (isset($_SESSION['flash_message'])) {
    $type = $_SESSION['flash_type'] ?? 'success';
    echo '<div class="alert alert-' . htmlspecialchars($type) . '">' . htmlspecialchars($_SESSION['flash_message']) . '</div>';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}
?>
