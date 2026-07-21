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
    echo '<div class="alert alert-danger">Author not found.</div>';
    include '../includes/footer.php';
    exit;
}
?>

<h2 style="text-align: center;" class="mb-4">Edit Author</h2>
<div class="card p-4" style="max-width:500px;">
  <form style="align-items: center;" action="author_controller.php" method="POST">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?php echo $author['id']; ?>">
    <div class="mb-3">
      <label class="form-label">Author Name</label>
      <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($author['name']); ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?php include '../includes/footer.php'; ?>
