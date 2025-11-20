class DelhiveryAPI {
    constructor(token) {
        this.token = token;
        this.baseUrl = 'https://track.delhivery.com';
    }

    async trackWaybill(waybill, ref_ids = null) {
        try {
            let params = new URLSearchParams();
            if (waybill) params.append('waybill', waybill);
            if (ref_ids) params.append('ref_ids', ref_ids);

            const response = await fetch(`${this.baseUrl}/api/v1/packages/json/?${params}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Token ${this.token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch tracking information');
            }

            return await response.json();
        } catch (error) {
            console.error('Tracking API Error:', error);
            throw error;
        }
    }

    async checkPincodeServiceability(pincode) {
        try {
            const response = await fetch(`${this.baseUrl}/c/api/pin-codes/json/?filter_codes=${pincode}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Token ${this.token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to check pincode serviceability');
            }

            const data = await response.json();
            return {
                serviceable: data.delivery_codes && data.delivery_codes.length > 0,
                details: data
            };
        } catch (error) {
            console.error('Pincode API Error:', error);
            throw error;
        }
    }
}
