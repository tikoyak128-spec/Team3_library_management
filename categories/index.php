<?php
require_once __DIR__ . '/../Authentication/auth_check.php';
require_once __DIR__ . '/category_controller.php';
require_once __DIR__ . '/../Database/db.php';

$pageTitle  = 'Categories';
$activePage = 'categories';
$categories = getAllCategories($conn);

require __DIR__ . '/../Includes/header.php';
?>

<style>
/* Top Header & Action Layout */
.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.topbar-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.page-title {
    font-size: 1.6rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

/* Card Wrapper */
.card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    padding: 1.5rem;
    border: 1px solid #f1f5f9;
    overflow-x: auto;
}

/* Modern Data Table */
.data-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}

.data-table th {
    background-color: #f8fafc;
    color: #64748b;
    font-size: 0.825rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.85rem 1rem;
    border-bottom: 2px solid #e2e8f0;
}

.data-table td {
    padding: 1rem;
    font-size: 0.925rem;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.data-table tr:hover {
    background-color: #f8fafc;
    transition: background 0.2s ease;
}

.data-table tr:last-child td {
    border-bottom: none;
}

/* Category ID Badge */
.cat-badge {
    display: inline-block;
    background-color: #f1f5f9;
    color: #475569;
    font-family: monospace;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.85rem;
}

/* Book Count Badge */
.book-count-badge {
    display: inline-block;
    background-color: #e0e7ff;
    color: #4338ca;
    font-weight: 600;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.825rem;
}

/* Buttons Styling */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
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

.btn-sm {
    padding: 0.35rem 0.75rem;
    font-size: 0.8rem;
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

.btn-danger {
    background-color: #fff1f2;
    color: #e11d48;
    border-color: #fecdd3;
}

.btn-danger:hover {
    background-color: #e11d48;
    color: #ffffff;
    border-color: #e11d48;
}

.actions-cell {
    display: flex;
    gap: 0.5rem;
}

.empty-state {
    text-align: center;
    padding: 2rem !important;
    color: #94a3b8;
    font-style: italic;
}
</style>

<main class="main-content">
    <div class="topbar">
        <div class="topbar-left">
            <!-- Back to Dashboard Navigation Icon -->
            <a href="../Dashboard/index.php" class="btn btn-outline" title="Back to Dashboard">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
            <h1 class="page-title">Categories</h1>
        </div>

        <a href="create.php" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Add Category
        </a>
    </div>

    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Books</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $c): ?>
                <tr>
                    <td><span class="cat-badge">C<?= str_pad($c['id'], 3, '0', STR_PAD_LEFT) ?></span></td>
                    <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                    <td><?= htmlspecialchars($c['description'] ?? '—') ?></td>
                    <td>
                        <span class="book-count-badge">
                            <?= (int)($c['book_count'] ?? 0) ?> books
                        </span>
                    </td>
                    <td>
                        <div class="actions-cell">
                            <a class="btn btn-outline btn-sm" href="edit.php?id=<?= $c['id'] ?>">
                                <i class="fa-solid fa-pen"></i> Edit
                            </a>
                            <a class="btn btn-danger btn-sm" href="delete.php?id=<?= $c['id'] ?>" onclick="return confirm('Delete this category?');">
                                <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php if (!$categories): ?>
                <tr>
                    <td colspan="5" class="empty-state">No categories found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require __DIR__ . '/../Includes/footer.php'; ?>