/**
 * Global AJAX Utility Service Wrapper
 */
const AppAJAX = {
    /**
     * Send a GET Request
     * @param {string} url - Target URL path endpoint
     * @returns {Promise<any>} Response parsing promise
     */
    async get(url) {
        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error('AJAX GET Execution Failed:', error);
            throw error;
        }
    },

    /**
     * Send a POST Request
     * @param {string} url - Target URL path endpoint
     * @param {Object|FormData} data - Payloads data package
     * @returns {Promise<any>} Response parsing promise
     */
    async post(url, data) {
        try {
            let bodyData = data;
            const headers = { 'X-Requested-With': 'XMLHttpRequest' };

            // If payload is plain object, transform to JSON package format
            if (!(data instanceof FormData)) {
                headers['Content-Type'] = 'application/json';
                bodyData = JSON.stringify(data);
            }

            const response = await fetch(url, {
                method: 'POST',
                headers: headers,
                body: bodyData
            });
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error('AJAX POST Execution Failed:', error);
            throw error;
        }
    }
};