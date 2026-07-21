<?php
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