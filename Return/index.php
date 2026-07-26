<?php include '../includes/header.php'; ?>

<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ----------------------------------------------------
// PROCESS RETURN ACTION WHEN BUTTON IS CLICKED
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'process_return') {
    $borrow_id = isset($_POST['borrow_id']) ? (int)$_POST['borrow_id'] : 0;

    if ($borrow_id > 0) {
        try {
            // Update borrowings status and set current date as return_date
            $updateSql = "
                UPDATE borrowings 
                SET status = 'returned', return_date = NOW() 
                WHERE id = :borrow_id AND status = 'borrowed'
            ";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->execute([':borrow_id' => $borrow_id]);

            // Optional: Update book status back to available
            $bookSql = "
                UPDATE books b
                JOIN borrowings br ON b.id = br.book_id
                SET b.status = 'available'
                WHERE br.id = :borrow_id
            ";
            $bookStmt = $conn->prepare($bookSql);
            $bookStmt->execute([':borrow_id' => $borrow_id]);

            $_SESSION['flash_message'] = "Book return completed successfully!";
            $_SESSION['flash_type'] = "success";
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = "Error updating return status: " . $e->getMessage();
            $_SESSION['flash_type'] = "danger";
        }
    }

    // Refresh page to show updated table
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ----------------------------------------------------
// FETCH ALL BORROW / RETURN RECORDS
// ----------------------------------------------------
$sql = "
    SELECT 
        b.id AS borrow_id,
        b.borrow_date,
        b.return_date,
        b.status,
        bk.title AS book_title,
        m.name AS member_name
    FROM borrowings b
    JOIN books bk ON b.book_id = bk.id
    JOIN members m ON b.member_id = m.id
    ORDER BY b.id DESC
";

$stmt = $conn->query($sql);
$returns = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
?>

<!-- Google Fonts & FontAwesome -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    :root {
        --primary: #6f42c1;
        --primary-hover: #5b2ef0;
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

    /* Card Container */
    .table-card {
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        overflow: hidden;
        background: var(--card-bg);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
    }

    .card-header-custom {
        background: var(--card-bg);
        border-bottom: 1px solid var(--border-color);
        padding: 1.25rem 1.75rem;
    }

    /* Table Design */
    .table-custom {
        margin-bottom: 0;
        vertical-align: middle;
    }

    .table-custom th {
        background-color: #f1f5f9;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: var(--text-muted);
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .table-custom td {
        padding: 1.1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-dark);
    }

    .table-custom tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Status Badges */
    .badge-status {
        padding: 0.4rem 0.85rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .badge-returned {
        background-color: #dcfce7;
        color: #15803d;
    }

    .badge-borrowed {
        background-color: #fef3c7;
        color: #b45309;
    }

    /* Buttons */
    .btn-action-primary {
        background: var(--primary);
        color: #ffffff !important;
        border: none;
        border-radius: var(--radius-md);
        padding: 0.65rem 1.25rem;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 12px rgba(111, 66, 193, 0.2);
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-action-primary:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
    }

    .btn-action-secondary {
        background: #ffffff;
        color: var(--text-dark) !important;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 0.65rem 1.25rem;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-action-secondary:hover {
        background: #f1f5f9;
    }

    .btn-sm-action {
        padding: 0.35rem 0.75rem;
        font-size: 0.8rem;
        border-radius: var(--radius-sm);
    }
</style>

<div class="container-fluid py-4 px-4">

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info'; ?> alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="fa-solid fa-circle-info me-2"></i>
            <?= htmlspecialchars($_SESSION['flash_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php 
            unset($_SESSION['flash_message']); 
            unset($_SESSION['flash_type']); 
        ?>
    <?php endif; ?>

    <!-- Header & Quick Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="page-title mb-1">Book Returns</h2>
            <p class="page-subtitle mb-0">Track and manage returned library materials.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="../dashboard/index.php" class="btn-action-secondary">
               <i class="fa-solid fa-house"></i> Dashboard
            </a>
            <a href="return_book.php" class="btn-action-primary">
                <i class="fa-solid fa-rotate-left"></i> Process Return
            </a>
        </div>
    </div>

    <!-- Returns Table Card -->
    <div class="card table-card">
        <div class="card-header-custom d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-clock-rotate-left text-muted me-2"></i> Return Records</h5>
            <span class="badge bg-light text-dark border"><?= count($returns); ?> Total Items</span>
        </div>

        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Book Title</th>
                        <th>Member Name</th>
                        <th>Borrow Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($returns)): ?>
                        <?php foreach ($returns as $row): ?>
                            <tr>
                                <td class="fw-bold text-muted">#<?= $row['borrow_id']; ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['book_title']); ?></div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-regular fa-user text-muted"></i>
                                        <span><?= htmlspecialchars($row['member_name']); ?></span>
                                    </div>
                                </td>
                                <td><?= date('M d, Y', strtotime($row['borrow_date'])); ?></td>
                                <td>
                                    <?= $row['return_date'] ? date('M d, Y', strtotime($row['return_date'])) : '<span class="text-muted">—</span>'; ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] === 'returned'): ?>
                                        <span class="badge-status badge-returned">
                                            <i class="fa-solid fa-circle-check"></i> Returned
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-status badge-borrowed">
                                            <i class="fa-solid fa-clock"></i> Borrowed
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($row['status'] === 'borrowed'): ?>
                                        <!-- Form to submit return action directly -->
                                        <form method="POST" style="display: inline-block;" onsubmit="return confirm('Mark this book as returned?');">
                                            <input type="hidden" name="action" value="process_return">
                                            <input type="hidden" name="borrow_id" value="<?= $row['borrow_id']; ?>">
                                            <button type="submit" class="btn-action-primary btn-sm-action">
                                                <i class="fa-solid fa-arrow-rotate-left"></i> Return
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted small"><i class="fa-solid fa-check text-success me-1"></i> Completed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fa-solid fa-box-open text-muted fs-2 mb-2 d-block"></i>
                                <p class="text-muted mb-0">No return records found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>