document.addEventListener('DOMContentLoaded', () => {
    // 1. Generic Dynamic Delete Confirmation
    // Add the class 'btn-delete' to any delete button or icon across the application
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            const itemType = button.getAttribute('data-item') || 'item';
            const confirmMessage = `Are you sure you want to delete this ${itemType}? This action cannot be undone.`;
            
            if (!confirm(confirmMessage)) {
                event.preventDefault(); // Blocks the link or form submission if cancelled
            }
        });
    });

    // 2. Clear Active Status Flash Messages Automatically after 4 seconds
    // Useful for showing "Book added successfully!" alerts that disappear on their own
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });
});