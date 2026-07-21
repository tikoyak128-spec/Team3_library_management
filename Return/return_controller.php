<?Php include '../includes/header.php'; ?>
<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

$borrow_id = (int)($_POST['borrow_id'] ?? 0);
$return_date = $_POST['return_date'] ?? date('Y-m-d');

if ($borrow_id > 0) {
    $conn->begin_transaction();
    try {
        // Get the borrowing record
        $stmt = $conn->prepare("SELECT * FROM borrowings WHERE id = ? AND status = 'borrowed' FOR UPDATE");
        $stmt->bind_param("i", $borrow_id);
        $stmt->execute();
        $borrow = $stmt->get_result()->fetch_assoc();

        if (!$borrow) {
            throw new Exception("Borrow record not found or already returned.");
        }

        // Update borrowing record
        $stmt = $conn->prepare("UPDATE borrowings SET return_date = ?, status = 'returned' WHERE id = ?");
        $stmt->bind_param("si", $return_date, $borrow_id);
        $stmt->execute();

        // Increase book quantity and mark available
        $stmt = $conn->prepare("UPDATE books SET quantity = quantity + 1, status = 'available' WHERE id = ?");
        $stmt->bind_param("i", $borrow['book_id']);
        $stmt->execute();

        $conn->commit();
        $_SESSION['flash_message'] = 'Book returned successfully.';
        $_SESSION['flash_type'] = 'success';
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['flash_message'] = 'Error: ' . $e->getMessage();
        $_SESSION['flash_type'] = 'danger';
    }
} else {
    $_SESSION['flash_message'] = 'Invalid return request.';
    $_SESSION['flash_type'] = 'danger';
}

header("Location: index.php");
exit;
