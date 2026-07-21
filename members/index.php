<?Php include '../includes/header.php'; ?>
<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

$result = $conn->query("SELECT * FROM members ORDER BY id DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Members</h2>
  <a href="create.php" class="btn btn-primary">+ Add Member</a>
</div>

<div class="card p-3">
<table class="table table-hover align-middle">
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Phone</th>
      <th>Email</th>
      <th class="text-end">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo htmlspecialchars($row['name']); ?></td>
          <td><?php echo htmlspecialchars($row['phone'] ?? '—'); ?></td>
          <td><?php echo htmlspecialchars($row['email'] ?? '—'); ?></td>
          <td class="text-end">
            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this member?');">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="5" class="text-center text-muted">No members found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
</div>

<?php include '../includes/footer.php'; ?>
