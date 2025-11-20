// LOCATION: public_html/js/config.js
// Replace everything in this file with:

const config = {
    API_BASE_URL: 'https://track.delhivery.com/api/v1/packages/json',
    DELHIVERY_API_TOKEN: '7cd40a23285441970a7fb2dcbff024ffd6c81acd'  // Your actual token from .env file
};

// If using as a module
if (typeof module !== 'undefined' && module.exports) {
    module.exports = config;
}