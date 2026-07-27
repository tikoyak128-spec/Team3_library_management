<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Authentication/auth_check.php';
require_once BASE_PATH . '/Database/db.php';

// Fetch all authors using PDO
$stmt = $conn->query("SELECT * FROM authors ORDER BY id DESC");
$authors = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fixed: Changed from fetch_assoc() to fetchAll(PDO::FETCH_ASSOC)

$pageTitle  = 'Manage Authors';
$activePage = 'authors';

require_once BASE_PATH . '/Includes/header.php';
?>

<style>


body{
    justify-items: center;
}
.topbar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

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

.card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    padding: 1.5rem;
    border: 1px solid #f1f5f9;
    overflow-x: auto;
}

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
    vertical-align: middle;
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

.text-center {
    text-align: center !important;
}

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
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
}

.author-id {
    font-family: monospace;
    font-weight: 600;
    background-color: #f1f5f9;
    color: #475569;
    padding: 0.2rem 0.4rem;
    border-radius: 6px;
    font-size: 0.85rem;
    display: inline-block;
}

.empty-state {
    text-align: center;
    padding: 2rem !important;
    color: #94a3b8;
    font-style: italic;
}
</style>

<body>
    <main class="main-content">
        <div class="topbar-header">
            <div>
                <a href="../Dashboard/index.php" class="btn btn-outline" title="Back to Dashboard">
                    <i class="fa-solid fa-house"></i> Dashboard
                </a>
            </div>
            <div>
                <!-- Trigger form or link to add author -->
                <a href="create.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Add Author
                </a>
            </div>
        </div>
    
        <h1 class="page-title">Authors</h1>
        <p class="page-subtitle">Manage authors in your library collection.</p>
    
        <!-- Flash Message Notification -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div style="padding: 0.75rem 1.25rem; margin-bottom: 1rem; border-radius: 8px; background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;">
                <?= htmlspecialchars($_SESSION['flash_message']) ?>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>
    
        <div class="card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 100px;">ID</th>
                        <th>Author Name</th>
                        <th class="text-center" style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($authors)): ?>
                        <?php foreach ($authors as $author): ?>
                            <tr>
                                <td class="text-center">
                                    <span class="author-id">A<?= str_pad($author['id'], 3, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td><strong><?= htmlspecialchars($author['name']) ?></strong></td>
                                <td class="text-center">
                                    <div class="actions-cell">
                                        <a class="btn btn-outline btn-sm" href="edit.php?id=<?= $author['id'] ?>" title="Edit Author">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </a>
                                        <a class="btn btn-danger btn-sm" href="delete.php?id=<?= $author['id'] ?>" onclick="return confirm('Are you sure you want to delete this author?');" title="Delete Author">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="empty-state">No authors found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

<?php require_once BASE_PATH . '/Includes/footer.php'; ?>