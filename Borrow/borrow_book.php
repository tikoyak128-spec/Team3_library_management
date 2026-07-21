<?php include '../includes/header.php' ?>
<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

$books = $conn->query("
    SELECT id, title, quantity
    FROM books
    WHERE status='available'
    AND quantity > 0
    ORDER BY title
");

$members = $conn->query("
    SELECT id, name
    FROM members
    ORDER BY name
");
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

.borrow-card{
    border:none;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
}

.card-header-custom{
    background:linear-gradient(135deg,#6C3EF4,#8B5CF6);
    color:white;
    padding:25px;
}

.form-label{
    font-weight:600;
    margin-bottom:8px;
}

.form-control,
.form-select{
    height:50px;
    border-radius:12px;
}

.form-control:focus,
.form-select:focus{
    border-color:#6C3EF4;
    box-shadow:0 0 0 .2rem rgba(108,62,244,.15);
}
.btn-secondary{
    background:secondary;
    border:none;
    color:white;
    border-radius:12px;
    padding:10px 25px;
    font-weight:600;
}
.btn-purple{
    background:#6C3EF4;
    border:none;
    color:white;
    border-radius:12px;
    padding:10px 25px;
    font-weight:600;
}

.btn-purple:hover{
    background:#5B2EF0;
    color:white;
}

.icon-box{
    width:60px;
    height:60px;
    border-radius:15px;
    background:rgba(255,255,255,.2);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:28px;
}
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card borrow-card">

                <div class="card-header-custom">

                    <div class="d-flex align-items-center">

                        <div class="stat-icon d-flex justify-content-center align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" 
                                viewBox="0 0 640 640" 
                                width="50" height="50"
                                fill="currentColor" 
                                class="text-white m-4">
                                <path d="M480 576L192 576C139 576 96 533 96 480L96 160C96 107 139 64 192 64L496 64C522.5 64 544 85.5 544 112L544 400C544 420.9 530.6 438.7 512 445.3L512 512C529.7 512 544 526.3 544 544C544 561.7 529.7 576 512 576L480 576zM192 448C174.3 448 160 462.3 160 480C160 497.7 174.3 512 192 512L448 512L448 448L192 448zM224 216C224 229.3 234.7 240 248 240L424 240C437.3 240 448 229.3 448 216C448 202.7 437.3 192 424 192L248 192C234.7 192 224 202.7 224 216zM248 288C234.7 288 224 298.7 224 312C224 325.3 234.7 336 248 336L424 336C437.3 336 448 325.3 448 312C448 298.7 437.3 288 424 288L248 288z"/>
                            </svg>
                        </div>

                        <div>
                            <h4 class="mb-0">
                                New Borrowing
                            </h4>

                            <small>
                                Select a book and member
                            </small>
                        </div>

                    </div>

                </div>

                <div class="card-body p-4">

                    <form action="borrow_controller.php" method="POST">

                        <div class="row">

                            <div class="col-md-12 mb-4">

                                <label class="form-label">
                                    Book
                                </label>

                                <select name="book_id"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        Select a Book
                                    </option>

                                    <?php while($b = $books->fetch_assoc()): ?>

                                        <option value="<?= $b['id']; ?>">

                                            <?= htmlspecialchars($b['title']); ?>
                                            (Available:
                                            <?= $b['quantity']; ?>)

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>

                            <div class="col-md-12 mb-4">

                                <label class="form-label">
                                    Member
                                </label>

                                <select name="member_id"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        Select a Member
                                    </option>

                                    <?php while($m = $members->fetch_assoc()): ?>

                                        <option value="<?= $m['id']; ?>">

                                            <?= htmlspecialchars($m['name']); ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>

                            <div class="col-md-12 mb-4">

                                <label class="form-label">
                                    Borrow Date
                                </label>

                                <input type="date"
                                       name="borrow_date"
                                       class="form-control"
                                       value="<?= date(''); ?>"
                                       required>

                            </div>

                        </div>

                        <div class="d-flex gap-2">

                            <button type="submit"
                                    class="btn btn-purple">

                                Borrow Book

                            </button>

                            <a href="index.php"
                               class="btn btn-secondary">

                                Cancel

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>