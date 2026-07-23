<header class="navbar">
    <div class="navbar-date">
        <div class="date-picker-trigger">
            <i class="fa-regular fa-calendar"></i>
            <span>Apr 14 - May 18, 2023</span>
            <i class="fa-solid fa-chevron-down arrow"></i>
        </div>
    </div>

    <div class="navbar-search">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="text" id="globalSearch" placeholder="Search...">
    </div>

    <div class="navbar-profile">
        <img src="<?php echo BASE_URL; ?>Assets/images/user-placeholder.jpg" alt="Profile" class="avatar-circle">
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const dateSpan = document.querySelector('.navbar-date .date-picker-trigger span');
    if (dateSpan) {
        const now = new Date();
        const currentYear = now.getFullYear();
        const options = { month: 'short', day: 'numeric' };
        const formattedCurrent = now.toLocaleDateString('en-US', options);
        dateSpan.textContent = ` ${formattedCurrent}, ${currentYear}`;
    }
});
</script>

<!-- Main content viewport container opening -->
<main class="content-viewport">