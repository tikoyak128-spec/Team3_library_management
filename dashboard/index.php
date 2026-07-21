<?php
// 1. Core Authentication & Configuration Checks
require_once '../Authentication/auth_check.php';
require_once '../Database/db.php'; // Establishes your PDO $db instance connection

if (!isset($db) && isset($conn)) {
    $db = $conn;
}
// 4. Hook up page-specific styles BEFORE header compiles DOM structure
$page_styles = ['Assets/css/dashboard.css'];

// 5. Render Structural Layout Components
include '../Includes/header.php';
include '../Includes/sidebar.php';
include '../Includes/navbar.php'; 

// --- DATABASE QUERIES START HERE ---
try{
    $stmt = $conn->query("SELECT COUNT(*) FROM books");
    $total_students = $stmt->fetchColumn();
    $stmt = $conn->query("SELECT count(*) from books where status ='available' ");
    $books_available = $stmt->fetchColumn();
    $stmt = $conn->query("SELECT count(*) from borrowings where status = 'issued'");
    $books_issued = $stmt->fetchColumn();
    $stmt = $conn->query("SELECT count(*) from borrowings where status = 'returned'");
    $books_due = $stmt->fetchColumn();
    

}catch(PDOException $e){

}
?>

<!-- Top Headline Greeting Row -->
<div class="dashboard-header-block">
    <h1>Welcome Saiba Sen! 👋</h1>
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
    
    <!-- Left Column: Split Data Cards Container -->
    <div class="main-column">
        
        <!-- Row: Fees & Student Profile Tables -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
            <!-- Fees Pending Card Table -->
           <!-- Student Profile Card Table -->
<div class="dashboard-card">
    <div class="card-header">
        <h3>Fees Pending</h3>
        <a href="<?php echo BASE_URL; ?>Members/index.php" class="link-view-all">View All</a>
    </div>
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Students</th>
                <th>Contact info</th> <!-- Changed Heading -->
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
                    $studentImg = !empty($student['profile_image']) ? $student['profile_image'] : 'user-placeholder.jpg';
                ?>
                <tr>
                    <td class="table-profile-cell">
                        <img src="<?php echo BASE_URL . 'Assets/images/' . htmlspecialchars($studentImg); ?>" alt="Profile">
                        <span><?php echo htmlspecialchars($student['name']); ?></span>
                    </td>
                    <!-- Displaying email and phone columns from your database -->
                    <td>
                        <span style="font-size: 13px; display: block; font-weight: 500;"><?php echo htmlspecialchars($student['email']); ?></span>
                        <span style="font-size: 11px; color: var(--text-muted);"><?php echo htmlspecialchars($student['phone']); ?></span>
                    </td>
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
            <!-- Student Profile Processing Status Card Table with Forms -->
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
                                $studentImg = !empty($student['profile_image']) ? $student['profile_image'] : 'user-placeholder.jpg';
                            ?>
                            <tr>
                                <td class="table-profile-cell">
                                    <img src="<?php echo BASE_URL . 'Assets/images/' . htmlspecialchars($studentImg); ?>" alt="Profile">
                                    <span><?php echo htmlspecialchars($student['name']); ?></span>
                                </td>
                                <td><span class="badge <?php echo ($student['status'] === 'approved') ? 'approved' : 'pending'; ?>"><?php echo ucfirst(htmlspecialchars($student['status'])); ?></span></td>
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

        <!-- Lower Section: Book Issued/Returned Master Management Table -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3>Book Issued / Returned</h3>
                <a href="<?php echo BASE_URL; ?>Borrow/index.php" class="link-view-all">View All</a>
            </div>
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Sno.</th>
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
                            $studentImg = !empty($history['profile_image']) ? $history['profile_image'] : 'user-placeholder.jpg';
                        ?>
                        <tr>
                            <td>A<?php echo sprintf("%02d", $index + 2); ?></td>
                            <td class="table-profile-cell">
                                <img src="<?php echo BASE_URL . 'Assets/images/' . htmlspecialchars($studentImg); ?>" alt="Profile">
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
                    $cover = !empty($book['cover_image']) ? $book['cover_image'] : 'book-cover-placeholder.jpg';
                ?>
                <div class="book-item-row">
                    <img src="<?php echo BASE_URL . 'Assets/images/' . htmlspecialchars($cover); ?>" alt="Cover" class="book-cover-img">
                    <div class="book-item-details">
                        <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                        <p><?php echo htmlspecialchars($book['author']) . ', ' . htmlspecialchars($book['publish_year']); ?></p>
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