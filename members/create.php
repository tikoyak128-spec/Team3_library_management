<?Php include '../includes/header.php'; ?>
<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';
?>

<h2 class="mb-4">Add Member</h2>
<div class="card p-4" style="max-width:500px;">
  <form action="member_controller.php" method="POST">
    <input type="hidden" name="action" value="create">
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Phone</label>
      <input type="text" name="phone" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?php include '../includes/footer.php'; ?>
