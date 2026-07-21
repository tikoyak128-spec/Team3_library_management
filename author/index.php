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

.stat-icon{
    width:60px;
    height:60px;
    border-radius:15px;
    background:rgba(108,62,244,.1);
    color:var(--primary);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:28px;
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

.author-avatar{
    width:42px;
    height:42px;
    border-radius:50%;
    background:#ede9fe;
    color:#6C3EF4;
    font-weight:bold;
    display:flex;
    align-items:center;
    justify-content:center;
}

.table tbody tr:hover{
    background:#f8f7ff;
}

.action-btn{
    border-radius:10px;
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

    <!-- Statistics Card -->

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

                    <div class="stat-icon">
                        ✍️
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
                        <th>ID</th>
                        <th>Author</th>
                        <th class="text-end">Actions</th>
                    </tr>
              </thead>
              <tbody>

                <?php if ($result && $result->num_rows > 0): ?>

                    <?php while ($row = $result->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <strong><?= $row['id']; ?></strong>
                        </td>

                        <td>
                            <div class="d-flex align-items-center">

                                <div class="author-avatar me-3">
                                    <?= strtoupper(substr($row['name'],0,1)); ?>
                                </div>

                                <strong>
                                    <?= htmlspecialchars($row['name']); ?>
                                </strong>

                            </div>
                        </td>

                        <td class="text-end">
                            <a href="edit.php?id=<?= $row['id']; ?>"
                               class="btn btn-outline-primary btn-sm action-btn">
                                Edit
                            </a>

                            <a href="delete.php?id=<?= $row['id']; ?>"
                               class="btn btn-outline-danger btn-sm action-btn"
                               onclick="return confirm('Delete this author?')">
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