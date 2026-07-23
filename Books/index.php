<?php
require_once __DIR__ . '/../Authentication/auth_check.php';
require_once __DIR__ . '/book_controller.php';
require_once __DIR__ . '/../Database/db.php';

$pageTitle  = 'Books Available';
$activePage = 'books';
$search     = trim($_GET['q'] ?? '');
$books      = getAllBooks($conn, $search);

require __DIR__ . '/../Includes/header.php';
?>

<style>
/* Header & Navigation Bar */
.topbar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.topbar-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

/* Page Titles */
.page-title {
    font-size: 1.6rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.page-subtitle {
    color: #64748b;
    font-size: 0.9rem;
    margin: 0.25rem 0 1.5rem 0;
}

/* Search Bar */
.search-box {
    display: flex;
    align-items: center;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 0.4rem 0.8rem;
    transition: all 0.2s ease;
}

.search-box:focus-within {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
}

.search-box i {
    color: #94a3b8;
    margin-right: 0.5rem;
}

.search-box input {
    border: none;
    outline: none;
    font-size: 0.9rem;
    color: #1e293b;
    background: transparent;
    width: 220px;
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

/* Data Table */
.data-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}

.data-table th {
    background-color: #f8fafc;
    color: #64748b;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.85rem 1rem;
    border-bottom: 2px solid #e2e8f0;
}

.data-table td {
    padding: 1rem;
    font-size: 0.9rem;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.data-table tr:hover {
    background-color: #f8fafc;
}

.data-table tr:last-child td {
    border-bottom: none;
}

/* ID Tag */
.book-id {
    font-family: monospace;
    font-weight: 600;
    background-color: #f1f5f9;
    color: #475569;
    padding: 0.2rem 0.4rem;
    border-radius: 6px;
    font-size: 0.85rem;
}

/* Status Badges */
.badge {
    display: inline-block;
    padding: 0.25rem 0.6rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.badge.approved {
    background-color: #dcfce7;
    color: #15803d;
}

.badge.overdue {
    background-color: #ffe4e6;
    color: #be123c;
}

/* Button Styling */
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
}

.btn-sm {
    padding: 0.35rem 0.65rem;
    font-size: 0.8rem;
}

.actions-cell {
    display: flex;
    gap: 0.4rem;
}

.empty-state {
    text-align: center;
    padding: 2rem !important;
    color: #94a3b8;
    font-style: italic;
}
</style>

<main class="main-content">
    <!-- Header Topbar with Back to Dashboard & Action Buttons -->
    <div class="topbar-header">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <!-- Back to Dashboard Icon Button -->
            <a href="../Dashboard/index.php" class="btn btn-outline" title="Back to Dashboard">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
        </div>

        <div class="topbar-actions">
            <!-- Search Form -->
            <form class="search-box" method="GET" action="index.php">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="q" placeholder="Search books..." value="<?= htmlspecialchars($search) ?>">
            </form>

            <!-- Add Book Button -->
            <a href="create.php" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Add Book
            </a>
        </div>
    </div>

    <h1 class="page-title">Books Available</h1>
    <p class="page-subtitle">Manage your library's book catalogue.</p>

    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>ISBN</th>
                    <th>Available</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $b): 
                    $avail = (int)($b['available_copies'] ?? $b['quantity'] ?? 0);
                    $total = (int)($b['total_copies'] ?? $b['quantity'] ?? 0);
                ?>
                <tr>
                    <td><span class="book-id">B<?= str_pad($b['id'], 3, '0', STR_PAD_LEFT) ?></span></td>
                    <td><strong><?= htmlspecialchars($b['title']) ?></strong></td>
                    <td><?= htmlspecialchars($b['author_name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($b['category_name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($b['isbn'] ?? '—') ?></td>
                    <td>
                        <span class="badge <?= $avail > 0 ? 'approved' : 'overdue' ?>">
                            <?= $avail ?>
                        </span>
                    </td>
                    <td><?= $total ?></td>
                    <td>
                        <div class="actions-cell">
                            <a class="btn btn-outline btn-sm" href="view.php?id=<?= $b['id'] ?>" title="View Details">
                                <i class="fa-solid fa-eye"></i> View
                            </a>
                            <a class="btn btn-outline btn-sm" href="edit.php?id=<?= $b['id'] ?>" title="Edit Book">
                                <i class="fa-solid fa-pen"></i> Edit
                            </a>
                            <a class="btn btn-danger btn-sm" href="delete.php?id=<?= $b['id'] ?>" onclick="return confirm('Delete this book?');" title="Delete Book">
                                <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php if (!$books): ?>
                <tr>
                    <td colspan="8" class="empty-state">No books found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require __DIR__ . '/../Includes/footer.php'; ?>