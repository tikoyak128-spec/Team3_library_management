<?php
require_once __DIR__ . '/../Database/db.php';

function getAllCategories(PDO $pdo): array {
    return $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM books b WHERE b.category_id = c.id) AS book_count
                         FROM categories c ORDER BY c.id DESC")->fetchAll();
}
function getCategoryById(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}
function createCategory(PDO $pdo, string $name, string $desc): void {
    $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    $stmt->execute([$name, $desc]);
}
function updateCategory(PDO $pdo, int $id, string $name, string $desc): void {
    $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
    $stmt->execute([$name, $desc, $id]);
}
function deleteCategory(PDO $pdo, int $id): void {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
}
