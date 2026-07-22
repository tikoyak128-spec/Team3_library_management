<?php include '../includes/header.php'; ?>

<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT br.*, b.title, m.name AS member_name
        FROM borrowings br
        JOIN books b ON br.book_id = b.id
        JOIN members m ON br.member_id = m.id
        WHERE br.id = ? AND br.status='borrowed'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$borrow = $stmt->get_result()->fetch_assoc();

if (!$borrow) {
    echo '<div class="alert alert-danger">Record not found or already returned.</div>';
    include '../includes/footer.php';
    exit;
}
?>

<style>
:root{
    --primary:#6f42c1;
    --secondary:#8B5CF6;
    --bg:#F7F8FC;
}

body{
    background:var(--bg);
}

.return-card{
    border:none;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
}

.card-header-custom{
    background:#6f42c1;
    color:white;
    padding:25px;
}

.form-label{
    font-weight:600;
    margin-bottom:8px;
}

.form-control{
    height:50px;
    border-radius:12px;
}

.form-control:focus{
    border-color:#6C3EF4;
    box-shadow:0 0 0 .2rem rgba(108,62,244,.15);
}

.info-box{
    background:#f8f7ff;
    border:1px solid #ece8ff;
    border-radius:12px;
    padding:15px;
    margin-bottom:15px;
}

.info-title{
    font-size:13px;
    color:#6c757d;
    margin-bottom:5px;
}

.info-value{
    font-weight:400;
    color:#222;
}

.btn-return{
    background:#6f42c1;
    border:none;
    color:white;
    border-radius:12px;
    padding:10px 25px;
    font-weight:600;
}

.btn-return:hover{
    background:#157347;
    color:white;
}

.btn-secondary{
    border-radius:12px;
    padding:10px 25px;
}
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card return-card">
                <div class="card-header-custom">
                    <div class="d-flex align-items-center">

                        <div class="ms-3">
                            <h4 class="mb-0">Return Book</h4>
                            <small>Confirm book return transaction</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">

                    <div class="form-label">Book</div>
                    <div class="info-box">
                        <div class="info-value">
                          <?= htmlspecialchars($borrow['title']); ?>
                        </div>
                    </div>

                    <div class="form-label">Member</div>
                    <div class="info-box">
                        <div class="info-value">
                            <?= htmlspecialchars($borrow['member_name']); ?>
                        </div>
                    </div>

                    <div class="form-label">Borrow Date</div>
                    <div class="info-box">
                        <div class="info-value">
                          <?= date('d M Y', strtotime($borrow['borrow_date'])); ?>
                        </div>
                    </div>

                    <form action="return_controller.php" method="POST">
                        <input type="hidden" name="borrow_id" value="<?= $borrow['id']; ?>">
                        <div class="mb-4">
                            <label class="form-label">
                                Return Date
                            </label>

                            <input
                                type="date"
                                name="return_date"
                                class="form-control"
                                value="<?= date('Y-m-d'); ?>"
                                min="<?= $borrow['borrow_date']; ?>"
                                required>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-return">
                                Confirm
                            </button>

                            <a href="../Borrow/index.php" class="btn btn-secondary">
                                Back
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>