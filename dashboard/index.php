<?php
<<<<<<< HEAD
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
=======
require_once __DIR__ . '/../Authentication/auth_check.php';
require_once __DIR__ . '/../Database/db.php';

$pageTitle  = 'Dashboard';
$activePage = 'dashboard';

// ---- Stats ----
$totalStudents = (int) $pdo->query('SELECT COUNT(*) c FROM members')->fetch()['c'];
$booksAvailable = (int) $pdo->query('SELECT COALESCE(SUM(available_copies),0) c FROM books')->fetch()['c'];
$booksIssued   = (int) $pdo->query("SELECT COUNT(*) c FROM borrowings WHERE status = 'Issued'")->fetch()['c'];
$totalCopies   = (int) $pdo->query('SELECT COALESCE(SUM(total_copies),0) c FROM books')->fetch()['c'];

// ---- Recent borrow/return records ----
$recent = $pdo->query("
    SELECT br.id, m.name AS student_name, bk.title AS book_name, br.issue_date, br.due_date, br.status
    FROM borrowings br
    JOIN members m ON m.id = br.member_id
    JOIN books bk ON bk.id = br.book_id
    ORDER BY br.id DESC LIMIT 5
")->fetchAll();

// ---- Recently added books (wishlist-style panel) ----
$recentBooks = $pdo->query("SELECT title FROM books ORDER BY id DESC LIMIT 3")->fetchAll();

if (session_status() === PHP_SESSION_NONE) session_start();
$userName = $_SESSION['user_name'] ?? 'Guest';
$userRole = $_SESSION['user_role'] ?? 'Admin';
$initial  = strtoupper(substr($userName, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?> — Library Pro</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ================= Library Pro — Dashboard Styles ================= */
:root {
  --purple: #6c3ffa;
  --purple-dark: #5a2fe0;
  --purple-light: #f3effe;
  --bg: #f6f5fb;
  --card-bg: #ffffff;
  --text-dark: #1e1b2e;
  --text-muted: #8a869a;
  --green: #1fbf75;
  --red: #ff6b6b;
  --yellow: #ffb020;
  --border: #ececf3;
  --radius: 14px;
  --shadow: 0 4px 18px rgba(108, 63, 250, 0.06);
}

* { box-sizing: border-box; }

body {
  margin: 0;
  font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
  background: var(--bg);
  color: var(--text-dark);
}

a { text-decoration: none; color: inherit; }

/* ===== Layout ===== */
.app-wrapper { display: flex; min-height: 100vh; }
.main-content { flex: 1; padding: 28px 34px; max-width: 100%; }

/* ===== Sidebar ===== */
.sidebar {
  width: 250px;
  background: linear-gradient(180deg, var(--purple), var(--purple-dark));
  color: #fff;
  padding: 26px 20px;
  display: flex;
  flex-direction: column;
  position: sticky;
  top: 0;
  height: 100vh;
}
.sidebar .brand {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 34px;
}
.sidebar .brand span.icon { font-size: 22px; }
.sidebar nav { flex: 1; }
.sidebar nav a {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 11px 14px;
  border-radius: 10px;
  margin-bottom: 4px;
  font-size: 14.5px;
  opacity: 0.88;
  transition: background .15s ease;
  color: #fff;
}
.sidebar nav a:hover { background: rgba(255,255,255,0.10); }
.sidebar nav a.active {
  background: rgba(255,255,255,0.18);
  opacity: 1;
  font-weight: 600;
}
.sidebar .upgrade-box {
  background: rgba(255,255,255,0.12);
  border-radius: 12px;
  padding: 16px;
  text-align: center;
  font-size: 13px;
  margin-bottom: 18px;
}
.sidebar .upgrade-box button {
  margin-top: 8px;
  background: #fff;
  color: var(--purple-dark);
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  font-size: 12.5px;
}
.sidebar .user-box {
  display: flex;
  align-items: center;
  gap: 10px;
  border-top: 1px solid rgba(255,255,255,0.15);
  padding-top: 16px;
}
.sidebar .user-box .avatar {
  width: 36px; height: 36px; border-radius: 50%;
  background: rgba(255,255,255,0.25);
  display: flex; align-items: center; justify-content: center;
  font-weight: 700;
}
.sidebar .user-box .u-name { font-size: 13.5px; font-weight: 600; }
.sidebar .user-box .u-role { font-size: 11px; opacity: .75; }
.sidebar .user-box a.logout { margin-left: auto; opacity: .8; }

/* ===== Topbar ===== */
.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 26px;
  flex-wrap: wrap;
  gap: 14px;
}
.topbar .search-box {
  flex: 1;
  max-width: 380px;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 9px 14px;
  display: flex;
  align-items: center;
  gap: 8px;
  color: var(--text-muted);
}
.topbar .search-box input {
  border: none; outline: none; flex: 1; font-size: 14px; background: transparent;
}
.topbar .date-range {
  background: #fff;
  border: 1px solid var(--border);
  padding: 8px 16px;
  border-radius: 10px;
  font-size: 13.5px;
  color: var(--text-dark);
}
.topbar .profile { display: flex; align-items: center; gap: 10px; }
.topbar .profile .avatar {
  width: 38px; height: 38px; border-radius: 50%; background: var(--purple-light);
  display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--purple);
}

.page-title { font-size: 22px; font-weight: 600; margin: 0 0 4px 0; }
.page-subtitle { color: var(--text-muted); font-size: 13.5px; margin: 0 0 22px 0; }
.welcome-banner { display: flex; align-items: center; gap: 8px; }
.welcome-banner .emoji { font-size: 22px; }

/* ===== Stat cards ===== */
.stat-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 18px;
  margin-bottom: 22px;
}
.stat-card {
  background: var(--card-bg);
  border-radius: var(--radius);
  padding: 18px 20px;
  box-shadow: var(--shadow);
}
.stat-card .icon-badge {
  width: 34px; height: 34px; border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
  background: var(--purple-light); color: var(--purple);
  margin-bottom: 12px; font-size: 16px;
}
.stat-card .stat-top { display: flex; justify-content: space-between; align-items: flex-start; }
.stat-card .delta { font-size: 12px; font-weight: 600; padding: 3px 8px; border-radius: 20px; }
.stat-card .delta.up { color: var(--green); background: #e7faf1; }
.stat-card .delta.down { color: var(--red); background: #ffeeee; }
.stat-card .stat-value { font-size: 24px; font-weight: 700; margin-top: 4px; }
.stat-card .stat-label { font-size: 13px; color: var(--text-muted); margin-top: 2px; }

/* ===== Panels / Cards ===== */
.panel-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 18px;
  margin-bottom: 22px;
}
.card {
  background: var(--card-bg);
  border-radius: var(--radius);
  padding: 20px;
  box-shadow: var(--shadow);
}
.card .card-header {
  display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;
}
.card .card-header h3 { font-size: 15.5px; margin: 0; font-weight: 600; }
.card .card-header a.view-all { font-size: 12.5px; color: var(--purple); font-weight: 600; }

/* ===== Tables ===== */
table.data-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
table.data-table th {
  text-align: left; color: var(--text-muted); font-weight: 600;
  padding: 10px 8px; border-bottom: 1px solid var(--border); font-size: 12px; text-transform: uppercase;
}
table.data-table td { padding: 12px 8px; border-bottom: 1px solid var(--border); vertical-align: middle; }
table.data-table tr:last-child td { border-bottom: none; }

.badge { padding: 4px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 600; }
.badge.paid, .badge.approved, .badge.returned, .badge.active { background: #e7faf1; color: var(--green); }
.badge.pending, .badge.issued { background: #fff5e5; color: var(--yellow); }
.badge.overdue, .badge.rejected { background: #ffeeee; color: var(--red); }

.person-cell { display: flex; align-items: center; gap: 10px; }
.person-cell .avatar-sm {
  width: 28px; height: 28px; border-radius: 50%; background: var(--purple-light);
  color: var(--purple); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;
}

/* ===== Buttons ===== */
.btn { display: inline-block; padding: 10px 18px; border-radius: 9px; font-size: 13.5px; font-weight: 600; border: none; cursor: pointer; }
.btn-primary { background: var(--purple); color: #fff; }
.btn-primary:hover { background: var(--purple-dark); }
.wishlist-btn {
  width: 100%; background: var(--purple); color: #fff; border: none;
  padding: 12px; border-radius: 10px; font-weight: 600; margin-bottom: 14px; cursor: pointer;
}

/* ===== Wishlist rows ===== */
.book-row { display: flex; gap: 12px; align-items: center; margin-bottom: 14px; }
.book-cover {
  width: 40px; height: 54px; background: var(--purple-light); border-radius: 6px;
  display: flex; align-items: center; justify-content: center; font-size: 10px; color: var(--purple); font-weight: 700;
}
.book-row .b-title { font-size: 13.5px; font-weight: 600; }
.book-row .b-author { font-size: 12px; color: var(--text-muted); }

@media (max-width: 1100px) {
  .stat-grid { grid-template-columns: repeat(2, 1fr); }
  .panel-grid { grid-template-columns: 1fr; }
}
@media (max-width: 700px) {
  .app-wrapper { flex-direction: column; }
  .sidebar { width: 100%; height: auto; position: relative; }
  .stat-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>
<div class="app-wrapper">

    <aside class="sidebar">
        <div class="brand"><span class="icon">📚</span> Library Pro</div>
        <nav>
            <a class="active" href="../Dashboard/index.php"><span>🏠</span><span>Dashboard</span></a>
            <a href="../Members/index.php"><span>🎓</span><span>Students</span></a>
            <a href="../Books/index.php"><span>📗</span><span>Books Available</span></a>
            <a href="../Borrow/index.php"><span>🔁</span><span>Book Issued/Return</span></a>
            <a href="../Categories/index.php"><span>🏷️</span><span>Categories</span></a>
            <a href="../author/index.php"><span>✍️</span><span>Authors</span></a>
        </nav>
        <div class="upgrade-box">
            Want to upgrade?
            <br><button type="button">Upgrade now</button>
        </div>
        <div class="user-box">
            <div class="avatar"><?= $initial ?></div>
            <div>
                <div class="u-name"><?= htmlspecialchars($userName) ?></div>
                <div class="u-role"><?= htmlspecialchars($userRole) ?></div>
            </div>
            <a class="logout" href="../Authentication/logout.php" title="Logout">⎋</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div class="search-box">🔍 <input type="text" placeholder="Search..."></div>
            <div class="date-range">📅 Apr 14 - May 18, 2023 ▾</div>
            <div class="profile">
                <div class="avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?></div>
            </div>
        </div>

        <h1 class="page-title welcome-banner"><span class="emoji">👋</span> Welcome <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>!</h1>
        <p class="page-subtitle">A new book can be added to your library. <a href="../Books/create.php" style="color:var(--purple);font-weight:600;">Learn More</a></p>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-top">
                    <div class="icon-badge">🎓</div>
                    <span class="delta up">+2.5%</span>
                </div>
                <div class="stat-value"><?= number_format($totalStudents) ?></div>
                <div class="stat-label">Total Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div class="icon-badge">📗</div>
                    <span class="delta up">+2.5%</span>
                </div>
                <div class="stat-value"><?= number_format($booksAvailable) ?></div>
                <div class="stat-label">Books available</div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div class="icon-badge">🔖</div>
                    <span class="delta down">-2.5%</span>
                </div>
                <div class="stat-value"><?= number_format($booksIssued) ?></div>
                <div class="stat-label">Book Issued</div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div class="icon-badge">📘</div>
                    <span class="delta up">+2.5%</span>
                </div>
                <div class="stat-value"><?= number_format($totalCopies) ?></div>
                <div class="stat-label">Book due for Return</div>
            </div>
        </div>

        <div class="panel-grid">
            <div class="card" style="grid-column: span 1;">
                <div class="card-header"><h3>Fees Pending</h3><a class="view-all" href="../Members/index.php">View All</a></div>
                <table class="data-table">
                    <tr><th>Students</th><th>Amount</th><th>Status</th></tr>
                    <tr>
                        <td class="person-cell"><span class="avatar-sm">A</span> Alany Haust</td>
                        <td>$31.48</td><td><span class="badge pending">Pending</span></td>
                    </tr>
                    <tr>
                        <td class="person-cell"><span class="avatar-sm">J</span> Jimmy Feron</td>
                        <td>$58.18</td><td><span class="badge pending">Pending</span></td>
                    </tr>
                </table>
            </div>

            <div class="card" style="grid-column: span 1;">
                <div class="card-header"><h3>Student Profile</h3><a class="view-all" href="../Members/index.php">View All</a></div>
                <table class="data-table">
                    <tr><th>Student</th><th>Class</th><th>Status</th></tr>
                    <?php foreach ($recent as $r): ?>
                    <tr>
                        <td class="person-cell"><span class="avatar-sm"><?= strtoupper(substr($r['student_name'],0,1)) ?></span> <?= htmlspecialchars($r['student_name']) ?></td>
                        <td><?= htmlspecialchars($r['due_date']) ?></td>
                        <td><span class="badge <?= strtolower($r['status']) ?>"><?= htmlspecialchars($r['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (!$recent): ?><tr><td colspan="3">No records yet.</td></tr><?php endif; ?>
                </table>
            </div>

            <div class="card" style="grid-column: span 1;">
                <button class="wishlist-btn">🔖 Wishlist</button>
                <?php foreach ($recentBooks as $b): ?>
                <div class="book-row">
                    <div class="book-cover">Cover</div>
                    <div>
                        <div class="b-title"><?= htmlspecialchars($b['title']) ?></div>
                        <div class="b-author">Added recently</div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (!$recentBooks): ?><p style="color:var(--text-muted);font-size:13px;">No books yet.</p><?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Book Issued / Returned</h3><a class="view-all" href="../Borrow/index.php">View All</a></div>
            <table class="data-table">
                <tr><th>#</th><th>Student Name</th><th>Book Name</th><th>Issued Date</th><th>Return Date</th><th>Status</th></tr>
                <?php foreach ($recent as $r): ?>
                <tr>
                    <td>B<?= str_pad($r['id'], 2, '0', STR_PAD_LEFT) ?></td>
                    <td class="person-cell"><span class="avatar-sm"><?= strtoupper(substr($r['student_name'],0,1)) ?></span> <?= htmlspecialchars($r['student_name']) ?></td>
                    <td><?= htmlspecialchars($r['book_name']) ?></td>
                    <td><?= htmlspecialchars($r['issue_date']) ?></td>
                    <td><?= htmlspecialchars($r['due_date']) ?></td>
                    <td><span class="badge <?= strtolower($r['status']) ?>"><?= htmlspecialchars($r['status']) ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$recent): ?><tr><td colspan="6">No borrow records yet.</td></tr><?php endif; ?>
            </table>
        </div>
    </main>
</div>
</body>
</html>
>>>>>>> origin/feature-panharith
