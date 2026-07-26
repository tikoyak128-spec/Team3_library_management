<?php include '../includes/header.php'; ?>

<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

// Fetch available books safely using PDO
$booksStmt = $conn->query("
    SELECT id, title, quantity
    FROM books
    WHERE status='available'
    AND quantity > 0
    ORDER BY title
");
$books = $booksStmt ? $booksStmt->fetchAll(PDO::FETCH_ASSOC) : [];

// Fetch members safely using PDO
$membersStmt = $conn->query("
    SELECT id, name
    FROM members
    ORDER BY name
");
$members = $membersStmt ? $membersStmt->fetchAll(PDO::FETCH_ASSOC) : [];
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
    .borrow-card {
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        overflow: hidden;
        background: var(--card-bg);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
    }

    .card-header-custom {
        background: var(--primary);
        color: white;
        padding: 1.75rem 2rem;
    }

    .header-icon-box {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(4px);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #ffffff;
    }

    /* Form Inputs */
    .form-label {
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    .input-group-custom {
        position: relative;
    }

    .input-group-custom i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 0.95rem;
        z-index: 10;
        pointer-events: none;
    }

    .form-control-custom, 
    .form-select-custom {
        height: 48px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        padding-left: 2.75rem;
        padding-right: 1rem;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-dark);
        background-color: #ffffff;
        transition: all 0.2s ease;
    }

    .form-control-custom:focus,
    .form-select-custom:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(111, 66, 193, 0.12);
        outline: none;
    }

    /* Buttons */
    .btn-action-primary {
        background: var(--primary);
        color: #ffffff !important;
        border: none;
        border-radius: var(--radius-md);
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 12px rgba(111, 66, 193, 0.25);
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-action-primary:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(111, 66, 193, 0.35);
    }

    .btn-action-secondary {
        background: #ffffff;
        color: var(--text-dark) !important;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 0.75rem 1.5rem;
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
</style>

<div class="container-fluid py-4 px-4">

    <!-- Top Navigation Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="page-title mb-1">New Borrowing</h2>
            <p class="page-subtitle mb-0">Issue a book to a registered library member.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="../index.php" class="btn-action-secondary">
               <i class="fa-solid fa-house"></i> Dashboard
            </a>
            <a href="index.php" class="btn-action-secondary">
               <i class="fa-solid fa-arrow-left"></i> Borrowings List
            </a>
        </div>
    </div>

    <!-- Form Section -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card borrow-card">
                <div class="card-header-custom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="header-icon-box">
                            <i class="fa-solid fa-book-bookmark"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">Borrow Transaction</h4>
                            <small class="opacity-75">Select the book and member to complete issuance</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">

                    <form action="borrow_controller.php" method="POST">
                        <input type="hidden" name="action" value="create">

                        <!-- Select Book -->
                        <div class="mb-4">
                            <label class="form-label">Book <span class="text-danger">*</span></label>
                            <div class="input-group-custom">
                                <i class="fa-solid fa-book"></i>
                                <select name="book_id" class="form-select form-select-custom" required>
                                    <option value="">Select a Book</option>
                                    <?php foreach ($books as $b): ?>
                                        <option value="<?= $b['id']; ?>">
                                            <?= htmlspecialchars($b['title']); ?> (Available: <?= $b['quantity']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Select Member -->
                        <div class="mb-4">
                            <label class="form-label">Member <span class="text-danger">*</span></label>
                            <div class="input-group-custom">
                                <i class="fa-solid fa-user"></i>
                                <select name="member_id" class="form-select form-select-custom" required>
                                    <option value="">Select a Member</option>
                                    <?php foreach ($members as $m): ?>
                                        <option value="<?= $m['id']; ?>">
                                            <?= htmlspecialchars($m['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Borrow Date -->
                        <div class="mb-4">
                            <label class="form-label">Borrow Date <span class="text-danger">*</span></label>
                            <div class="input-group-custom">
                                <i class="fa-regular fa-calendar-days"></i>
                                <input type="date" name="borrow_date" class="form-control form-control-custom" value="<?= date('Y-m-d'); ?>" required>
                            </div>
                        </div>

                        <hr class="my-4" style="border-color: var(--border-color);">

                        <!-- Action Controls -->
                        <div class="d-flex align-items-center gap-2 justify-content-end">
                            <a href="index.php" class="btn-action-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn-action-primary">
                                <i class="fa-solid fa-check"></i> Borrow Book
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>