<?php
// Helper to detect current page and add 'active' class
$current_page = basename($_SERVER['PHP_SELF']);
require_once __DIR__ . '/../Authentication/auth_check.php';
?>
<aside class="sidebar">
    <!-- Branding -->
    <div class="sidebar-brand">
        <i class="fa-solid fa-cube logo-icon"></i>
        <h2>Library Pro</h2>
    </div>

    <!-- Navigation Links -->
    <nav class="sidebar-menu">
        <ul>
            <li class="<?php echo ($current_page == 'index.php' && strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>Dashboard/index.php">
                    <i class="fa-solid fa-table-columns"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'members') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>members/index.php">
                    <i class="fa-solid fa-users"></i> <span>Students</span>
                </a>
            </li>
            <li class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'Books') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>Books/index.php">
                    <i class="fa-solid fa-book-open"></i> <span>Books Available</span>
                </a>
            </li>
            <li class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'Borrow') !== false || strpos($_SERVER['REQUEST_URI'], 'Return') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>Borrow/index.php">
                    <i class="fa-solid fa-right-left"></i> <span>Borrowed Books</span>
                </a>
            </li>
            <li class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'categories') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>categories/index.php">
                    <i class="fa-solid fa-tags"></i> <span>Categories</span>
                </a>
            </li>
            <li class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'search') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>search/search.php">
                    <i class="fa-solid fa-pen-nib"></i> <span>Search</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Upgrade Promo Card -->
    <div class="sidebar-promo">
        <div class="rocket-img">🚀</div>
        <p>Want to upgrade?</p>
        <button class="btn-upgrade">Upgrade now</button>
    </div>

    <!-- User Profile & Logout at Bottom -->
    <div class="sidebar-footer">
        
       <a href="<?php echo BASE_URL; ?>Authentication/logout.php" class="btn-logout">
    <i class="fa-solid fa-right-from-bracket"></i> Logout
</a>
    </div>
</aside>

<!-- Start of the Main Content wrapper right after Sidebar -->
<div class="main-layout">