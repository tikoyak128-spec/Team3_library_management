<?php
    define('BASE_PATH', dirname(__DIR__)); 
    require_once BASE_PATH . '/Database/db.php';

    if (session_status() === PHP_SESSION_NONE) session_start();

    $book_id = (int)($_POST['book_id'] ?? 0);
    $member_id = (int)($_POST['member_id'] ?? 0);
    $borrow_date = $_POST['borrow_date'] ?? date('Y-m-d');

    if ($book_id > 0 && $member_id > 0) {
        $conn->begin_transaction();
        try {
            // Check book availability
            $stmt = $conn->prepare("SELECT quantity FROM books WHERE id = ? FOR UPDATE");
            $stmt->bind_param("i", $book_id);
            $stmt->execute();
            $book = $stmt->get_result()->fetch_assoc();

            if (!$book || $book['quantity'] <= 0) {
                throw new Exception("Book is not available.");
            }

            // Insert borrowing record
            $stmt = $conn->prepare("INSERT INTO borrowings (book_id, member_id, borrow_date, status) VALUES (?, ?, ?, 'borrowed')");
            $stmt->bind_param("iis", $book_id, $member_id, $borrow_date);
            $stmt->execute();

            // Decrease book quantity
            $newQty = $book['quantity'] - 1;
            $newStatus = $newQty > 0 ? 'available' : 'unavailable';
            $stmt = $conn->prepare("UPDATE books SET quantity = ?, status = ? WHERE id = ?");
            $stmt->bind_param("isi", $newQty, $newStatus, $book_id);
            $stmt->execute();

            $conn->commit();
            $_SESSION['flash_message'] = 'Book borrowed successfully.';
            $_SESSION['flash_type'] = 'success';
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['flash_message'] = 'Error: ' . $e->getMessage();
            $_SESSION['flash_type'] = 'danger';
        }
    } else {
        $_SESSION['flash_message'] = 'Please select a book and a member.';
        $_SESSION['flash_type'] = 'danger';
    }

    header("Location: index.php");
    exit;
