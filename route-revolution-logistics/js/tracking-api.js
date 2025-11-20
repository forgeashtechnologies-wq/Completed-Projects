class TrackingAPI {
    constructor(apiKey) {
        this.apiKey = apiKey;
        // Update to use your domain
        this.baseUrl = '/proxy.php';
    }

    async trackShipment(trackingInput) {
        try {
            const response = await fetch(`${this.baseUrl}?tracking=${encodeURIComponent(trackingInput)}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.Error || 'Failed to fetch tracking information');
            }

            return await response.json();
        } catch (error) {
            console.error('Tracking API Error:', error);
            throw error;
        }
    }
}