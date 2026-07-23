document.addEventListener('DOMContentLoaded', () => {
    const globalSearchInput = document.getElementById('globalSearch');
    const filterForm = document.getElementById('filterForm');
    const sortSelect = document.getElementById('sortSelect');
    const filterSelect = document.getElementById('filterSelect');
    const resultsContainer = document.getElementById('product-container'); 

    const submitCombinedFilters = async () => {
        if (!filterForm) return;
        const formData = new FormData(filterForm);

        if (globalSearchInput) {
            formData.set('search', globalSearchInput.value.trim());
        }

        try {
            const endpoint = filterForm.action || 'controllers/filterHandler.php';
            const result = await AppAJAX.post(endpoint, formData);

            if (result && result.success) {
                if (resultsContainer) {
                    resultsContainer.innerHTML = result.html;
                }
            } else {
                console.error('Server returned an error:', result?.message || 'Unknown error');
            }
        } catch (error) {
            console.error('Failed to submit filters via AJAX:', error);
        }
    };

    if (sortSelect) sortSelect.addEventListener('change', submitCombinedFilters);
    if (filterSelect) filterSelect.addEventListener('change', submitCombinedFilters);

    if (globalSearchInput) {
        globalSearchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                submitCombinedFilters();
            }
        });
    }
});