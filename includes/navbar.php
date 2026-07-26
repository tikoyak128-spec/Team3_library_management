<header class="navbar">
    <div class="navbar-date">
        <div class="date-picker-trigger">
            <i class="fa-regular fa-calendar"></i>
            <span>Apr 14 - May 18, 2023</span>
            <i class="fa-solid fa-chevron-down arrow"></i>
        </div>
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