<?php
require_once __DIR__ . '/../Authentication/auth_check.php';
require_once __DIR__ . '/author_controller.php';

$pageTitle  = 'Add Author';
$activePage = 'authors';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $bio  = trim($_POST['bio'] ?? '');
    if ($name === '') {
        $error = 'Name is required.';
    } else {
        createAuthor($pdo, $name, $bio);
        header('Location: index.php');
        exit;
    }
}

require __DIR__ . '/../Includes/header.php';
require __DIR__ . '/../Includes/navbar.php';
?>
<main class="main-content">
    <link rel="stylesheet" href="style.css">
    <h1 class="page-title">Add Author</h1>
    <div class="form-card">
        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST" action="create.php">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Bio</label>
                <textarea name="bio" rows="4"><?= htmlspecialchars($_POST['bio'] ?? '') ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="index.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</main>
<?php require __DIR__ . '/../Includes/footer.php'; ?>
