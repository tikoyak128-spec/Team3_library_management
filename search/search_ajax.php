<?php
header('Content-Type: application/json');

// Path from search/ folder to Database/ folder
require_once __DIR__ . '/../Database/db.php';

// Check which database variable exists in db.php and set $conn safely
if (!isset($conn)) {
    if (isset($pdo)) {
        $conn = $pdo;
    } elseif (isset($db)) {
        $conn = $db;
    }
}

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($query)) {
    echo json_encode([
        'status' => 'success',
        'results' => [
            'books' => [],
            'members' => [],
            'borrowings' => []
        ]
    ]);
    exit;
}

$searchTerm = "%{$query}%";

try {
    // 1. Search Books
    $stmtBook = $conn->prepare("
        SELECT b.id, b.title, b.status, a.name AS author_name 
        FROM books b 
        LEFT JOIN authors a ON b.author_id = a.id 
        WHERE b.title LIKE :query OR a.name LIKE :query 
        LIMIT 5
    ");
    $stmtBook->execute([':query' => $searchTerm]);
    $books = $stmtBook->fetchAll(PDO::FETCH_ASSOC);

    // 2. Search Members / Students
    $stmtMember = $conn->prepare("
        SELECT id, name, email, phone, profile_image 
        FROM members 
        WHERE name LIKE :query OR email LIKE :query OR phone LIKE :query 
        LIMIT 5
    ");
    $stmtMember->execute([':query' => $searchTerm]);
    $members = $stmtMember->fetchAll(PDO::FETCH_ASSOC);

    // 3. Search Borrowing Records
    $stmtBorrow = $conn->prepare("
        SELECT br.id, br.status, br.borrow_date, m.name AS student_name, bk.title AS book_title 
        FROM borrowings br 
        JOIN members m ON br.member_id = m.id 
        JOIN books bk ON br.book_id = bk.id 
        WHERE m.name LIKE :query OR bk.title LIKE :query 
        LIMIT 5
    ");
    $stmtBorrow->execute([':query' => $searchTerm]);
    $borrowings = $stmtBorrow->fetchAll(PDO::FETCH_ASSOC);

    // Send JSON Response
    echo json_encode([
        'status' => 'success',
        'results' => [
            'books' => $books,
            'members' => $members,
            'borrowings' => $borrowings
        ]
    ]);
    $stmtBook = $conn->prepare("
    SELECT b.*, a.name AS author_name, c.name AS category_name 
    FROM books b 
    LEFT JOIN authors a ON b.author_id = a.id 
    LEFT JOIN categories c ON b.category_id = c.id 
    WHERE LOWER(b.title) LIKE LOWER(:query) 
       OR LOWER(a.name) LIKE LOWER(:query) 
       OR LOWER(c.name) LIKE LOWER(:query)
       OR CAST(b.id AS CHAR) LIKE :query
    ORDER BY b.id DESC
");
$stmtBook->execute([':query' => $searchTerm]);
$books = $stmtBook->fetchAll(PDO::FETCH_ASSOC);
$stmtMember = $conn->prepare("
    SELECT * FROM members 
    WHERE LOWER(name) LIKE LOWER(:query) 
       OR LOWER(email) LIKE LOWER(:query) 
       OR phone LIKE :query 
       OR CAST(id AS CHAR) LIKE :query
    ORDER BY id DESC
");
$stmtMember->execute([':query' => $searchTerm]);
$members = $stmtMember->fetchAll(PDO::FETCH_ASSOC);
$stmtBorrow = $conn->prepare("
    SELECT br.*, m.name AS student_name, m.profile_image, bk.title AS book_title 
    FROM borrowings br 
    JOIN members m ON br.member_id = m.id 
    JOIN books bk ON br.book_id = bk.id 
    WHERE LOWER(m.name) LIKE LOWER(:query) 
       OR LOWER(bk.title) LIKE LOWER(:query) 
       OR CAST(br.id AS CHAR) LIKE :query
    ORDER BY br.id DESC
");
$stmtBorrow->execute([':query' => $searchTerm]);
$borrowings = $stmtBorrow->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}