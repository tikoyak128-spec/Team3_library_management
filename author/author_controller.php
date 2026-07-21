<?php
require_once __DIR__ . '/../Database/db.php';

function getAllAuthors(PDO $pdo): array {
    return $pdo->query("SELECT a.*, (SELECT COUNT(*) FROM books b WHERE b.author_id = a.id) AS book_count
                         FROM authors a ORDER BY a.id DESC")->fetchAll();
}
function getAuthorById(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM authors WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}
function createAuthor(PDO $pdo, string $name, string $bio): void {
    $stmt = $pdo->prepare("INSERT INTO authors (name, bio) VALUES (?, ?)");
    $stmt->execute([$name, $bio]);
}
function updateAuthor(PDO $pdo, int $id, string $name, string $bio): void {
    $stmt = $pdo->prepare("UPDATE authors SET name = ?, bio = ? WHERE id = ?");
    $stmt->execute([$name, $bio, $id]);
}
function deleteAuthor(PDO $pdo, int $id): void {
    $stmt = $pdo->prepare("DELETE FROM authors WHERE id = ?");
    $stmt->execute([$id]);
}
