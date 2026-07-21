<?php
require_once __DIR__ . '/../Authentication/auth_check.php';
require_once __DIR__ . '/author_controller.php';

$pageTitle  = 'Edit Author';
$activePage = 'authors';
$id = (int)($_GET['id'] ?? 0);
$author = getAuthorById($pdo, $id);
if (!$author) { header('Location: index.php'); exit; }
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $bio  = trim($_POST['bio'] ?? '');
    if ($name === '') {
        $error = 'Name is required.';
    } else {
        updateAuthor($pdo, $id, $name, $bio);
        header('Location: index.php');
        exit;
    }
    $author = array_merge($author, $_POST);
}

require __DIR__ . '/../Includes/header.php';
require __DIR__ . '/../Includes/navbar.php';
?>
<main class="main-content">
    <link rel="stylesheet" href="style.css">
    <h1 class="page-title">Edit Author</h1>
    <div class="form-card">
        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST" action="edit.php?id=<?= $id ?>">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($author['name']) ?>">
            </div>
            <div class="form-group">
                <label>Bio</label>
                <textarea name="bio" rows="4"><?= htmlspecialchars($author['bio'] ?? '') ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="index.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</main>
<?php require __DIR__ . '/../Includes/footer.php'; ?>
