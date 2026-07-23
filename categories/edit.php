<?php
require_once __DIR__ . '/../Authentication/auth_check.php';
require_once __DIR__ . '/category_controller.php';

$pageTitle  = 'Edit Category';
$activePage = 'categories';
$id = (int)($_GET['id'] ?? 0);
$cat = getCategoryById($pdo, $id);
if (!$cat) { header('Location: index.php'); exit; }
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    if ($name === '') {
        $error = 'Name is required.';
    } else {
        updateCategory($pdo, $id, $name, $desc);
        header('Location: index.php');
        exit;
    }
    $cat = array_merge($cat, $_POST);
}

require __DIR__ . '/../Includes/header.php';
require __DIR__ . '/../Includes/navbar.php';
?>
<main class="main-content">
    <h1 class="page-title">Edit Category</h1>
    <div class="form-card">
        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST" action="edit.php?id=<?= $id ?>">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($cat['name']) ?>">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"><?= htmlspecialchars($cat['description'] ?? '') ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="index.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</main>
<?php require __DIR__ . '/../Includes/footer.php'; ?>
