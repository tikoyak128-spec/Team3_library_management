<?Php include '../includes/header.php'; ?>
<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();

if (!$member) {
    echo '<div class="alert alert-danger">Member not found.</div>';
    include '../includes/footer.php';
    exit;
}
?>

<h2 class="mb-4">Edit Member</h2>
<div class="card p-4" style="max-width:500px;">
  <form action="member_controller.php" method="POST">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($member['name']); ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Phone</label>
      <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($member['phone'] ?? ''); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($member['email'] ?? ''); ?>">
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?php include '../includes/footer.php'; ?>
