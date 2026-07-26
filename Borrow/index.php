<?php 
session_start();
include '../includes/header.php'; 

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

// --- HANDLE FORM SUBMISSIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Action 1: Issue a new book (Unlimited issuance per book allowed)
    if (isset($_POST['action']) && $_POST['action'] === 'issue_book') {
        $book_id = (int)($_POST['book_id'] ?? 0);
        $member_id = (int)($_POST['member_id'] ?? 0);
        $return_date = $_POST['return_date'] ?? '';
        $status = $_POST['status'] ?? 'issued';
        $borrow_date = date('Y-m-d H:i:s');

        // Allowed statuses to prevent invalid inputs
        $allowed_statuses = ['borrowed', 'issued', 'pending', 'returned'];
        if (!in_array($status, $allowed_statuses)) {
            $status = 'issued';
        }

        if ($book_id > 0 && $member_id > 0 && !empty($return_date)) {
            try {
                // Directly insert without checking if book is already borrowed
                $stmt = $conn->prepare("INSERT INTO borrowings (book_id, member_id, borrow_date, return_date, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$book_id, $member_id, $borrow_date, $return_date, $status]);
                
                $_SESSION['message'] = "Book successfully recorded with status: " . htmlspecialchars($status) . "!";
                $_SESSION['message_type'] = "success";
            } catch (PDOException $e) {
                $_SESSION['message'] = "Database error: " . $e->getMessage();
                $_SESSION['message_type'] = "danger";
            }
        } else {
            $_SESSION['message'] = "Please fill in all required fields.";
            $_SESSION['message_type'] = "warning";
        }

        // Perform HTTP Redirect to clear POST payload
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Action 2: Return/Check in a book
    if (isset($_POST['action']) && $_POST['action'] === 'return_book') {
        $borrowing_id = (int)($_POST['borrowing_id'] ?? 0);
        if ($borrowing_id > 0) {
            try {
                $stmt = $conn->prepare("UPDATE borrowings SET status = 'returned' WHERE id = ?");
                $stmt->execute([$borrowing_id]);

                $_SESSION['message'] = "Book marked as returned!";
                $_SESSION['message_type'] = "success";
            } catch (PDOException $e) {
                $_SESSION['message'] = "Database error: " . $e->getMessage();
                $_SESSION['message_type'] = "danger";
            }
        }

        // Perform HTTP Redirect to clear POST payload
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Retrieve flash message from session, then clear it
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);

// --- DATA FETCHING ---
// Fetch books list for issue modal dropdown
$booksStmt = $conn->query("SELECT id, title FROM books ORDER BY title ASC");
$available_books = $booksStmt ? $booksStmt->fetchAll(PDO::FETCH_ASSOC) : [];

// Fetch members list for issue modal dropdown
$membersStmt = $conn->query("SELECT id, name FROM members ORDER BY name ASC");
$active_members = $membersStmt ? $membersStmt->fetchAll(PDO::FETCH_ASSOC) : [];

// Fetch active borrowings (includes 'borrowed', 'issued', and 'pending' statuses)
$sql = "SELECT br.*, b.title, m.name AS member_name
        FROM borrowings br
        JOIN books b ON br.book_id = b.id
        JOIN members m ON br.member_id = m.id
        WHERE br.status IN ('borrowed', 'issued', 'pending')
        ORDER BY br.borrow_date DESC";

$stmt = $conn->query($sql);
$borrowings = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

// Statistics queries
$totalCountStmt = $conn->query("SELECT COUNT(*) FROM borrowings WHERE status IN ('borrowed', 'issued', 'pending')");
$totalBorrowed = $totalCountStmt ? $totalCountStmt->fetchColumn() : 0;

$dueTodayStmt = $conn->query("SELECT COUNT(*) FROM borrowings WHERE status IN ('borrowed', 'issued', 'pending') AND DATE(return_date) = CURDATE()");
$dueToday = $dueTodayStmt ? $dueTodayStmt->fetchColumn() : 0;
?>

<!-- Bootstrap 5 CSS & CDN Dependencies -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
    .btn-action-primary {
        background: var(--primary);
        color: #ffffff !important;
        border: none;
        border-radius: var(--radius-md);
        padding: 0.65rem 1.25rem;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-action-primary:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);
    }

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

    /* Metric Stat Cards */
    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
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
        font-size: 1.85rem;
        font-weight: 800;
        margin-top: 0.2rem;
        color: var(--text-dark);
    }

    .stat-icon-wrapper {
        width: 52px;
        height: 52px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .stat-icon-purple {
        background: #e0e7ff;
        color: #4f46e5;
    }

    .stat-icon-amber {
        background: #fef3c7;
        color: #d97706;
    }

    /* Table Container & Layout */
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

    /* Book & Member Badges/Avatars */
    .book-info {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .book-icon-box {
        width: 38px;
        height: 38px;
        background: #f1f5f9;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    .member-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
        flex-shrink: 0;
    }

    /* Badges & Action Button */
    .status-badge-borrowed {
        background: #fffbe3;
        color: #b45309;
        border: 1px solid #fde68a;
        padding: 0.3rem 0.75rem;
        border-radius: 20px;
        font-size: 0.775rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        text-transform: capitalize;
    }

    .status-badge-issued {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
        padding: 0.3rem 0.75rem;
        border-radius: 20px;
        font-size: 0.775rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        text-transform: capitalize;
    }

    .status-badge-pending {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        padding: 0.3rem 0.75rem;
        border-radius: 20px;
        font-size: 0.775rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        text-transform: capitalize;
    }

    .status-badge-returned {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        padding: 0.3rem 0.75rem;
        border-radius: 20px;
        font-size: 0.775rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        text-transform: capitalize;
    }

    .btn-checkin {
        background: #f1f5f9;
        color: var(--text-dark) !important;
        border: 1px solid var(--border-color);
        padding: 0.45rem 0.9rem;
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: 0.825rem;
        text-decoration: none !important;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
    }

    .btn-checkin:hover {
        background: #10b981;
        color: #ffffff !important;
        border-color: #10b981;
    }
</style>

<div class="container-fluid py-4 px-4">

    <!-- Flash Alert Notification -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $message_type; ?> alert-dismissible fade show mb-4" role="alert">
            <?= htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Header & Action Controls -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="page-title mb-1">Borrowed Books</h2>
            <p class="page-subtitle mb-0">Track active checkouts and manage book returns.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="../index.php" class="btn-action-secondary">
               <i class="fa-solid fa-house"></i> Dashboard
            </a>
            
            <a href="borrow_history.php" class="btn-action-secondary">
               <i class="fa-solid fa-clock-rotate-left"></i> View History
            </a>

            <!-- Trigger Button for Modal -->
            <button type="button" class="btn-action-primary" data-bs-toggle="modal" data-bs-target="#issueBookModal">
               <i class="fa-solid fa-plus"></i> Issue New Book
            </button>
        </div>
    </div>

    <!-- Statistics Overview Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Active Records</div>
                    <div class="stat-number"><?= htmlspecialchars((string)$totalBorrowed); ?></div>
                </div>
                <div class="stat-icon-wrapper stat-icon-purple">
                    <i class="fa-solid fa-book-reader"></i>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Due Today</div>
                    <div class="stat-number"><?= htmlspecialchars((string)$dueToday); ?></div>
                </div>
                <div class="stat-icon-wrapper stat-icon-amber">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Borrowed Books Table Card -->
    <div class="table-card">
        <div class="table-card-header">
            <h5>Currently Checked Out</h5>
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-monospace">
                <?= count($borrowings) ?> records
            </span>
        </div>

        <div class="table-responsive">
            <table class="custom-table align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Book Title</th>
                        <th>Borrowed By</th>
                        <th>Borrow Date</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($borrowings)): ?>
                        <?php foreach ($borrowings as $row): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-muted">#<?= htmlspecialchars((string)$row['id']); ?></span>
                                </td>
                                <td>
                                    <div class="book-info">
                                        <div class="book-icon-box">
                                            <i class="fa-solid fa-book"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($row['title']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="member-avatar">
                                            <?= strtoupper(substr($row['member_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($row['member_name']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-secondary fw-medium">
                                        <i class="fa-regular fa-calendar me-1 text-muted"></i>
                                        <?= !empty($row['borrow_date']) ? date('M d, Y', strtotime($row['borrow_date'])) : '—'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['status'] === 'issued'): ?>
                                        <span class="status-badge-issued">
                                            <i class="fa-solid fa-circle" style="font-size: 6px;"></i> Issued
                                        </span>
                                    <?php elseif ($row['status'] === 'pending'): ?>
                                        <span class="status-badge-pending">
                                            <i class="fa-solid fa-circle" style="font-size: 6px;"></i> Pending
                                        </span>
                                    <?php elseif ($row['status'] === 'returned'): ?>
                                        <span class="status-badge-returned">
                                            <i class="fa-solid fa-circle" style="font-size: 6px;"></i> Returned
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge-borrowed">
                                            <i class="fa-solid fa-circle" style="font-size: 6px;"></i> Borrowed
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <form method="POST" action="" class="d-inline" onsubmit="return confirm('Are you sure you want to mark this book as returned?');">
                                        <input type="hidden" name="action" value="return_book">
                                        <input type="hidden" name="borrowing_id" value="<?= $row['id']; ?>">
                                        <button type="submit" class="btn-checkin">
                                            <i class="fa-solid fa-arrow-down-to-bracket"></i> Check In
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-box-open fa-2x mb-3 text-secondary" style="opacity: 0.5;"></i>
                                    <h6>No active records found</h6>
                                    <p class="small mb-0">Issued or borrowed books will appear here automatically.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Issue Book Modal -->
<div class="modal fade" id="issueBookModal" tabindex="-1" aria-labelledby="issueBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="issueBookModalLabel">
                    <i class="fa-solid fa-book-medical me-2 text-primary"></i>Issue / Assign Book
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="issue_book">
                    
                    <!-- Select Book -->
                    <div class="mb-3">
                        <label for="book_id" class="form-label fw-semibold">Select Book</label>
                        <select class="form-select py-2" id="book_id" name="book_id" required>
                            <option value="" disabled selected>-- Choose a book --</option>
                            <?php foreach ($available_books as $book): ?>
                                <option value="<?= $book['id']; ?>"><?= htmlspecialchars($book['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Select Member -->
                    <div class="mb-3">
                        <label for="member_id" class="form-label fw-semibold">Select Member</label>
                        <select class="form-select py-2" id="member_id" name="member_id" required>
                            <option value="" disabled selected>-- Choose a member --</option>
                            <?php foreach ($active_members as $member): ?>
                                <option value="<?= $member['id']; ?>"><?= htmlspecialchars($member['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Status Selection -->
                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold">Assign Status</label>
                        <select class="form-select py-2" id="status" name="status" required>
                            <option value="issued" selected>Issued</option>
                            <option value="borrowed">Borrowed</option>
                            <option value="pending">Pending</option>
                            <option value="returned">Returned</option>
                        </select>
                    </div>

                    <!-- Due Return Date -->
                    <div class="mb-3">
                        <label for="return_date" class="form-label fw-semibold">Return Due Date</label>
                        <input type="date" class="form-control py-2" id="return_date" name="return_date" required min="<?= date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-action-primary">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JavaScript Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>

<?php include '../includes/footer.php'; ?>