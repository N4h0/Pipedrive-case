<?php
require __DIR__ . '/../vendor/autoload.php'; //Brukt for å bruke lokale variablar for å gøyme API-nøklen
//Henter eigenlagde funksjonar
require __DIR__ . '/postRequest.php'; //postRequest blir brukt for å sende alle post-requests. 
require __DIR__ . '/getRequest.php'; //getRequest blir brukt for å sende alle get-requests. 
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..'); //Må spesifisere at .env ligg i nivået over. 
$dotenv->load(); //Henter lokale variablar (API_KEY). Denne blir brukt i postRequest.php og getRequest.php

$leadurl = 'https://api.pipedrive.com/v1/leads';  //API for å legge til leads
$personurl = 'https://api.pipedrive.com/v1/persons'; //API for å legge til personar
$organizationurl = 'https://api.pipedrive.com/v1/organizations'; //API for å legge til organisasjonar
$input_data = include('../exampledata.php'); //Henter eksempeldataen.

//Array med informasjon eg set som obligatorisk. I ein reel situasjon ville eg ha sjekka opp nøyare kva informasjon som faktisk er obligatorisk
//Alle input-felt de har gitt er her obligatoriske med unntak av comment. 
$required_information = [
    'name',
    'phone',
    'email',
    'housing_type',
    'property_size',
    'deal_type',
    'contact_type'
];

//Looper gjennom og sjekker at det alle obligatoriske felt eksisterer og har verdiar. Skriv ut ei feilmelding og stopper ved manglar. 
foreach ($required_information as $field) {
    if (empty($input_data[$field])) {
        echo "Error: missing required field: " . $field;
        return false;
    }
};

//Oppretter person, organisasjon og lead som skal bli sendt til pipedrive
$organization = [
    "name" => "Johan Tryti sin organisasjon", //Enkel statisk verdi som organisasjonsnavn. 
];

$person = [
    "name" => $input_data['name'],
    "phone" => $input_data['phone'],
    "email" => $input_data['email'],
    "fac468a2757b9bac40bef3d33c67f6e190f80b55" =>  "40", // contact type
];

$lead = [
    "title" => $input_data['name'] . " | " . $input_data['deal_type'] . " | " . $input_data['contact_type'], //Lager eigendefinert lead-tittel basert på dataen.
    "bfd7de6d20fd153c450e6a9fa9e687846a9fcb84" => preg_match('/\((\d+)\)/', $input_data['housing_type'], $matches) ? $matches[1] : null, //Housing type
    "3803e2afd28dcf016b749abefce02952f63afb76" => $input_data['property_size'], //Property size
    "9ae06060d6cc0e797c5a5235e58e4d41afeace0b" => $input_data['comment'], //Comment
    "ad0c0c5d6d5b85d6ce81777139e55f5155d4bd2e" => preg_match('/\((\d+)\)/', $input_data['deal_type'], $matches) ? $matches[1] : null, //deal type
];

$leadresponse = getRequest($leadurl, "title", $lead['title']);
$personresponse = getRequest($personurl, "name", $input_data['name']);
$organizationresponse = getRequest($organizationurl, "name", $organization["name"]);

echo "<br> organisasjon:<br><br>";
echo count($organizationresponse['data']['items']);
echo "<br>";
echo count($personresponse['data']['items']);
echo "<br> lead:<br><br>";
echo count($leadresponse['data']['items']);

#Kode for å køyre på min pipedrive-konto for å få rett samanheng mellom idane til dykkar og mine options (Må slette):
$lead["bfd7de6d20fd153c450e6a9fa9e687846a9fcb84"] += 6;
$lead["ad0c0c5d6d5b85d6ce81777139e55f5155d4bd2e"] += 6;

// Increase contact type in $person by 8
$person["fac468a2757b9bac40bef3d33c67f6e190f80b55"] += 8;


echo json_encode($leadresponse);
echo "<br> person:<br><br>";
echo json_encode($personresponse);
echo "<br> organisasjon:<br><br>";
echo json_encode($organizationresponse);
echo "<br>";


$result = postRequest($organizationurl, $organization);


echo "<br>";

if ($result['success']) {
    echo '<br>Organization created successfully!';
} else {
    echo '<br><br>Failed to create Organization: ' . $result['error'];
    '<br>' . $result['error_info'];
}

$person['org_id'] = $result['data']['id'];
$lead['organization_id'] = $result['data']['id']; //Sjølv om lead sin API for organisasjon er org_id, funka det berre når eg brukte organization_id (som vist i pipedrive tutorialen: https://developers.pipedrive.com/tutorials/adding-leads-to-pipedrive?step=5).


if ($result['success']) {
    echo '<br>person created successfully!';
} else {
    echo '<br><br>Failed to create person: ' . $result['error'];
    '<br>' . $result['error_info'];
}

$lead['person_id'] = $result['data']['id'];
// Handle success or failure
if ($result['success']) {
    echo '<br>Lead created successfully!';
} else {
    echo 'Failed to create lead: ' . $result['error']; //Error seier kva feilen er
    echo '<br>' . $result['error_info'];
}