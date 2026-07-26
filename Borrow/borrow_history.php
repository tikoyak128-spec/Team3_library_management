<?php include '../includes/header.php'; ?>

<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

// 1. Fetch full transaction history
$sql = "SELECT br.*, b.title, m.name AS member_name
        FROM borrowings br
        JOIN books b ON br.book_id = b.id
        JOIN members m ON br.member_id = m.id
        ORDER BY br.borrow_date DESC, br.id DESC";

$stmt = $conn->query($sql);
$history = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

// 2. Statistics Queries
$totalStmt = $conn->query("SELECT COUNT(*) FROM borrowings");
$totalHistory = $totalStmt ? $totalStmt->fetchColumn() : 0;

$returnedStmt = $conn->query("SELECT COUNT(*) FROM borrowings WHERE status = 'returned'");
$totalReturned = $returnedStmt ? $returnedStmt->fetchColumn() : 0;

$activeStmt = $conn->query("SELECT COUNT(*) FROM borrowings WHERE status = 'borrowed'");
$totalActive = $activeStmt ? $activeStmt->fetchColumn() : 0;
?>

<!-- Google Fonts & FontAwesome -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    :root {
        --primary: #6366f1;
        --primary-hover: #4f46e5;
        --bg-surface: #f8fafc;
        --card-bg: #ffffff;
        --text-dark: #0f172a;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --radius-lg: 16px;
        --radius-md: 12px;
        --radius-sm: 8px;
    }

    body {
        background-color: var(--bg-surface);
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--text-dark);
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--text-dark);
    }

    .page-subtitle {
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    /* Action Buttons */
    .btn-action-secondary {
        background: #ffffff;
        color: var(--text-dark) !important;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 0.65rem 1.25rem;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-action-secondary:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    /* Stat Cards */
    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.25rem 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: transform 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .stat-card .stat-label {
        font-size: 0.825rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
    }

    .stat-card .stat-number {
        font-size: 1.75rem;
        font-weight: 800;
        margin-top: 0.2rem;
        color: var(--text-dark);
    }

    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    .stat-icon-indigo { background: #e0e7ff; color: #4f46e5; }
    .stat-icon-emerald { background: #d1fae5; color: #059669; }
    .stat-icon-amber { background: #fef3c7; color: #d97706; }

    /* Table & Container */
    .table-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }

    .table-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
    }

    .table-card-header h5 {
        font-weight: 700;
        font-size: 1.05rem;
        color: var(--text-dark);
        margin: 0;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
    }

    .custom-table th {
        background: #f8fafc;
        padding: 0.85rem 1.25rem;
        font-size: 0.775rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border-color);
    }

    .custom-table td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.9rem;
    }

    .custom-table tbody tr:last-child td {
        border-bottom: none;
    }

    .custom-table tbody tr {
        transition: background-color 0.15s ease;
    }

    .custom-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Custom Badges & Avatars */
    .book-icon-box {
        width: 36px;
        height: 36px;
        background: #f1f5f9;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .member-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.775rem;
        flex-shrink: 0;
    }

    .status-pill {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        text-transform: capitalize;
    }

    .status-pill-returned {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .status-pill-borrowed {
        background: #fffbe3;
        color: #b45309;
        border: 1px solid #fde68a;
    }
</style>

<div class="container-fluid py-4 px-4">

    <!-- Header & Navigation -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="page-title mb-1">Borrow History</h2>
            <p class="page-subtitle mb-0">View complete logs of book borrowings and returns.</p>
        </div>
        <a href="../Borrow/index.php" class="btn-action-secondary">
           <i class="fa-solid fa-arrow-left"></i> Back to Borrowed Books
        </a>
    </div>

    <!-- Statistics Grid -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Total Transactions</div>
                    <div class="stat-number"><?= htmlspecialchars((string)$totalHistory); ?></div>
                </div>
                <div class="stat-icon-wrapper stat-icon-indigo">
                    <i class="fa-solid fa-list-check"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Returned Books</div>
                    <div class="stat-number"><?= htmlspecialchars((string)$totalReturned); ?></div>
                </div>
                <div class="stat-icon-wrapper stat-icon-emerald">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Active Borrowed</div>
                    <div class="stat-number"><?= htmlspecialchars((string)$totalActive); ?></div>
                </div>
                <div class="stat-icon-wrapper stat-icon-amber">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table Card -->
    <div class="table-card">
        <div class="table-card-header">
            <h5>All Activity Logs</h5>
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-monospace">
                <?= count($history) ?> records
            </span>
        </div>

        <div class="table-responsive">
            <table class="custom-table align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Book Title</th>
                        <th>Member</th>
                        <th>Borrow Date</th>
                        <th>Return Date</th>
                        <th class="pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($history)): ?>
                        <?php foreach ($history as $row): ?>
                            <?php $isReturned = strtolower($row['status']) === 'returned'; ?>
                            <tr>
                                <!-- ID -->
                                <td class="ps-4">
                                    <span class="fw-bold text-muted">#<?= htmlspecialchars((string)$row['id']); ?></span>
                                </td>

                                <!-- Book Info -->
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="book-icon-box">
                                            <i class="fa-solid fa-book"></i>
                                        </div>
                                        <span class="fw-bold text-dark"><?= htmlspecialchars($row['title']); ?></span>
                                    </div>
                                </td>

                                <!-- Member Info -->
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="member-avatar">
                                            <?= strtoupper(substr($row['member_name'], 0, 1)); ?>
                                        </div>
                                        <span class="fw-semibold text-dark"><?= htmlspecialchars($row['member_name']); ?></span>
                                    </div>
                                </td>

                                <!-- Borrow Date -->
                                <td>
                                    <span class="text-secondary fw-medium">
                                        <i class="fa-regular fa-calendar me-1 text-muted"></i>
                                        <?= !empty($row['borrow_date']) ? date('M d, Y', strtotime($row['borrow_date'])) : '—'; ?>
                                    </span>
                                </td>

                                <!-- Return Date -->
                                <td>
                                    <span class="text-secondary fw-medium">
                                        <?php if (!empty($row['return_date'])): ?>
                                            <i class="fa-regular fa-calendar-check me-1 text-emerald-600"></i>
                                            <?= date('M d, Y', strtotime($row['return_date'])); ?>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </span>
                                </td>

                                <!-- Status Badge -->
                                <td class="pe-4">
                                    <span class="status-pill <?= $isReturned ? 'status-pill-returned' : 'status-pill-borrowed'; ?>">
                                        <i class="fa-solid fa-circle" style="font-size: 5px;"></i>
                                        <?= htmlspecialchars(ucfirst($row['status'])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-history fa-2x mb-3 text-secondary" style="opacity: 0.5;"></i>
                                    <h6>No borrow history found</h6>
                                    <p class="small mb-0">Transactions will automatically appear here once activity starts.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>