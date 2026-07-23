document.addEventListener('DOMContentLoaded', () => {
    // 1. Grab DOM Elements
    const globalSearchInput = document.getElementById('globalSearch');
    const filterForm = document.getElementById('filterForm');
    const sortSelect = document.getElementById('sortSelect');
    const filterSelect = document.getElementById('filterSelect');
    
    // Optional: Container where your dynamic results/products will be rendered
    const resultsContainer = document.getElementById('product-container'); 

    /**
     * Helper function to gather form parameters and submit via AJAX 
     * instead of triggering a full page reload.
     */
    const submitCombinedFilters = async () => {
        if (!filterForm) return;

        // Gather all form fields using FormData
        const formData = new FormData(filterForm);

        // Include the global search input value if it exists outside the form
        if (globalSearchInput) {
            formData.set('search', globalSearchInput.value.trim());
        }

        try {
            // Determine the endpoint from the form's action attribute, or use a default
            const endpoint = filterForm.action || 'controllers/filterHandler.php';

            // Send data asynchronously using our Ajax utility
            const result = await Ajax.post(endpoint, formData);

            // Handle the response (assuming your PHP backend returns JSON with a success flag and HTML string)
            if (result && result.success) {
                if (resultsContainer) {
                    resultsContainer.innerHTML = result.html;
                } else {
                    console.warn('Results container #product-container not found in DOM.');
                }
            } else {
                console.error('Server returned an error:', result?.message || 'Unknown error');
            }
        } catch (error) {
            console.error('Failed to submit filters via AJAX:', error);
        }
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
                event.preventDefault(); // Stop standard form submission
                submitCombinedFilters();
            }
        });
    }
});