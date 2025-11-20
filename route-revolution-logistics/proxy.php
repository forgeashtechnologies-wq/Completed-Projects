<?php
header('Access-Control-Allow-Origin: https://routerevolution.in');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

$api_token = '7cd40a23285441970a7fb2dcbff024ffd6c81acd';

if (!isset($_GET['tracking'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Tracking number is required']);
    exit;
}

$tracking_input = $_GET['tracking'];

// Function to determine if input is a reference ID
function isReferenceId($input) {
    // Convert to uppercase for pattern matching
    $upper = strtoupper($input);
    
    // Common reference ID patterns
    $patterns = [
        '/^RR/',      // Starts with RR
        '/^RRCC/',    // Starts with RRCC
        '/^TM/',      // Starts with TM
        '/^\d{2}RR/', // Starts with numbers followed by RR
        '/^CC/'       // Starts with CC
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $upper)) {
            return true;
        }
    }
    
    return false;
}

// First try as reference ID
$is_ref_id = isReferenceId($tracking_input);
$url = 'https://track.delhivery.com/api/v1/packages/json/?' . 
       ($is_ref_id ? 'ref_ids=' : 'waybill=') . 
       urlencode($tracking_input);

error_log("First attempt - Tracking Input: " . $tracking_input . " | Using as ref_id: " . ($is_ref_id ? "Yes" : "No"));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Token ' . $api_token,
    'Content-Type: application/json'
));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// If first attempt fails, try the opposite approach
if ($http_code !== 200 || !$response || strpos($response, '"ShipmentData":[]') !== false) {
    error_log("First attempt failed, trying opposite approach");
    
    // Try opposite parameter
    $url = 'https://track.delhivery.com/api/v1/packages/json/?' . 
           ($is_ref_id ? 'waybill=' : 'ref_ids=') . 
           urlencode($tracking_input);
    
    curl_setopt($ch, CURLOPT_URL, $url);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
}

curl_close($ch);

if ($http_code !== 200) {
    error_log("API Error: HTTP Code " . $http_code . " for tracking number: " . $tracking_input);
    http_response_code($http_code);
    echo json_encode(['error' => 'Failed to fetch tracking information']);
    exit;
}

// Validate JSON response
$decoded = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON Parse Error for tracking number: " . $tracking_input);
    http_response_code(500);
    echo json_encode(['error' => 'Invalid response from tracking server']);
    exit;
}

// Check if we got valid shipment data
if (!isset($decoded['ShipmentData']) || empty($decoded['ShipmentData'])) {
    error_log("No shipment data found for: " . $tracking_input);
    echo json_encode(['error' => 'No tracking information found']);
    exit;
}

echo $response;