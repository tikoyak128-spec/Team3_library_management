<?php include '../includes/header.php'; ?>

<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';
?>

<style>
  :root{
      --primary:#6C3EF4;
      --secondary:#8B5CF6;
      --bg:#F5F6FA;
  }

  body{
      background:var(--bg);
  }

  .member-card{
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
      color:#374151;
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

  .btn-secondary{
      border-radius:12px;
      padding:10px 25px;
  }

</style>

<div class="container-fluid py-4">

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card member-card">
                <div class="card-header-custom">
                    <div class="d-flex align-items-center">

                        <div class="ms-3">
                            <h4 class="mb-0">
                                Add New Member
                            </h4>

                            <small>
                                Create a new library member
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">

                    <form action="member_controller.php" method="POST">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label class="form-label">
                                Member Name
                            </label>
                            <input type="text" name="name" class="form-control" placeholder="Enter member name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Phone Number
                            </label>

                            <input type="text" name="phone" class="form-control" placeholder="Enter phone number">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                Email Address
                            </label>
                            <input type="email" name="email" class="form-control" placeholder="Enter email address">

                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-purple">
                                Save Member
                            </button>

                            <a href="index.php" class="btn btn-secondary"> 
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