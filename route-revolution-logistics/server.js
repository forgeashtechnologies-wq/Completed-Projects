require('dotenv').config();
const express = require('express');
const cors = require('cors');
const path = require('path');

// For node-fetch@3, we need to use dynamic import
const fetchModule = import('node-fetch').then(({ default: fetch }) => fetch);

const app = express();

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.static(path.join(__dirname)));

// Environment variables
const DELHIVERY_API_TOKEN = process.env.DELHIVERY_API_TOKEN;
const API_BASE_URL = 'https://track.delhivery.com/api/v1/packages/json';

// Test route
app.get('/test', (req, res) => {
    res.json({ message: 'Server is running!' });
});

// Tracking endpoint
app.post('/api/track', async (req, res) => {
    try {
        const fetch = await fetchModule;
        const { trackingNumber } = req.body;
        console.log('Received tracking request for:', trackingNumber);

        if (!trackingNumber) {
            return res.status(400).json({ error: 'Tracking number is required' });
        }

        if (!DELHIVERY_API_TOKEN) {
            console.error('Delhivery API token not configured');
            return res.status(500).json({ error: 'Server configuration error' });
        }

        const url = `${API_BASE_URL}/?waybill=${trackingNumber}`;
        console.log('Requesting URL:', url);

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Token ${DELHIVERY_API_TOKEN}`,
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();
        
        // Handle Delhivery API error responses
        if (!data.Success && data.Error) {
            return res.status(404).json({ 
                error: 'Shipment not found',
                details: data.Error || data.rmk || 'No additional details available'
            });
        }

        if (!data || !data.ShipmentData || data.ShipmentData.length === 0) {
            return res.status(404).json({ error: 'Could not find shipment with the provided number' });
        }

        res.json(data);

    } catch (error) {
        console.error('Tracking API Error:', error);
        res.status(500).json({ 
            error: 'Error tracking shipment', 
            details: error.message 
        });
    }
});

const PORT = process.env.PORT || 3000;

app.listen(PORT, () => {
    console.log(`Server running on http://localhost:${PORT}`);
    console.log(`API Token configured: ${!!DELHIVERY_API_TOKEN}`);
});
