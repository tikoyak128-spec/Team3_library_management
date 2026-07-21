<?php
require_once __DIR__ . '/../Authentication/auth_check.php';
require_once __DIR__ . '/author_controller.php';

$pageTitle  = 'Authors';
$activePage = 'authors';
$authors = getAllAuthors($pdo);

require __DIR__ . '/../Includes/header.php';
require __DIR__ . '/../Includes/navbar.php';
?>
<link rel="stylesheet" href="style.css">
<main class="main-content">
    <div class="topbar">
        <h1 class="page-title" style="margin:0;">Authors</h1>
        <a href="create.php" class="btn btn-primary">+ Add Author</a>
    </div>
    <div class="card">
        <table class="data-table">
            <tr><th>ID</th><th>Name</th><th>Bio</th><th>Books</th><th>Actions</th></tr>
            <?php foreach ($authors as $a): ?>
            <tr>
                <td>A<?= str_pad($a['id'], 3, '0', STR_PAD_LEFT) ?></td>
                <td><?= htmlspecialchars($a['name']) ?></td>
                <td><?= htmlspecialchars(mb_strimwidth($a['bio'] ?? '—', 0, 60, '...')) ?></td>
                <td><?= (int)$a['book_count'] ?></td>
                <td>
                    <a class="btn btn-outline btn-sm" href="edit.php?id=<?= $a['id'] ?>">Edit</a>
                    <a class="btn btn-danger btn-sm" href="delete.php?id=<?= $a['id'] ?>" onclick="return confirm('Delete this author?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$authors): ?><tr><td colspan="5">No authors found.</td></tr><?php endif; ?>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../Includes/footer.php'; ?>
