<?Php include '../includes/header.php'; ?>
<?php
    define('BASE_PATH', dirname(__DIR__));
    require_once BASE_PATH . '/Database/db.php';

    $sql = "SELECT br.*, b.title, m.name AS member_name
            FROM borrowings br
            JOIN books b ON br.book_id = b.id
            JOIN members m ON br.member_id = m.id
            WHERE br.status='borrowed'
            ORDER BY br.borrow_date DESC";

    $result = $conn->query($sql);

    $totalBorrowed = $conn->query("
        SELECT COUNT(*) as total
        FROM borrowings
        WHERE status='borrowed'
    ")->fetch_assoc()['total'];
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
    }

    .page-subtitle{
        color:#6c757d;
    }

    .btn-purple{
        background:var(--primary);
        border:none;
        color:white;
        border-radius:12px;
        padding:10px 20px;
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
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:28px;
    }

    .borrow-card{
        border:none;
        border-radius:20px;
        overflow:hidden;
        box-shadow:0 5px 20px rgba(0,0,0,.06);
    }

    .borrow-header{
        background:linear-gradient(135deg,#6C3EF4,#8B5CF6);
        color:white;
        padding:20px;
    }

    .table tbody tr:hover{
        background:#f8f7ff;
    }
    .history-badge-returned{
        text-decoration: none; 
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
</style>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title">Borrowed Books</h2>
            <p class="page-subtitle mb-0">
                Manage currently borrowed books
            </p>
        </div>
        <div>
            <a href="borrow_history.php" class="btn btn-purple">
               View Histroy
            </a>
            <a href="borrow_book.php" class="btn btn-purple">
                + Borrow a Book
            </a>
        </div>
    </div>

    <!-- Statistics Card -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">
                            Currently Borrowed
                        </small>

                        <h2 class="mt-2 mb-0">
                            <?= $totalBorrowed ?>
                        </h2>
                    </div>

                    <div class="stat-icon d-flex justify-content-center align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" 
                            viewBox="0 0 640 640" 
                            width="30" height="30"
                            fill="currentColor" 
                            class="text-primary">
                            <path d="M480 576L192 576C139 576 96 533 96 480L96 160C96 107 139 64 192 64L496 64C522.5 64 544 85.5 544 112L544 400C544 420.9 530.6 438.7 512 445.3L512 512C529.7 512 544 526.3 544 544C544 561.7 529.7 576 512 576L480 576zM192 448C174.3 448 160 462.3 160 480C160 497.7 174.3 512 192 512L448 512L448 448L192 448zM224 216C224 229.3 234.7 240 248 240L424 240C437.3 240 448 229.3 448 216C448 202.7 437.3 192 424 192L248 192C234.7 192 224 202.7 224 216zM248 288C234.7 288 224 298.7 224 312C224 325.3 234.7 336 248 336L424 336C437.3 336 448 325.3 448 312C448 298.7 437.3 288 424 288L248 288z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Borrow Table -->

    <div class="card borrow-card">
        <div class="borrow-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Borrowed Books List
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
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>

                    <tr>
                        <td>
                            <strong class="text-dark"><?= $row['id']; ?></strong>
                        </td>

                        <td>
                            <img src="your-image.jpg" 
                                alt="Icon" 
                                width="20" height="20" 
                                class="me-2" />
                            <span class="text-dark">
                                <?= htmlspecialchars($row['title']); ?>
                            </span>
                        </td>

                        <td>
                            <div class="d-flex align-items-center">
                                <span class="text-muted"><?= htmlspecialchars($row['member_name']); ?></span>
                            </div>
                        </td>

                        <!-- Borrow date -->
                        <td>
                            <span class="text-muted"><?= date('d M Y', strtotime($row['borrow_date'])); ?></span>
                        </td>

                        <td class="text-start">
                            <span class="history-badge-borrowed">
                                Borrowed
                            </span>
                        </td>

                        <td class="text-end">
                            <a href="../Return/return_book.php?id=<?= $row['id']; ?>" class="history-badge-returned">
                                Return
                            </a>
                        </td>

                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>

                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <h6 class="text-muted">
                                    No books currently borrowed
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