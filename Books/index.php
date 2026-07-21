<?php
require_once __DIR__ . '/../Authentication/auth_check.php';
require_once __DIR__ . '/book_controller.php';

$pageTitle  = 'Books Available';
$activePage = 'books';
$search = trim($_GET['q'] ?? '');
$books = getAllBooks($pdo, $search);

require __DIR__ . '/../Includes/header.php';
require __DIR__ . '/../Includes/navbar.php';
?>
<main class="main-content">
    <link rel="stylesheet" href="style.css">
    <div class="topbar">
        <form class="search-box" method="GET" action="index.php">
            🔍 <input type="text" name="q" placeholder="Search books..." value="<?= htmlspecialchars($search) ?>">
        </form>
        <a href="create.php" class="btn btn-primary">+ Add Book</a>
    </div>

    <h1 class="page-title">Books Available</h1>
    <p class="page-subtitle">Manage your library's book catalogue.</p>

    <div class="card">
        <table class="data-table">
            <tr><th>ID</th><th>Title</th><th>Author</th><th>Category</th><th>ISBN</th><th>Available</th><th>Total</th><th>Actions</th></tr>
            <?php foreach ($books as $b): ?>
            <tr>
                <td>B<?= str_pad($b['id'], 3, '0', STR_PAD_LEFT) ?></td>
                <td><?= htmlspecialchars($b['title']) ?></td>
                <td><?= htmlspecialchars($b['author_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($b['category_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($b['isbn'] ?? '—') ?></td>
                <td><span class="badge <?= $b['available_copies'] > 0 ? 'approved' : 'overdue' ?>"><?= (int)$b['available_copies'] ?></span></td>
                <td><?= (int)$b['total_copies'] ?></td>
                <td>
                    <a class="btn btn-outline btn-sm" href="view.php?id=<?= $b['id'] ?>">View</a>
                    <a class="btn btn-outline btn-sm" href="edit.php?id=<?= $b['id'] ?>">Edit</a>
                    <a class="btn btn-danger btn-sm" href="delete.php?id=<?= $b['id'] ?>" onclick="return confirm('Delete this book?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$books): ?><tr><td colspan="8">No books found.</td></tr><?php endif; ?>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../Includes/footer.php'; ?>
