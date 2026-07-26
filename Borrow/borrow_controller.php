<?php
define('BASE_PATH', dirname(__DIR__)); 
require_once BASE_PATH . '/Database/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$book_id   = (int)($_POST['book_id'] ?? 0);
$member_id = (int)($_POST['member_id'] ?? 0);
$borrow_date = !empty($_POST['borrow_date']) ? $_POST['borrow_date'] : date('Y-m-d');

if ($book_id > 0 && $member_id > 0) {
    try {
        // 1. Begin PDO Transaction
        $conn->beginTransaction();

        // 2. Check book availability
        $stmt = $conn->prepare("SELECT quantity FROM books WHERE id = ? FOR UPDATE");
        $stmt->execute([$book_id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$book || $book['quantity'] <= 0) {
            throw new Exception("Book is not available.");
        }

        // 3. Insert borrowing record
        $stmt = $conn->prepare("INSERT INTO borrowings (book_id, member_id, borrow_date, status) VALUES (?, ?, ?, 'borrowed')");
        $stmt->execute([$book_id, $member_id, $borrow_date]);

        // 4. Decrease book quantity & update status
        $newQty = $book['quantity'] - 1;
        $newStatus = ($newQty > 0) ? 'available' : 'unavailable';

        $stmt = $conn->prepare("UPDATE books SET quantity = ?, status = ? WHERE id = ?");
        $stmt->execute([$newQty, $newStatus, $book_id]);

        // 5. Commit Transaction
        $conn->commit();

        $_SESSION['flash_message'] = 'Book borrowed successfully.';
        $_SESSION['flash_type'] = 'success';

    } catch (Exception $e) {
        // Rollback transaction on failure
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $_SESSION['flash_message'] = 'Error: ' . $e->getMessage();
        $_SESSION['flash_type'] = 'danger';
    }
} else {
    $_SESSION['flash_message'] = 'Please select a book and a member.';
    $_SESSION['flash_type'] = 'danger';
}

header("Location: index.php");
exit;