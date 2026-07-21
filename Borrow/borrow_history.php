<?php include '../includes/header.php'; ?>

<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

$sql = "SELECT br.*, b.title, m.name AS member_name
        FROM borrowings br
        JOIN books b ON br.book_id = b.id
        JOIN members m ON br.member_id = m.id
        ORDER BY br.borrow_date DESC, br.id DESC";

$result = $conn->query($sql);

$totalHistory = $conn->query("
    SELECT COUNT(*) AS total
    FROM borrowings
")->fetch_assoc()['total'];
?>

<style>
:root{
    --primary:#6C3EF4;
    --bg:#F7F8FC;
}

body{
    background:var(--bg);
}

.history-card{
    border:none;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 5px 20px rgba(0,0,0,.06);
}

.history-header{
    background:linear-gradient(135deg,#6C3EF4,#8B5CF6);
    color:white;
    padding:20px;
}

.stat-card{
    border:none;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,.06);
}

.btn-purple{
    background:#6C3EF4;
    color:white;
    border:none;
    border-radius:12px;
    padding:10px 18px;
    font-weight:600;
}

.btn-purple:hover{
    background:#5B2EF0;
    color:white;
}

.history-badge-returned{
    background:#d1e7dd;
    color:#0f5132;
    padding:8px 12px;
    border-radius:10px;
    font-size:12px;
    font-weight:600;
}

.history-badge-borrowed{
    background:#fff3cd;
    color:#856404;
    padding:8px 12px;
    border-radius:10px;
    font-size:12px;
    font-weight:600;
}

.table tbody tr:hover{
    background:#f8f7ff;
}
</style>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">Borrow History</h2>
            <small class="text-muted">
                View all borrowing and return transactions
            </small>
        </div>

        <a href="../Borrow/index.php"
           class="btn btn-purple">
            ← Back to Borrowed Books
        </a>

    </div>
    <div class="row mb-4">

        <div class="col-md-4">
            <div class="card stat-card p-4">
                <small class="text-muted">
                    Total Transactions
                </small>

                <h2 class="mt-2 mb-0">
                    <?= $totalHistory ?>
                </h2>
            </div>
        </div>

    </div>
    <div class="card history-card">

        <div class="history-header">
            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    Borrowing History
                </h5>
            </div>
        </div>

        <div class="card-body">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Book</th>
                        <th>Member</th>
                        <th>Borrow Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
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
                                <img src="your-image.jpg" alt="Icon"  width="20" height="20" class="me-2" />
                                <?= htmlspecialchars($row['title']); ?>
                            </td>

                            <td>
                                <span class="text-muted"><?= htmlspecialchars($row['member_name']); ?></span> 
                            </td>

                            <td>
                               <span class="text-muted"><?= date('d M Y', strtotime($row['borrow_date'])); ?></span> 
                            </td>

                            <td>
                                <?php if(!empty($row['return_date'])): ?>
                                    <span class="text-muted"><?= date('d M Y', strtotime($row['return_date'])); ?></span> 
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>

                            </td>

                            <td>
                                <?php if($row['status'] === 'returned'): ?>
                                    <span class="history-badge-returned">
                                        ✓ Returned
                                    </span>

                                <?php else: ?>
                                    <span class="history-badge-borrowed">
                                        Borrowed
                                    </span>
                                <?php endif; ?>
                            </td>

                        </tr>
                        <?php endwhile; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                No borrow history found.
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>