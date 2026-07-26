<?php include '../includes/header.php'; ?>

<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

// Fetch members data cleanly using PDO
$sql = "SELECT * FROM members ORDER BY id DESC";
$stmt = $conn->query($sql);
$members = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

// Fetch metric stats
$totalMembersStmt = $conn->query("SELECT COUNT(*) as total FROM members");
$totalMembers = $totalMembersStmt ? $totalMembersStmt->fetchColumn() : 0;
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

    /* Primary & Secondary Action Buttons */
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
        box-shadow: 0 4px 12px rgba(111, 66, 193, 0.25);
        transition: all 0.2s ease;
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

    /* Metric Stat Card */
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
        background: #f3e8ff;
        color: var(--primary);
    }

    /* Table Container & Custom Styling */
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
        background-color: #faf7ff;
    }

    /* User Avatar & Styling */
    .member-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #f3e8ff;
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    /* Table Action Buttons */
    .btn-edit {
        background: #e0f2fe;
        color: #0369a1 !important;
        padding: 0.45rem 0.85rem;
        border-radius: var(--radius-sm);
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: all 0.2s ease;
    }

    .btn-edit:hover {
        background: #0284c7;
        color: #ffffff !important;
    }

    .btn-delete {
        background: #ffe4e6;
        color: #be123c !important;
        padding: 0.45rem 0.85rem;
        border-radius: var(--radius-sm);
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: all 0.2s ease;
    }

    .btn-delete:hover {
        background: #e11d48;
        color: #ffffff !important;
    }
</style>

<div class="container-fluid py-4 px-4">

    <!-- Header & Action Controls -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="page-title mb-1">Members Management</h2>
            <p class="page-subtitle mb-0">View, create, and manage registered library members.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Back to Dashboard Link -->
            <a href="../index.php" class="btn-action-secondary">
               <i class="fa-solid fa-house"></i> Dashboard
            </a>

            <!-- Add Member CTA -->
            <a href="create.php" class="btn-action-primary">
               <i class="fa-solid fa-user-plus"></i> Add Student
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Total Members</div>
                    <div class="stat-number"><?= htmlspecialchars((string)$totalMembers); ?></div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Members Table Card -->
    <div class="table-card">
        <div class="table-card-header">
            <h5>Registered Members</h5>
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-monospace">
                <?= count($members) ?> records
            </span>
        </div>

        <div class="table-responsive">
            <table class="custom-table align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($members)): ?>
                        <?php foreach ($members as $row): ?>
                            <tr>
                                <!-- ID Column -->
                                <td class="ps-4">
                                    <span class="fw-bold text-muted">#<?= htmlspecialchars((string)$row['id']); ?></span>
                                </td>

                                <!-- Name Column with Initial Avatar -->
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="member-avatar">
                                            <?= strtoupper(substr($row['name'] ?? 'M', 0, 1)); ?>
                                        </div>
                                        <div class="fw-bold text-dark">
                                            <?= htmlspecialchars($row['name'] ?? 'N/A'); ?>
                                        </div>
                                    </div>
                                </td>

                                <!-- Phone Column -->
                                <td>
                                    <span class="text-secondary fw-medium">
                                        <i class="fa-solid fa-phone me-1 text-muted" style="font-size: 0.8rem;"></i>
                                        <?= htmlspecialchars($row['phone'] ?? '—'); ?>
                                    </span>
                                </td>

                                <!-- Email Column -->
                                <td>
                                    <span class="text-secondary fw-medium">
                                        <i class="fa-regular fa-envelope me-1 text-muted" style="font-size: 0.85rem;"></i>
                                        <?= htmlspecialchars($row['email'] ?? '—'); ?>
                                    </span>
                                </td>

                                <!-- Action Buttons -->
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-2">
                                        <a href="edit.php?id=<?= $row['id']; ?>" class="btn-edit">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>
                                        <a href="delete.php?id=<?= $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this member?');">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-user-slash fa-2x mb-3 text-secondary" style="opacity: 0.5;"></i>
                                    <h6>No members found</h6>
                                    <p class="small mb-0">Click "+ Add Member" above to create your first member record.</p>
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