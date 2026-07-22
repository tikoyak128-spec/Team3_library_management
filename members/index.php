<?php include '../includes/header.php'; ?>

<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

$result = $conn->query("SELECT * FROM members ORDER BY id DESC");
$totalMembers = $conn->query("SELECT COUNT(*) as total FROM members")->fetch_assoc()['total'];
?>

<style>
    :root{
        --primary:#6f42c1;
        --secondary:#8B5CF6;
        --bg:#F5F6FA;
    }

    body{
        background:var(--bg);
    }

    .page-title{
        font-weight:700;
        color:#111827;
    }

    .page-subtitle{
        color:#6B7280;
    }

    .btn-purple{
        background:#6f42c1;
        color:#fff;
        border:none;
        border-radius:12px;
        padding:12px 20px;
        font-weight:600;
    }

    .btn-purple:hover{
        background:#5B2EF0;
        color:#fff;
    }

    .stat-card{
        border:none;
        border-radius:20px;
        background:#fff;
        box-shadow:0 4px 15px rgba(0,0,0,.05);
    }

    .member-card{
        border:none;
        border-radius:20px;
        overflow:hidden;
        background:#fff;
        box-shadow:0 4px 15px rgba(0,0,0,.05);
    }

    .member-header{
        background:#6f42c1;
        color:white;
        padding:20px;
    }

    .table{
        margin-bottom:0;
    }

    .table thead th{
        font-weight:600;
        border-bottom:1px solid #e5e7eb;
    }

    .table tbody tr:hover{
        background:#faf7ff;
    }

    .btn-edit{
        text-decoration: none; 
        background:#d1e7dd;
        color:#0f5132;
        padding:8px 12px;
        border: none;
        border-radius:10px;
        font-size:12px;
        font-weight:600;
    }

    .btn-delete{
        background:#fff3cd;
        color:#856404;
        padding:8px 12px;
        border: none;
        border-radius:10px;
        font-size:12px;
        font-weight:600;
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title">Members Management</h2>
            <p class="page-subtitle mb-0">
                Manage all library members
            </p>
        </div>

        <a href="create.php" class="btn btn-purple">
            + Add Member
        </a>
    </div>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">
                            Total Members
                        </small>

                        <h1 class="mt-2 mb-0">
                            <?= $totalMembers ?>
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card member-card">
        <div class="member-header">
            <h5 class="mb-0">
                Member List
            </h5>
        </div>

        <div class="card-body p-0">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($result && $result->num_rows > 0): ?>

                    <?php while ($row = $result->fetch_assoc()): ?>

                    <tr>
                        <td class="text-center">
                            <strong><?= $row['id']; ?></strong>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <span>
                                    <?= htmlspecialchars($row['name']); ?>
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="text-secondary"><?= !empty($row['phone']) ? htmlspecialchars($row['phone']) : '—'; ?></span>
                        </td>

                        <td>
                            <span class="text-secondary"><?= !empty($row['email']) ? htmlspecialchars($row['email']) : '—'; ?></span>
                        </td>

                        <td class="text-center">
                            <a href="edit.php?id=<?= $row['id']; ?>"class="btn btn-outline-primary btn-sm btn-edit">
                                Edit
                            </a>
                            <a href="delete.php?id=<?= $row['id']; ?>" class="btn btn-outline-danger btn-sm btn-delete" onclick="return confirm('Delete this member?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <h6 class="text-muted">
                                No members found
                            </h6>
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>