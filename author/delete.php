<?php include './includes/header.php' ?>
<?php
    define('BASE_PATH', dirname(__DIR__)); 
    require_once BASE_PATH . '/Database/db.php';

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM authors WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = 'Author deleted successfully.';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Could not delete author (it may be linked to books).';
            $_SESSION['flash_type'] = 'danger';
        }
    }

    header("Location: index.php");
    exit;
