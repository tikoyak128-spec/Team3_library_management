<?php
// Reusable book data-access functions
require_once __DIR__ . '/../Database/db.php';

function getAllBooks(PDO $conn, string $search = ''): array {
    $sql = "SELECT b.*, a.name AS author_name, c.name AS category_name
            FROM books b
            LEFT JOIN authors a ON a.id = b.author_id
            LEFT JOIN categories c ON c.id = b.category_id";
    $params = [];
    
    if ($search !== '') {
        $sql .= " WHERE b.title LIKE ? OR a.name LIKE ? OR b.isbn LIKE ?";
        $like = "%{$search}%";
        $params = [$like, $like, $like];
    }
    
    $sql .= " ORDER BY b.id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getBookById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createBook(PDO $conn, array $data): void {
    $stmt = $conn->prepare("INSERT INTO books (title, author_id, category_id, isbn, total_copies, available_copies)
                            VALUES (?, ?, ?, ?, ?, ?)");
    
    $totalCopies = (int)($data['total_copies'] ?? 1);

    $stmt->execute([
        $data['title'], 
        !empty($data['author_id']) ? $data['author_id'] : null, 
        !empty($data['category_id']) ? $data['category_id'] : null,
        $data['isbn'] ?? null, 
        $totalCopies, 
        $totalCopies // Initial available copies equals total copies
    ]);
}

function updateBook($conn, $id, $data) {
    // 1. Safe fallbacks for POST inputs to prevent Undefined array key warnings
    $title     = trim($data['title'] ?? '');
    $author_id = !empty($data['author_id']) ? (int)$data['author_id'] : null;
    $cat_id    = !empty($data['category_id']) ? (int)$data['category_id'] : null;
    $isbn      = trim($data['isbn'] ?? '');

    // Check for 'total_copies' OR fall back to 'quantity' / 1
    $total     = (int)($data['total_copies'] ?? $data['quantity'] ?? 1);
    
    // Check for 'available_copies' OR fall back to total copies
    $available = (int)($data['available_copies'] ?? $total);

    // 2. Prepared UPDATE query
    $sql = "UPDATE books 
            SET title = ?, 
                author_id = ?, 
                category_id = ?, 
                isbn = ?, 
                total_copies = ?, 
                available_copies = ? 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    return $stmt->execute([
        $title, 
        $author_id, 
        $cat_id, 
        $isbn, 
        $total, 
        $available, 
        $id
    ]);
}

function deleteBook(PDO $conn, int $id): bool {
    try {
        $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        // Return false if foreign key constraints prevent deletion (e.g., book has borrowing history)
        return false;
    }
}