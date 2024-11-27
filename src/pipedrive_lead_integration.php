<?php
//Inspirasjon for grunnleggande setup: https://endgrate.com/blog/using-the-pipedrive-api-to-create-or-update-leads-in-php
//

require __DIR__ . '/../vendor/autoload.php'; //Brukt for å bruke lokale variablar for å gøyme API-nøklen
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..'); //Må spesifisere at .env ligg i nivået over. 
$dotenv->load();

$api_key = $_ENV['Pipedrive_API_TOKEN']; //Henter API-nøklen
$leadurl = 'https://api.pipedrive.com/v1/leads?api_token=' . $api_key; //API for å legge til leads
$personurl = 'https://api.pipedrive.com/v1/persons?api_token=' . $api_key; //API for å legge til personar
$organizationurl = 'https://api.pipedrive.com/v1/organizations?api_token=' . $api_key; //API for å legge til organisasjonar


// Lead data
$lead = [
    "title" => "lead found in a back alley",
    "bfd7de6d20fd153c450e6a9fa9e687846a9fcb84" => 27, // housing_type
    "3803e2afd28dcf016b749abefce02952f63afb76" => 123, // property size
    "9ae06060d6cc0e797c5a5235e58e4d41afeace0b" => "Scary lead, maybe don't follow up?", // Comment
    "ad0c0c5d6d5b85d6ce81777139e55f5155d4bd2e" => 37, // deal_type
    "person_id" => 1
];

$organization = [
    "name" => "Svoren Ninjatjenester"
];

$person = [
    "name" => "Ni(e)ls"
];

function postRequest($adress, $data){
    $ch = curl_init($adress);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch); // Execute the request
    $result = json_decode($response, true);
    curl_close($ch);
    return $result;
}

$result = postRequest($personurl, $person);

if ($result['success']) {
    echo 'Organization created successfully!';
} else {
    echo '<br><br>Failed to create lead: ' . $result['error'];
}

/*
$result = postRequest($organizationurl, $organization);

if ($result['success']) {
    echo 'Organization created successfully!';
} else {
    echo '<br><br>Failed to create lead: ' . $result['error'];
}


$result = postRequest($leadurl, $lead);
// Handle success or failure
if ($result['success']) {
    echo 'Lead created successfully!';
} else {
    echo '<br><br>Failed to create lead: ' . $result['error'];
}
*/