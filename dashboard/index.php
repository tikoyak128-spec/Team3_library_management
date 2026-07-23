<?php
// 1. Core Authentication & Configuration Checks
require_once '../Authentication/auth_check.php';
require_once __DIR__ . '/../Database/db.php'; // Establishes your PDO $conn instance connection

if (!isset($db) && isset($conn)) {
    $db = $conn;
}

// 2. Hook up page-specific styles BEFORE header compiles DOM structure
$page_styles = ['Assets/css/dashboard.css'];

// 3. Render Structural Layout Components
include '../Includes/header.php';
include '../Includes/sidebar.php';
include '../Includes/navbar.php'; 

// --- DATABASE QUERIES & DATA FETCHING START HERE ---
try {
    // Analytics Counters
    $stmt = $conn->query("SELECT COUNT(*) FROM books");
    $total_students = $stmt->fetchColumn();

    $stmt = $conn->query("SELECT COUNT(*) FROM books WHERE status = 'available'");
    $books_available = $stmt->fetchColumn();

    $stmt = $conn->query("SELECT COUNT(*) FROM borrowings WHERE status = 'issued'");
    $books_issued = $stmt->fetchColumn();

    $stmt = $conn->query("SELECT COUNT(*) FROM borrowings WHERE status = 'returned'");
    $books_due = $stmt->fetchColumn();

    // 1. Fetch "Book Returned" Data (JOIN borrowings with members & books)
    $returnedQuery = "SELECT b.*, m.name AS student_name, m.email AS student_email, m.phone AS student_phone, m.profile_image 
                      FROM borrowings b 
                      JOIN members m ON b.member_id = m.id 
                      WHERE b.status = 'returned' 
                      ORDER BY b.id DESC LIMIT 5";
    $stmt = $conn->query($returnedQuery);
    $returned_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Fetch Student Profiles Data
    $stmt = $conn->query("SELECT * FROM members ORDER BY id DESC LIMIT 5");
    $student_profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Fetch Book Issued Transactions
    $borrowQuery = "SELECT b.*, m.name AS student_name, m.profile_image, bk.title AS book_name 
                    FROM borrowings b 
                    JOIN members m ON b.member_id = m.id 
                    JOIN books bk ON b.book_id = bk.id 
                    ORDER BY b.id DESC";
    $stmt = $conn->query($borrowQuery);
    $borrow_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Fetch Wishlist Books Data with Author JOIN
    $wishlistQuery = "SELECT bk.*, a.name AS author_name 
                      FROM books bk 
                      LEFT JOIN authors a ON bk.author_id = a.id 
                      ORDER BY bk.id DESC LIMIT 5";
    $stmt = $conn->query($wishlistQuery);
    $wishlist_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Fallback empty sets on query error
    $total_students = 0;
    $books_available = 0;
    $books_issued = 0;
    $books_due = 0;
    $returned_books = [];
    $student_profiles = [];
    $borrow_history = [];
    $wishlist_books = [];
}
?>

<!-- Top Headline Greeting Row -->
<div class="dashboard-header-block">
    <h1>Welcome to Library Management</h1>
    <p>A new book can added to your library. <a href="<?php echo BASE_URL; ?>Books/create.php">add here</a></p>
</div>

<!-- Four-Column Core Analytics Highlights Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon-wrapper"><i class="fa-solid fa-user-graduate"></i></div>
            <span class="stat-trend positive">+2.5%</span>
        </div>
        <p>Total Students</p>
        <h3><?php echo number_format($total_students); ?></h3>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon-wrapper"><i class="fa-solid fa-book"></i></div>
            <span class="stat-trend positive">+2.5%</span>
        </div>
        <p>Books available</p>
        <h3><?php echo number_format($books_available); ?></h3>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon-wrapper"><i class="fa-solid fa-bookmark"></i></div>
            <span class="stat-trend negative">-2.5%</span>
        </div>
        <p>Book Issued</p>
        <h3><?php echo number_format($books_issued); ?></h3>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon-wrapper"><i class="fa-solid fa-clock"></i></div>
            <span class="stat-trend positive">+2.5%</span>
        </div>
        <p>Book due for Return</p>
        <h3><?php echo number_format($books_due); ?></h3>
    </div>
