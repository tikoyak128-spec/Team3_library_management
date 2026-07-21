<?php
define('BASE_URL', '..');
include '../includes/header.php';
?>

<style>
    body{
        background:#F5F6FA;
    }

    .page-title{
        color:#222;
        font-weight:700;
    }

    .author-card{
        background:#fff;
        border:none;
        border-radius:16px;
        box-shadow:0 4px 15px rgba(0,0,0,0.08);
    }

    .card-header-custom{
        background:linear-gradient(135deg,#6C3EF4,#8B5CF6);
        color:white;
        border-radius:16px 16px 0 0;
        padding:20px;
    }

    .form-label{
        font-weight:600;
        color:#444;
    }

    .form-control{
        border-radius:12px;
        height:50px;
        border:1px solid #ddd;
    }

    .form-control:focus{
        border-color:#6C3EF4;
        box-shadow:0 0 0 .2rem rgba(108,62,244,.15);
    }

    .btn-purple{
        background:#6C3EF4;
        border:none;
        color:#fff;
        padding:10px 25px;
        border-radius:12px;
        font-weight:600;
    }

    .btn-purple:hover{
        background:#5A30E5;
        color:#fff;
    }

    .btn-cancel{
        border-radius:12px;
        padding:10px 25px;
    }

    .icon-circle{
        width:50px;
        height:50px;
        background:rgba(255,255,255,0.2);
        border-radius:50%;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:22px;
    }
</style>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title">Add Author</h2>

        <a href="index.php" class="btn btn-light shadow-sm">
            Back to Authors
        </a>
    </div>

    <div class="row justify-content-center">

        <div class="col-md-8 col-lg-6">

            <div class="card author-card">

                <div class="card-header-custom d-flex align-items-center">

                    <div class="icon-circle me-3">
                        ✍️
                    </div>

                    <div>
                        <h4 class="mb-0">New Author</h4>
                        <small>Add a new author to the library system</small>
                    </div>

                </div>

                <div class="card-body p-4">

                    <form action="author_controller.php" method="POST">

                        <input type="hidden" name="action" value="create">

                        <div class="mb-4">
                            <label class="form-label">
                                Author Name
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                placeholder="Enter author name..."
                                required>
                        </div>

                        <div class="d-flex gap-2">

                            <button type="submit" class="btn btn-purple">
                                Save Author
                            </button>

                            <a href="index.php" class="btn btn-secondary btn-cancel">
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