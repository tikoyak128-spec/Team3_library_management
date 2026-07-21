<?php include '../includes/header.php' ?>
<?php
    define('BASE_PATH', dirname(__DIR__)); 
    require_once BASE_PATH . '/Database/db.php';

    $sql = "SELECT br.*, b.title, m.name AS member_name
            FROM borrowings br
            JOIN books b ON br.book_id = b.id
            JOIN members m ON br.member_id = m.id
            ORDER BY br.borrow_date DESC, br.id DESC";
    $result = $conn->query($sql);
?>

    <h2 class="mb-4">Borrow History</h2>

    <div class="card p-3">
    <table class="table table-hover align-middle">
    <thead>
        <tr>
        <th>ID</th>
        <th>Book</th>
        <th>Member</th>
        <th>Borrow Date</th>
        <th>Return Date</th>
        <th>Status</th>
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
            <td><?php echo htmlspecialchars($row['return_date'] ?? '—'); ?></td>
            <td>
                <?php if ($row['status'] === 'returned'): ?>
                <span class="badge bg-success">returned</span>
                <?php else: ?>
                <span class="badge bg-warning text-dark">borrowed</span>
                <?php endif; ?>
            </td>
            </tr>
        <?php endwhile; ?>
        <?php else: ?>
        <tr><td colspan="6" class="text-center text-muted">No borrow history found.</td></tr>
        <?php endif; ?>
    </tbody>
    </table>
    </div>

    <?php include '../includes/footer.php'; ?>