</div>

<!-- Split Data Tables Layout Section -->
<div class="dashboard-content-layout">
    
    <!-- Left Column: Main Container -->
    <div class="main-column">
        
        <!-- Side-by-Side Grid Row -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
            
            <!-- 1. Book Returned Card Table -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>Book Returned</h3>
                    <a href="<?php echo BASE_URL; ?>Borrow/index.php" class="link-view-all">View All</a>
                </div>
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Students</th>
                            <th>Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($returned_books)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 20px;">
                                    No returned book records found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($returned_books as $ret): 
                                $imgValue = $ret['profile_image'] ?? '';
                                $studentImg = !empty($imgValue) ? (filter_var($imgValue, FILTER_VALIDATE_URL) ? $imgValue : BASE_URL . 'Assets/images/' . htmlspecialchars($imgValue)) : BASE_URL . 'Assets/images/user-placeholder.jpg';
                            ?>
                            <tr>
                                <td class="table-profile-cell">
                                    <img src="<?php echo htmlspecialchars($studentImg); ?>" alt="Profile">
                                    <span><?php echo htmlspecialchars($ret['student_name']); ?></span>
                                </td>
                                <td>
                                    <span style="font-size: 13px; display: block; font-weight: 500;"><?php echo htmlspecialchars($ret['status'] ?? 'N/A'); ?></span>
                                    <span style="font-size: 11px; color: var(--text-muted);"><?php echo htmlspecialchars($ret['student_phone'] ?? ''); ?></span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="<?php echo BASE_URL; ?>Borrow/edit.php?id=<?php echo $ret['id']; ?>" class="btn-table-action" title="Edit Entry"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <form action="<?php echo BASE_URL; ?>Borrow/delete.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $ret['id']; ?>">
                                        <button type="submit" class="btn-table-action btn-delete" data-item="return record" title="Delete Entry"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- 2. Student Profile Processing Status Card Table -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>Student Profile</h3>
                    <a href="<?php echo BASE_URL; ?>Members/index.php" class="link-view-all">View All</a>
                </div>
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Students</th>
                            <th>Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($student_profiles)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 20px;">
                                    No registered members found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($student_profiles as $student): 
                                $imgValue = $student['profile_image'] ?? '';
                                $studentImg = !empty($imgValue) ? (filter_var($imgValue, FILTER_VALIDATE_URL) ? $imgValue : BASE_URL . 'Assets/images/' . htmlspecialchars($imgValue)) : BASE_URL . 'Assets/images/user-placeholder.jpg';
                                $statusValue = isset($student['status']) ? $student['status'] : 'approved';
                            ?>
                            <tr>
                                <td class="table-profile-cell">
                                    <img src="<?php echo htmlspecialchars($studentImg); ?>" alt="Profile">
                                    <span><?php echo htmlspecialchars($student['name']); ?></span>
                                </td>
                                <td><span class="badge <?php echo ($statusValue === 'approved') ? 'approved' : 'pending'; ?>"><?php echo ucfirst(htmlspecialchars($statusValue)); ?></span></td>
                                <td style="text-align: right;">
                                    <a href="<?php echo BASE_URL; ?>Members/edit.php?id=<?php echo $student['id']; ?>" class="btn-table-action" title="Edit Student"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <form action="<?php echo BASE_URL; ?>Members/delete.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                                        <button type="submit" class="btn-table-action btn-delete" data-item="student" title="Delete Student"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Lower Section: Book Issued Scrollable Container -->
        <div class="dashboard-card" style="margin-top: 24px;">
            <div class="card-header">
                <h3>Book Issued</h3>
                <a href="<?php echo BASE_URL; ?>Borrow/index.php" class="link-view-all">View All</a>
            </div>
            
            <!-- SCROLLABLE CONTAINER -->
            <div class="table-scroll-wrapper" style="max-height: 280px; overflow-y: auto; overflow-x: auto;">
                <table class="dashboard-table">
                    <thead style="position: sticky; top: 0; background-color: var(--bg-card, #ffffff); z-index: 2;">
                        <tr>
                            <th>No.</th>
                            <th>Student name</th>
                            <th>Book name</th>
                            <th>Issued date</th>
                            <th>Status</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($borrow_history)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 25px;">
                                    <i class="fa-solid fa-circle-info" style="margin-right: 5px;"></i> No issue or transaction logs available.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($borrow_history as $index => $history): 
                                $imgValue = $history['profile_image'] ?? '';
                                $studentImg = !empty($imgValue) ? (filter_var($imgValue, FILTER_VALIDATE_URL) ? $imgValue : BASE_URL . 'Assets/images/' . htmlspecialchars($imgValue)) : BASE_URL . 'Assets/images/user-placeholder.jpg';
                            ?>
                            <tr>
                                <td>A<?php echo sprintf("%02d", $index + 1); ?></td>
                                <td class="table-profile-cell">
                                    <img src="<?php echo htmlspecialchars($studentImg); ?>" alt="Profile">
                                    <span><?php echo htmlspecialchars($history['student_name']); ?></span>
                                </td>
                                <td><strong><?php echo htmlspecialchars($history['book_name']); ?></strong></td>
                                <td><?php echo date('M d, Y', strtotime($history['borrow_date'])); ?></td>
                                <td><span class="badge <?php echo ($history['status'] === 'returned') ? 'paid' : 'pending'; ?>"><?php echo ucfirst(htmlspecialchars($history['status'])); ?></span></td>
                                <td style="text-align: center;">
                                    <a href="<?php echo BASE_URL; ?>Borrow/edit.php?id=<?php echo $history['id']; ?>" class="btn-table-action" title="Edit Transaction"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <form action="<?php echo BASE_URL; ?>Borrow/delete.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $history['id']; ?>">
                                        <button type="submit" class="btn-table-action btn-delete" data-item="borrow entry" title="Delete Entry"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Dynamic Wishlist Panel Container -->
    <aside class="side-column">
        <button class="wishlist-btn-toggle">
            <i class="fa-solid fa-bookmark"></i> Wishlist
        </button>

        <div class="dashboard-card" style="flex: 1;">
            <?php if (empty($wishlist_books)): ?>
                <p style="text-align: center; color: var(--text-muted); padding: 20px; font-size: 14px;">Your wishlist is empty.</p>
            <?php else: ?>
                <?php foreach ($wishlist_books as $book): 
                    $coverValue = $book['cover_image'] ?? '';
                    $bookCover = !empty($coverValue) ? (filter_var($coverValue, FILTER_VALIDATE_URL) ? $coverValue : BASE_URL . 'Assets/images/' . htmlspecialchars($coverValue)) : BASE_URL . 'Assets/images/book-cover-placeholder.jpg';
                    $authorName = !empty($book['author_name']) ? $book['author_name'] : 'Unknown Author';
                    $publishYear = isset($book['publish_year']) ? $book['publish_year'] : '2023';
                ?>
                <div class="book-item-row">
                    <img src="<?php echo htmlspecialchars($bookCover); ?>" alt="Cover" class="book-cover-img">
                    <div class="book-item-details">
                        <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                        <p><?php echo htmlspecialchars($authorName) . ', ' . htmlspecialchars($publishYear); ?></p>
                        <div style="margin-top: 4px; display: flex; gap: 8px;">
                            <a href="<?php echo BASE_URL; ?>Books/edit.php?id=<?php echo $book['id']; ?>" style="font-size: 11px; color: var(--text-muted); text-decoration: none;"><i class="fa-solid fa-pen"></i></a>
                            <form action="<?php echo BASE_URL; ?>Books/delete.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
                                <button type="submit" class="btn-delete" data-item="wishlist book" style="background:none; border:none; font-size:11px; color:var(--text-muted); cursor:pointer;"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </aside>
</div>

<?php 
include '../Includes/footer.php'; 
?>