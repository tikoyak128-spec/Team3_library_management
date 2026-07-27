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

function highlightKeywords($text, $search) {
    if (empty($search)) {
        return htmlspecialchars($text);
    }
    $safeText = htmlspecialchars($text);
    $safeSearch = preg_quote(htmlspecialchars($search), '/');
    return preg_replace("/(" . $safeSearch . ")/i", '<mark class="search-highlight">$1</mark>', $safeText);
}

if (!empty($query)) {
    $searchTerm = "%{$query}%";

    try {
        // Query Books (Title, Author, Category, or ID)
        $stmtBook = $conn->prepare("
            SELECT b.*, a.name AS author_name, c.name AS category_name 
            FROM books b 
            LEFT JOIN authors a ON b.author_id = a.id 
            LEFT JOIN categories c ON b.category_id = c.id 
            WHERE LOWER(b.title) LIKE LOWER(:query) 
               OR LOWER(a.name) LIKE LOWER(:query) 
               OR LOWER(c.name) LIKE LOWER(:query)
               OR CAST(b.id AS CHAR) LIKE :query
            ORDER BY b.id DESC
        ");
        $stmtBook->execute([':query' => $searchTerm]);
        $books = $stmtBook->fetchAll(PDO::FETCH_ASSOC);

        // Query Members
        $stmtMember = $conn->prepare("
            SELECT * FROM members 
            WHERE LOWER(name) LIKE LOWER(:query) 
               OR LOWER(email) LIKE LOWER(:query) 
               OR phone LIKE :query 
               OR CAST(id AS CHAR) LIKE :query
            ORDER BY id DESC
        ");
        $stmtMember->execute([':query' => $searchTerm]);
        $members = $stmtMember->fetchAll(PDO::FETCH_ASSOC);

        // Query Borrowings
        $stmtBorrow = $conn->prepare("
            SELECT br.*, m.name AS student_name, bk.title AS book_title 
            FROM borrowings br 
            JOIN members m ON br.member_id = m.id 
            JOIN books bk ON br.book_id = bk.id 
            WHERE LOWER(m.name) LIKE LOWER(:query) 
               OR LOWER(bk.title) LIKE LOWER(:query) 
               OR CAST(br.id AS CHAR) LIKE :query
            ORDER BY br.id DESC
        ");
        $stmtBorrow->execute([':query' => $searchTerm]);
        $borrowings = $stmtBorrow->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $error_message = $e->getMessage();
    }
}
?>

<!-- Custom CSS for Centering Search Results -->
<style>
.search-container {
    max-width: 800px;
    margin: 30px auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.search-bar-card {
    width: 100%;
    background: #ffffff;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    margin-bottom: 24px;
    text-align: center;
}

.search-form {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-top: 15px;
}

.search-input {
    width: 70%;
    padding: 12px 16px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 15px;
    outline: none;
}

.btn-search {
    padding: 12px 24px;
    background: var(--primary-color, #6366f1);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.result-card {
    width: 100%;
    background: #ffffff;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-left: 5px solid #cbd5e1;
}

.result-card.available {
    border-left-color: #10b981; /* Green highlight for available */
}

.result-card.unavailable {
    border-left-color: #f59e0b; /* Yellow highlight for unavailable */
}

.status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    text-transform: capitalize;
}

.status-badge.available {
    background-color: #d1fae5;
    color: #065f46;
}

.status-badge.unavailable {
    background-color: #fef3c7;
    color: #92400e;
}
</style>

<!-- MAIN CONTAINER CENTERED IN SCREEN -->
<div class="search-container">

    <!-- Search Form Block -->
    <div class="search-bar-card">
        <h2>Search Library</h2>
        <p style="color: #64748b; font-size: 14px;">Find available books only</p>
        
        <form action="" method="GET" class="search-form">
            <input 
                type="text" 
                name="q" 
                class="search-input"
                value="<?php echo htmlspecialchars($query); ?>" 
                placeholder="Type book title, author, or category..." 
                required
            >
            <button type="submit" class="btn-search">
                <i class="fa-solid fa-magnifying-glass"></i> Search
            </button>
        </form>
    </div>

    <!-- SEARCH RESULTS SECTION -->
    <?php if (!empty($query)): ?>

        <div style="width: 100%;">
            <h3 style="margin-bottom: 16px; color: #334155;">
                <i class="fa-solid fa-book" style="color: var(--primary-color, #6366f1); margin-right: 8px;"></i>
                Available Books (<?php echo count($books); ?>)
            </h3>

            <?php if (empty($books)): ?>
                <div class="result-card" style="justify-content: center; color: #64748b;">
                    No matching books found for "<strong><?php echo htmlspecialchars($query); ?></strong>".
                </div>
            <?php else: ?>
                <?php foreach ($books as $b): 
                    $isAvailable = strtolower($b['status']) === 'available';
                ?>
                    <div class="result-card <?php echo $isAvailable ? 'available' : 'unavailable'; ?>">
                        <div>
                            <h4 style="font-size: 18px; margin-bottom: 6px;">
                                <?php echo highlightKeywords($b['title'], $query); ?>
                            </h4>
                            <p style="color: #64748b; font-size: 14px; margin: 2px 0;">
                                <strong>Author:</strong> <?php echo htmlspecialchars($b['author_name'] ?? 'N/A'); ?> | 
                                <strong>Category:</strong> <?php echo htmlspecialchars($b['category_name'] ?? 'Uncategorized'); ?>
                            </p>
                        </div>
                        
                        <div>
                            <span class="status-badge <?php echo $isAvailable ? 'available' : 'unavailable'; ?>">
                                <?php echo ucfirst(htmlspecialchars($b['status'])); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</div>

<?php include '../Includes/footer.php'; ?>