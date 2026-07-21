<?php
require_once __DIR__ . '/../Authentication/auth_check.php';
require_once __DIR__ . '/book_controller.php';

$pageTitle  = 'Add Book';
$activePage = 'books';
$error = '';

$authors    = $pdo->query('SELECT id, name FROM authors ORDER BY name')->fetchAll();
$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    if ($title === '') {
        $error = 'Title is required.';
    } else {
        createBook($pdo, [
            'title'         => $title,
            'author_id'     => $_POST['author_id'] ?? null,
            'category_id'   => $_POST['category_id'] ?? null,
            'isbn'          => trim($_POST['isbn'] ?? ''),
            'total_copies'  => max(1, (int)($_POST['total_copies'] ?? 1)),
        ]);
        header('Location: index.php');
        exit;
    }
}

require __DIR__ . '/../Includes/header.php';
require __DIR__ . '/../Includes/navbar.php';
?>
<main class="main-content">
    <link rel="stylesheet" href="style.css">
    <h1 class="page-title">Add Book</h1>
    <p class="page-subtitle">Add a new title to the catalogue.</p>
    <div class="form-card">
        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST" action="create.php">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Author</label>
                <select name="author_id">
                    <option value="">— Select author —</option>
                    <?php foreach ($authors as $a): ?>
                        <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category_id">
                    <option value="">— Select category —</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>ISBN</label>
                <input type="text" name="isbn" value="<?= htmlspecialchars($_POST['isbn'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Total Copies</label>
                <input type="number" name="total_copies" min="1" value="<?= htmlspecialchars($_POST['total_copies'] ?? 1) ?>">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Book</button>
                <a href="index.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</main>
<?php require __DIR__ . '/../Includes/footer.php'; ?>
