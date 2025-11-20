function openPaymentQRModal() {
    console.log('Payment QR Modal is being opened');
    const modalId = 'paymentQRModal';
    const existingModal = document.getElementById(modalId);
    
    if (!existingModal) {
        // Create the modal if it doesn't exist
        const paymentModal = document.createElement('div');
        paymentModal.id = modalId;
        paymentModal.className = 'modal';
        paymentModal.innerHTML = `
            <div class="modal-content">
                <span class="close-modal" onclick="closeModal('${modalId}')">&times;</span>
                <h2>Payment QR Code</h2>
                <div class="payment-section">
                    <p>Scan the QR code below to make your payment:</p>
                    <div class="qr-container">
                        <img 
                            src="images/route-revolution-payment-qr.png" 
                            alt="Route Revolution Payment QR" 
                            class="payment-qr"
                        >
                    </div>
                    <div class="payment-details">
                        <h3>Payment Instructions</h3>
                        <ol>
                            <li>Open your preferred UPI payment app</li>
                            <li>Scan the QR code</li>
                            <li>Enter the amount as per the shipping charges</li>
                            <li>Complete the payment</li>
                            <li>Keep the payment receipt for reference</li>
                        </ol>
                    </div>
                    <div class="payment-contact">
                    <p>Need help? Contact us:</p>
                    <div style="margin-top: 0rem;">
                        <span>📞 +91 9994344150</span> |
                        <span>📞 +91 8300898507</span>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(paymentModal);
    }
    
    // Display the modal
    const modal = document.getElementById(modalId);
    modal.style.display = 'flex';
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

document.addEventListener('DOMContentLoaded', function() {
    const trackingForm = document.getElementById('trackingForm');
    const trackingInput = document.getElementById('trackingInput');
    const trackingResult = document.getElementById('trackingResult');

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        try {
            const date = new Date(dateString);
            return date.toLocaleString('en-IN', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
        } catch (e) {
            return 'N/A';
        }
    }

    function getStatusColor(status) {
        status = status?.toLowerCase() || '';
        const statusColors = {
            'delivered': '#2ed573',
            'in transit': '#1e90ff',
            'out for delivery': '#ff6b81',
            'pending': '#ffa502',
            'manifested': '#a8e6cf'
        };
        
        for (const [key, color] of Object.entries(statusColors)) {
            if (status.includes(key)) return color;
        }
        return '#ff4757'; // default color
    }

    trackingForm.addEventListener('submit', async function(event) {
        event.preventDefault();
        
        const trackingNumber = trackingInput.value.trim();
        if (!trackingNumber) {
            trackingResult.innerHTML = '<p class="tracking-details" style="color: #ff4757;">Please enter a tracking number</p>';
            return;
        }

        try {
            // Show loading state
            trackingResult.innerHTML = `
                <div class="tracking-details" style="text-align: center; padding: 20px;">
                    <p style="color: #ff4757;">Looking up your shipment...</p>
                    <p style="color: #666; font-size: 0.9rem;">Tracking Number: ${trackingNumber}</p>
                </div>
            `;
            
            const response = await fetch(`/proxy.php?tracking=${encodeURIComponent(trackingNumber)}`);
            const data = await response.json();

            if (!response.ok || data.error) {
                throw new Error(data.error || 'Failed to fetch tracking information');
            }

            if (data.ShipmentData && data.ShipmentData.length > 0) {
                const shipment = data.ShipmentData[0].Shipment;
                const currentStatus = shipment.Status?.Status || 'N/A';
                const statusColor = getStatusColor(currentStatus);

                let tableHtml = `
                    <style type="text/css">
                        .tftable {
                            font-size: 14px;
                            color: #333333;
                            width: 100%;
                            border-width: 1px;
                            border-color: #ff4757;
                            border-collapse: collapse;
                            margin-top: 20px;
                            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                        }
                        .tftable th {
                            font-size: 16px;
                            background-color: #ff4757;
                            border-width: 1px;
                            padding: 12px;
                            border-style: solid;
                            border-color: #ff4757;
                            color: white;
                            text-align: left;
                        }
                        .tftable tr {
                            background-color: #ffffff;
                        }
                        .tftable td {
                            font-size: 14px;
                            border-width: 1px;
                            padding: 12px;
                            border-style: solid;
                            border-color: #ff4757;
                        }
                        .tftable tr:hover {
                            background-color: #fff5f5;
                        }
                        .current-status {
                            color: ${statusColor};
                            font-weight: bold;
                        }
                        .status-chip {
                            display: inline-block;
                            padding: 4px 12px;
                            border-radius: 50px;
                            background-color: ${statusColor};
                            color: white;
                            font-size: 0.9rem;
                        }
                    </style>

                    <div class="tracking-details">
                        <div style="text-align: center; margin-bottom: 20px;">
                            <h4 style="color: #ff4757; margin-bottom: 10px;">Shipment Status</h4>
                            <div class="status-chip">${currentStatus}</div>
                        </div>

                        <table class="tftable" border="1">
                            <tr>
                                <th>Tracking Number</th>
                                <th>Origin</th>
                                <th>Destination</th>
                                <th>Expected Delivery</th>
                            </tr>
                            <tr>
                                <td>${shipment.AWB || shipment.ReferenceNo || trackingNumber}</td>
                                <td>${shipment.Origin || 'N/A'}</td>
                                <td>${shipment.Destination || 'N/A'}</td>
                                <td>${formatDate(shipment.ExpectedDeliveryDate)}</td>
                            </tr>
                        </table>
                    </div>
                `;

                // Add tracking history if available
                if (shipment.Scans?.length > 0) {
                    tableHtml += `
                        <div class="tracking-details" style="margin-top: 20px;">
                            <h4 style="color: #ff4757; margin-bottom: 10px;">Tracking History</h4>
                            <table class="tftable" border="1">
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <th>Location</th>
                                    <th>Instructions</th>
                                </tr>
                    `;
                    
                    // Sort scans by date in descending order
                    const sortedScans = shipment.Scans
                        .map(scan => scan.ScanDetail)
                        .sort((a, b) => new Date(b.ScanDateTime) - new Date(a.ScanDateTime));

                    sortedScans.forEach(scan => {
                        tableHtml += `
                            <tr>
                                <td>${formatDate(scan.ScanDateTime)}</td>
                                <td>${scan.Scan || 'N/A'}</td>
                                <td>${scan.ScannedLocation || 'N/A'}</td>
                                <td>${scan.Instructions || 'N/A'}</td>
                            </tr>
                        `;
                    });
                    tableHtml += '</table></div>';
                }

                trackingResult.innerHTML = tableHtml;
            } else {
                trackingResult.innerHTML = `
                    <div class="tracking-details" style="text-align: center; padding: 20px;">
                        <p style="color: #ff4757;">No tracking information found</p>
                        <p style="color: #666; font-size: 0.9rem;">Please verify your tracking number and try again</p>
                        <p style="color: #666; font-size: 0.9rem;">Tracking Number: ${trackingNumber}</p>
                    </div>
                `;
            }
        } catch (error) {
            trackingResult.innerHTML = `
                <div class="tracking-details" style="text-align: center; padding: 20px;">
                    <p style="color: #ff4757;">${error.message}</p>
                    <p style="color: #666; font-size: 0.9rem;">Tracking Number: ${trackingNumber}</p>
                </div>
            `;
            console.error('Tracking error:', error);
        }
    });
});
