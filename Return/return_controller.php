<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

// Ensure session is started for flash messages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$borrow_id   = (int)($_POST['borrow_id'] ?? 0);
$return_date = $_POST['return_date'] ?? date('Y-m-d');

if ($borrow_id > 0) {
    try {
        // Begin PDO Transaction
        $conn->beginTransaction();

        // 1. Fetch the borrowing record
        $sql  = "SELECT * FROM borrowings WHERE id = :id AND status = 'borrowed' FOR UPDATE";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $borrow_id]);
        $borrow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$borrow) {
            throw new Exception("Borrow record not found or already returned.");
        }

        // 2. Update borrowing record status and return date
        $updateBorrow = $conn->prepare("UPDATE borrowings SET return_date = :return_date, status = 'returned' WHERE id = :id");
        $updateBorrow->execute([
            ':return_date' => $return_date,
            ':id'          => $borrow_id
        ]);

        // 3. Increase book quantity and set status to available
        $updateBook = $conn->prepare("UPDATE books SET quantity = quantity + 1, status = 'available' WHERE id = :book_id");
        $updateBook->execute([
            ':book_id' => $borrow['book_id']
        ]);

        // Commit transaction if all queries succeeded
        $conn->commit();

        $_SESSION['flash_message'] = 'Book returned successfully.';
        $_SESSION['flash_type']    = 'success';

    } catch (Exception $e) {
        // Roll back changes if any step fails
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        $_SESSION['flash_message'] = 'Error: ' . $e->getMessage();
        $_SESSION['flash_type']    = 'danger';
    }
} else {
    $_SESSION['flash_message'] = 'Invalid return request.';
    $_SESSION['flash_type']    = 'danger';
}

// Redirect back to the borrowings list
header("Location: ../Borrow/index.php");
exit;