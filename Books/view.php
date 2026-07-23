<?php
require_once __DIR__ . '/../Authentication/auth_check.php';
require_once __DIR__ . '/book_controller.php';

$pageTitle  = 'Book Details';
$activePage = 'books';
$id = (int)($_GET['id'] ?? 0);
$book = getBookById($conn, $id);
if (!$book) { header('Location: index.php'); exit; }

require __DIR__ . '/../Includes/header.php';
require __DIR__ . '/../Includes/navbar.php';
?>
<main class="main-content">
    <link rel="stylesheet" href="style.css">
    <h1 class="page-title"><?= htmlspecialchars($book['title']) ?></h1>
    <p class="page-subtitle">Book details</p>
    <div class="card" style="max-width:600px;">
        <table class="data-table">
            <tr><th>Author</th><td><?= htmlspecialchars($book['author_name'] ?? '—') ?></td></tr>
            <tr><th>Category</th><td><?= htmlspecialchars($book['category_name'] ?? '—') ?></td></tr>
            <tr><th>ISBN</th><td><?= htmlspecialchars($book['isbn'] ?? '—') ?></td></tr>
            <tr><th>Total Copies</th><td><?= (int)$book['total_copies'] ?></td></tr>
            <tr><th>Available</th><td><?= (int)$book['available_copies'] ?></td></tr>
            <tr><th>Added</th><td><?= htmlspecialchars($book['added_date'] ?? $book['created_at'] ?? 'N/A') ?></td></tr>
        </table>
        <div class="form-actions">
            <a href="edit.php?id=<?= $book['id'] ?>" class="btn btn-primary">Edit</a>
            <a href="index.php" class="btn btn-outline">Back</a>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../Includes/footer.php'; ?>
