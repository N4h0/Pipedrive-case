<?php
// Your Pipedrive API key
$api_key = '75aa16f02f782a452b8f0b97c75c3c5371f39937';

$api_key = getenv('Pipedrive_API_TOKEN');

echo("API-key: " . $api_key . "<br><br>");

// API endpoint for creating a lead
$url = 'https://api.pipedrive.com/v1/leads?api_token=' . $api_key;


// Lead data
$data = [
    "title" => "lead found in a back alley",
    "bfd7de6d20fd153c450e6a9fa9e687846a9fcb84" => 27, // housing_type
    "3803e2afd28dcf016b749abefce02952f63afb76" => 123, // property size
    "9ae06060d6cc0e797c5a5235e58e4d41afeace0b" => "Scary lead, maybe don't follow up?", // Comment
    "ad0c0c5d6d5b85d6ce81777139e55f5155d4bd2e" => 37, // deal_type
    "person_id" => 1
];

// Initialize cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Execute the request
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Debug: Print raw response
echo "Raw Response: " . $response;

// Decode the response
$result = json_decode($response, true);

// Check if the response is valid
if (!$result || !isset($result['success'])) {
    echo 'Error: Invalid response from Pipedrive API. Response: ' . $response;
    exit;
}

// Handle success or failure
if ($result['success']) {
    echo 'Lead created successfully!';
} else {
    echo 'Failed to create lead: ' . $result['error'];
}
?>
