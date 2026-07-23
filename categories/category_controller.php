<?php
require_once __DIR__ . '/../Database/db.php';

function getAllCategories(PDO $conn): array {
    return $conn->query("SELECT c.*, (SELECT COUNT(*) FROM books b WHERE b.category_id = c.id) AS book_count
                         FROM categories c ORDER BY c.id DESC")->fetchAll();
}
function getCategoryById(PDO $conn, int $id): ?array {
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}
function createCategory(PDO $conn, string $name, string $desc): void {
    $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    $stmt->execute([$name, $desc]);
}
function updateCategory(PDO $conn, int $id, string $name, string $desc): void {
    $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
    $stmt->execute([$name, $desc, $id]);
    header('location: Dashboard/index.php');
}
function deleteCategory(PDO $conn, int $id): void {
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
}
