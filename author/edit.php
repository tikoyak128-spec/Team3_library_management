<?php include '../includes/header.php'; ?>

<?php
    define('BASE_PATH', dirname(__DIR__));
    require_once BASE_PATH . '/Database/db.php';

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    $stmt = $conn->prepare("SELECT * FROM authors WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $author = $stmt->get_result()->fetch_assoc();

    if (!$author) {
        echo '<div class="container py-4">
                <div class="alert alert-danger">
                    Author not found.
                </div>
            </div>';
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

  .edit-card{
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

  .btn-purple{
      background:#6f42c1;
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

  .btn-secondary{
      border-radius:12px;
      padding:10px 25px;
  }

  .author-preview{
      background:#f8f7ff;
      border:1px solid #ece8ff;
      border-radius:12px;
      padding:15px;
      margin-bottom:20px;
  }

</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="card edit-card">
                <div class="card-header-custom">
                    <div class="d-flex align-items-center">
                        <div class="ms-3">
                            <h4 class="mb-0">Edit Author</h4>
                            <small>Update author information</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">

                    <div class="author-preview">
                      <div class="d-flex align-items-center">
                        <div>
                          <div class="fw-bold">
                            Author ID <?= $author['id']; ?>
                          </div>
                        </div>
                      </div>
                    </div>

                    <form action="author_controller.php" method="POST">

                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= $author['id']; ?>">

                        <div class="mb-4">
                            <label class="form-label">
                                Author Name
                            </label>

                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($author['name']); ?>"required>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-purple">
                                Update Author
                            </button>

                            <a href="index.php" class="btn btn-secondary">
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