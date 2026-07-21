<?php include '../includes/header.php'; ?>

<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

$result = $conn->query("SELECT * FROM authors ORDER BY id DESC");
$totalAuthors = $conn->query("SELECT COUNT(*) as total FROM authors")->fetch_assoc()['total'];
?>

<style>
    :root{
        --primary:#6C3EF4;
        --secondary:#8B5CF6;
        --bg:#F7F8FC;
    }

    body{
        background:var(--bg);
    }

    .page-title{
        font-weight:700;
        color:#222;
    }

    .page-subtitle{
        color:#6c757d;
    }

    .btn-purple{
        background:var(--primary);
        color:white;
        border:none;
        border-radius:12px;
        padding:10px 18px;
        font-weight:600;
    }

    .btn-purple:hover{
        background:#5b2ef0;
        color:white;
    }

    .stat-card{
        border:none;
        border-radius:20px;
        box-shadow:0 5px 20px rgba(0,0,0,.06);
    }

    .author-card{
        border:none;
        border-radius:20px;
        overflow:hidden;
        box-shadow:0 5px 20px rgba(0,0,0,.06);
    }

    .author-header{
        background:linear-gradient(135deg,#6C3EF4,#8B5CF6);
        color:white;
        padding:20px;
    }

    .table tbody tr:hover{
        background:#f8f7ff;
    }

    .btn-outline-primary{
        text-decoration: none; 
        background:#d1e7dd;
        color:#0f5132;
        padding:8px 12px;
        border: none;
        border-radius:10px;
        font-size:12px;
        font-weight:600;
    }

    .btn-outline-danger{
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
            <h2 class="page-title">Authors Management</h2>
            <p class="page-subtitle mb-0">
                Manage all authors in your library
            </p>
        </div>

        <a href="create.php" class="btn btn-purple">
            + Add Author
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">
                            Total Authors
                        </small>

                        <h2 class="mt-2 mb-0">
                            <?= $totalAuthors ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card author-card">
        <div class="author-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Author List
                </h5>
            </div>
        </div>

        <div class="card-body">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-start">Author</th>
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

                            <td class="text-start">
                                <div class="d-flex align-items-center">
                                        <?= htmlspecialchars($row['name']); ?>
                                </div>
                            </td>

                            <td class="text-center gap-5">
                                <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-outline-primary btn-sm action-btn">
                                    Edit
                                </a>

                                <a href="delete.php?id=<?= $row['id']; ?>" class="btn btn-outline-danger btn-sm action-btn" onclick="return confirm('Delete this author?')">
                                    Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <h6 class="text-muted">
                                    No authors found
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