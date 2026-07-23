<?php
// Reusable book data-access functions
require_once __DIR__ . '/../Database/db.php';

function getAllBooks(PDO $pdo, string $search = ''): array {
    $sql = "SELECT b.*, a.name AS author_name, c.name AS category_name
            FROM books b
            LEFT JOIN authors a ON a.id = b.author_id
            LEFT JOIN categories c ON c.id = b.category_id";
    $params = [];
    if ($search !== '') {
        $sql .= " WHERE b.title LIKE ? OR a.name LIKE ? OR b.isbn LIKE ?";
        $like = "%$search%";
        $params = [$like, $like, $like];
    }
    $sql .= " ORDER BY b.id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getBookById(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT b.*, a.name AS author_name, c.name AS category_name
                            FROM books b
                            LEFT JOIN authors a ON a.id = b.author_id
                            LEFT JOIN categories c ON c.id = b.category_id
                            WHERE b.id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function createBook(PDO $pdo, array $data): void {
    $stmt = $pdo->prepare("INSERT INTO books (title, author_id, category_id, isbn, total_copies, available_copies)
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['title'], $data['author_id'] ?: null, $data['category_id'] ?: null,
        $data['isbn'], $data['total_copies'], $data['total_copies'],
    ]);
}

function updateBook(PDO $pdo, int $id, array $data): void {
    $stmt = $pdo->prepare("UPDATE books SET title=?, author_id=?, category_id=?, isbn=?, total_copies=? WHERE id=?");
    $stmt->execute([
        $data['title'], $data['author_id'] ?: null, $data['category_id'] ?: null,
        $data['isbn'], $data['total_copies'], $id,
    ]);
}

function deleteBook(PDO $pdo, int $id): void {
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    $stmt->execute([$id]);
}
