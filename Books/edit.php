<?php
require_once __DIR__ . '/../Authentication/auth_check.php';
require_once __DIR__ . '/book_controller.php';
// Replace line 4 with this:
require_once __DIR__ . '/../Database/db.php';
$pageTitle  = 'Edit Book';
$activePage = 'books';
$id = (int)($_GET['id'] ?? 0);
$book = getBookById($conn, $id);
if (!$book) { header('Location: index.php'); exit; }

$error = '';
$authors    = $conn->query('SELECT id, name FROM authors ORDER BY name')->fetchAll();
$categories = $conn->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    if ($title === '') {
        $error = 'Title is required.';
    } else {
        updateBook($conn, $id, [
            'title'         => $title,
            'author_id'     => $_POST['author_id'] ?? null,
            'category_id'   => $_POST['category_id'] ?? null,
            'isbn'          => trim($_POST['isbn'] ?? ''),
            'total_copies'  => max(1, (int)($_POST['total_copies'] ?? 1)),
        ]);
        header('Location: index.php');
        exit;
    }
    $book = array_merge($book, $_POST);
}

require __DIR__ . '/../Includes/header.php';

?>
<main class="main-content">
    <link rel="stylesheet" href="style.css">
    <h1 class="page-title">Edit Book</h1>
    <div class="form-card">
        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST" action="edit.php?id=<?= $id ?>">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" required value="<?= htmlspecialchars($book['title']) ?>">
            </div>
            <div class="form-group">
                <label>Author</label>
                <select name="author_id">
                    <option value="">— Select author —</option>
                    <?php foreach ($authors as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= $a['id'] == $book['author_id'] ? 'selected' : '' ?>><?= htmlspecialchars($a['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category_id">
                    <option value="">— Select category —</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $c['id'] == $book['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>ISBN</label>
                <input type="text" name="isbn" value="<?= htmlspecialchars($book['isbn'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Total Copies</label>
                <input type="number" name="total_copies" value="<?php echo htmlspecialchars($book['total_copies'] ?? $book['quantity'] ?? 1); ?>">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Book</button>
                <a href="index.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</main>
<?php require __DIR__ . '/../Includes/footer.php'; ?>
