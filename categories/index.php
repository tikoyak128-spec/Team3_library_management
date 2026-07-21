<?php
require_once __DIR__ . '/../Authentication/auth_check.php';
require_once __DIR__ . '/category_controller.php';

$pageTitle  = 'Categories';
$activePage = 'categories';
$categories = getAllCategories($pdo);

require __DIR__ . '/../Includes/header.php';
require __DIR__ . '/../Includes/navbar.php';
?>
<main class="main-content">
    <div class="topbar">
        <h1 class="page-title" style="margin:0;">Categories</h1>
        <a href="create.php" class="btn btn-primary">+ Add Category</a>
    </div>
    <div class="card">
        <table class="data-table">
            <tr><th>ID</th><th>Name</th><th>Description</th><th>Books</th><th>Actions</th></tr>
            <?php foreach ($categories as $c): ?>
            <tr>
                <td>C<?= str_pad($c['id'], 3, '0', STR_PAD_LEFT) ?></td>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= htmlspecialchars($c['description'] ?? '—') ?></td>
                <td><?= (int)$c['book_count'] ?></td>
                <td>
                    <a class="btn btn-outline btn-sm" href="edit.php?id=<?= $c['id'] ?>">Edit</a>
                    <a class="btn btn-danger btn-sm" href="delete.php?id=<?= $c['id'] ?>" onclick="return confirm('Delete this category?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$categories): ?><tr><td colspan="5">No categories found.</td></tr><?php endif; ?>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../Includes/footer.php'; ?>
