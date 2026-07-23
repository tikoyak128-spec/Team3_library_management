<?php
require_once __DIR__ . '/../Authentication/auth_check.php';
require_once __DIR__ . '/category_controller.php';
require_once __DIR__ . '/../Database/db.php';

$pageTitle  = 'Edit Category';
$activePage = 'categories';

$id  = (int)($_GET['id'] ?? 0);
$cat = getCategoryById($conn, $id);

if (!$cat) { 
    header('Location: index.php'); 
    exit; 
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    if ($name === '') {
        $error = 'Name is required.';
    } else {
        updateCategory($conn, $id, $name, $desc);
        header('Location: index.php');
        exit;
    }
    $cat = array_merge($cat, $_POST);
}

require __DIR__ . '/../Includes/header.php';
?>

<style>
/* Header & Layout */
.page-title {
    font-size: 1.6rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1.5rem;
}

/* Card Wrapper */
.form-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    padding: 2rem;
    border: 1px solid #f1f5f9;
    max-width: 600px;
}

/* Alert Styling */
.alert {
    padding: 0.85rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
}

.alert-error {
    background-color: #fff1f2;
    color: #e11d48;
    border: 1px solid #fecdd3;
}

/* Form Controls */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1.25rem;
}

.form-group label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #475569;
}

.form-group input[type="text"],
.form-group textarea {
    width: 100%;
    padding: 0.65rem 0.85rem;
    font-size: 0.925rem;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    color: #0f172a;
    background-color: #ffffff;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    outline: none;
    font-family: inherit;
}

.form-group input[type="text"]:focus,
.form-group textarea:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
}

/* Actions & Buttons */
.form-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-top: 1.75rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    padding: 0.6rem 1.25rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease-in-out;
    cursor: pointer;
    border: 1px solid transparent;
}

.btn-primary {
    background-color: #6366f1;
    color: #ffffff;
}

.btn-primary:hover {
    background-color: #4f46e5;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
}

.btn-outline {
    background-color: #ffffff;
    border-color: #cbd5e1;
    color: #475569;
}

.btn-outline:hover {
    background-color: #f8fafc;
    border-color: #94a3b8;
    color: #0f172a;
}
</style>

<main class="main-content">
    <h1 class="page-title">Edit Category</h1>

    <div class="form-card">
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="edit.php?id=<?= $id ?>">
            <div class="form-group">
                <label for="name">Category Name</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($cat['name']) ?>">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Brief details about this category..."><?= htmlspecialchars($cat['description'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Update
                </button>
                <a href="index.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php require __DIR__ . '/../Includes/footer.php'; ?>