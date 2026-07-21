<?Php include '../includes/header.php'; ?>
<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/Database/db.php';

$sql = "SELECT br.*, b.title, m.name AS member_name
        FROM borrowings br
        JOIN books b ON br.book_id = b.id
        JOIN members m ON br.member_id = m.id
        WHERE br.status = 'borrowed'
        ORDER BY br.borrow_date ASC";
$result = $conn->query($sql);
?>

<h2 class="mb-4">Books Awaiting Return</h2>

<div class="card p-3">
<table class="table table-hover align-middle">
  <thead>
    <tr>
      <th>ID</th>
      <th>Book</th>
      <th>Member</th>
      <th>Borrow Date</th>
      <th class="text-end">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo htmlspecialchars($row['title']); ?></td>
          <td><?php echo htmlspecialchars($row['member_name']); ?></td>
          <td><?php echo htmlspecialchars($row['borrow_date']); ?></td>
          <td class="text-end">
            <a href="return_book.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">Return</a>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="5" class="text-center text-muted">No books awaiting return.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
</div>

<?php include '../includes/footer.php'; ?>
