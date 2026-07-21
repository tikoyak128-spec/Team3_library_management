<div class="filter-bar-container">
    <div class="filter-left-actions">
        <!-- Optional contextual actions like "Add Book" can be placed here -->
    </div>
    
    <form id="filterForm" class="filter-right-actions" method="GET" action="">
        <!-- Preserves the existing search keyword if it came from the top navbar -->
        <?php if(isset($_GET['search'])): ?>
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>">
        <?php endif; ?>

        <!-- Sorting Selector -->
        <div class="custom-dropdown">
            <i class="fa-solid fa-arrow-down-z-a dropdown-icon"></i>
            <select name="sort" id="sortSelect">
                <option value="latest" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'latest') ? 'selected' : ''; ?>>Sorting: Latest</option>
                <option value="alphabetical" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'alphabetical') ? 'selected' : ''; ?>>Sorting: A-Z</option>
                <option value="popular" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'popular') ? 'selected' : ''; ?>>Sorting: Popular</option>
            </select>
        </div>

        <!-- Category/Filter Selector -->
        <div class="custom-dropdown">
            <i class="fa-solid fa-filter dropdown-icon"></i>
            <select name="category" id="filterSelect">
                <option value="">Filter: All Categories</option>
                <!-- These values can be wired to dynamic entries from your database later -->
                <option value="1" <?php echo (isset($_GET['category']) && $_GET['category'] === '1') ? 'selected' : ''; ?>>Tech</option>
                <option value="2" <?php echo (isset($_GET['category']) && $_GET['category'] === '2') ? 'selected' : ''; ?>>Design</option>
                <option value="3" <?php echo (isset($_GET['category']) && $_GET['category'] === '3') ? 'selected' : ''; ?>>Business</option>
            </select>
        </div>
    </form>
</div>