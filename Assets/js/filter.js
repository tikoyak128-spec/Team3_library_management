document.addEventListener('DOMContentLoaded', () => {
    // 1. Grab DOM Elements
    const globalSearchInput = document.getElementById('globalSearch');
    const filterForm = document.getElementById('filterForm');
    const sortSelect = document.getElementById('sortSelect');
    const filterSelect = document.getElementById('filterSelect');

    /**
     * Helper function to safely inject or update the search value 
     * inside the hidden field of the filter form before submission.
     */
    const submitCombinedFilters = () => {
        if (!filterForm) return;

        // Find or create a hidden input for the search parameter
        let hiddenSearchInput = filterForm.querySelector('input[name="search"]');
        
        if (!hiddenSearchInput) {
            hiddenSearchInput = document.createElement('input');
            hiddenSearchInput.type = 'hidden';
            hiddenSearchInput.name = 'search';
            filterForm.appendChild(hiddenSearchInput);
        }

        // Sync the current navbar text to the form
        if (globalSearchInput) {
            hiddenSearchInput.value = globalSearchInput.value.trim();
        }

        // Send it off to the backend controller
        filterForm.submit();
    };

    // 2. Event Listeners for Dropdowns (Change Event)
    if (sortSelect) {
        sortSelect.addEventListener('change', () => {
            submitCombinedFilters();
        });
    }

    if (filterSelect) {
        filterSelect.addEventListener('change', () => {
            submitCombinedFilters();
        });
    }

    // 3. Event Listener for the Global Navbar Search (Enter Key)
    if (globalSearchInput) {
        globalSearchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault(); // Stop page from doing standard form reload
                submitCombinedFilters();
            }
        });
    }
});