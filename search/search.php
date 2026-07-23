<?php
// 1. Core Authentication & Configuration Checks
require_once '../Authentication/auth_check.php';
require_once __DIR__ . '/../Database/db.php';

if (!isset($conn) && isset($db)) {
    $conn = $db;
}

// 2. Page-specific styles
$page_styles = ['Assets/css/dashboard.css'];

// 3. Include Layout Headers
include '../Includes/header.php';
include '../Includes/sidebar.php';
include '../Includes/navbar.php';

// Get search query from URL parameter
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

$books = [];
$members = [];
$borrowings = [];

if (!empty($query)) {
    $searchTerm = "%{$query}%";

    try {
        // Query Books with Author names
        $stmtBook = $conn->prepare("
            SELECT b.*, a.name AS author_name, c.name AS category_name 
            FROM books b 
            LEFT JOIN authors a ON b.author_id = a.id 
            LEFT JOIN categories c ON b.category_id = c.id 
            WHERE b.title LIKE :query OR a.name LIKE :query 
            ORDER BY b.id DESC
        ");
        $stmtBook->execute([':query' => $searchTerm]);
        $books = $stmtBook->fetchAll(PDO::FETCH_ASSOC);

        // Query Members/Students
        $stmtMember = $conn->prepare("
            SELECT * FROM members 
            WHERE name LIKE :query OR email LIKE :query OR phone LIKE :query 
            ORDER BY id DESC
        ");
        $stmtMember->execute([':query' => $searchTerm]);
        $members = $stmtMember->fetchAll(PDO::FETCH_ASSOC);

        // Query Borrowing Transactions
        $stmtBorrow = $conn->prepare("
            SELECT br.*, m.name AS student_name, m.profile_image, bk.title AS book_title 
            FROM borrowings br 
            JOIN members m ON br.member_id = m.id 
            JOIN books bk ON br.book_id = bk.id 
            WHERE m.name LIKE :query OR bk.title LIKE :query 
            ORDER BY br.id DESC
        ");
        $stmtBorrow->execute([':query' => $searchTerm]);
        $borrowings = $stmtBorrow->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $error_message = $e->getMessage();
    }
}
?>

<!-- Header Banner -->
<div class="dashboard-header-block">
    <h1>Search Results</h1>
    <p>
        <?php if (!empty($query)): ?>
            Showing matching results for "<strong><?php echo htmlspecialchars($query); ?></strong>"
        <?php else: ?>
            Please enter a keyword in the search bar above.
        <?php endif; ?>
    </p>
</div>

<div class="dashboard-content-layout" style="display: flex; flex-direction: column; gap: 24px;">

    <!-- SECTION 1: BOOKS RESULTS -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fa-solid fa-book" style="margin-right: 8px; color: var(--primary-color, #6366f1);"></i> Matching Books (<?php echo count($books); ?>)</h3>
            <a href="<?php echo BASE_URL; ?>Books/index.php" class="link-view-all">Manage Books</a>
        </div>
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($books)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 18px;">No books found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($books as $b): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($b['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($b['author_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($b['category_name'] ?? 'Uncategorized'); ?></td>
                            <td><span class="badge <?php echo ($b['status'] === 'available') ? 'approved' : 'pending'; ?>"><?php echo ucfirst(htmlspecialchars($b['status'])); ?></span></td>
                            <td style="text-align: right;">
                                <a href="<?php echo BASE_URL; ?>Books/edit.php?id=<?php echo $b['id']; ?>" class="btn-table-action" title="Edit Book"><i class="fa-solid fa-pen-to-square"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- SECTION 2: MEMBERS / STUDENTS RESULTS -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fa-solid fa-users" style="margin-right: 8px; color: var(--primary-color, #6366f1);"></i> Matching Members (<?php echo count($members); ?>)</h3>
            <a href="<?php echo BASE_URL; ?>Members/index.php" class="link-view-all">Manage Members</a>
        </div>
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($members)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 18px;">No members found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($members as $m): 
                        $imgValue = $m['profile_image'] ?? '';
                        $userImg = !empty($imgValue) ? (filter_var($imgValue, FILTER_VALIDATE_URL) ? $imgValue : BASE_URL . 'Assets/images/' . htmlspecialchars($imgValue)) : BASE_URL . 'Assets/images/user-placeholder.jpg';
                    ?>
                        <tr>
                            <td class="table-profile-cell">
                                <img src="<?php echo htmlspecialchars($userImg); ?>" alt="Profile">
                                <span><?php echo htmlspecialchars($m['name']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($m['email'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($m['phone'] ?? 'N/A'); ?></td>
                            <td style="text-align: right;">
                                <a href="<?php echo BASE_URL; ?>Members/edit.php?id=<?php echo $m['id']; ?>" class="btn-table-action" title="Edit Member"><i class="fa-solid fa-pen-to-square"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- SECTION 3: BORROWING LOGS RESULTS -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fa-solid fa-right-left" style="margin-right: 8px; color: var(--primary-color, #6366f1);"></i> Matching Borrow Log Records (<?php echo count($borrowings); ?>)</h3>
            <a href="<?php echo BASE_URL; ?>Borrow/index.php" class="link-view-all">Manage Borrowings</a>
        </div>
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Book Title</th>
                    <th>Borrow Date</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($borrowings)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 18px;">No borrow records found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($borrowings as $br): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($br['student_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($br['book_title']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($br['borrow_date'])); ?></td>
                            <td><span class="badge <?php echo ($br['status'] === 'returned') ? 'paid' : 'pending'; ?>"><?php echo ucfirst(htmlspecialchars($br['status'])); ?></span></td>
                            <td style="text-align: right;">
                                <a href="<?php echo BASE_URL; ?>Borrow/edit.php?id=<?php echo $br['id']; ?>" class="btn-table-action" title="Edit Borrow Record"><i class="fa-solid fa-pen-to-square"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include '../Includes/footer.php'; ?>