<?php
require __DIR__ . '/../vendor/autoload.php'; //Brukt for å bruke lokale variablar for å gøyme API-nøklen
//Henter eigenlagde funksjonar
require __DIR__ . '/postRequest.php'; //postRequest blir brukt for å sende alle post-requests. 
require __DIR__ . '/getRequest.php'; //getRequest blir brukt for å sende alle get-requests. 
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..'); //Må spesifisere at .env ligg i nivået over. 
$dotenv->load();

$leadurl = 'https://api.pipedrive.com/v1/leads';  //API for å legge til leads
$personurl = 'https://api.pipedrive.com/v1/persons'; //API for å legge til personar
$organizationurl = 'https://api.pipedrive.com/v1/organizations'; //API for å legge til organisasjonar
$exampledata = include('../exampledata.php'); //Henter eksempeldataen.

$response = getRequest($leadurl, "title", "Kriminalomsorgen lead");
$response2 = getRequest($personurl, "name", "Henrik etternavn");
$response3 = getRequest($organizationurl, "name", "Svoren Ninjatjenester");
echo "<br> lead:<br><br>";
echo json_encode($response);
echo "<br> person:<br><br>";
echo json_encode($response2);
echo "<br> organisasjon:<br><br>";
echo json_encode($response3);
echo "<br>";

//Lead-informasjon:
$title = $exampledata['name'] . " | " . $exampledata['deal_type'] . " | " . $exampledata['contact_type']; //Lager eigendefinert lead-tittel basert på dataen.
$housing_type = $exampledata['housing_type'];
$proptery_size = $exampledata['property_size'];
$comment = $exampledata['comment'];
$dealtype = $exampledata['deal_type'];

//person-informasjon:
$contact_type = $exampledata['contact_type'];
$person_name = $exampledata['name'];
$phone = $exampledata['phone'];
$email = $exampledata['email'];

//Organisasjonsinfomasjon
$organization_name = $exampledata['organization'];

// IDs of person and organization
$person=$exampledata['name'];
$organization = $exampledata['organization'];

// Lead data
$lead = [
    "title" => $title,
    "bfd7de6d20fd153c450e6a9fa9e687846a9fcb84" => 27, // housing_type
    "3803e2afd28dcf016b749abefce02952f63afb76" => 123, // property size
    "9ae06060d6cc0e797c5a5235e58e4d41afeace0b" => "Scary lead, maybe don't follow up?", // Comment
    "ad0c0c5d6d5b85d6ce81777139e55f5155d4bd2e" => 37,
];

$organization = [
    "name" => $organization_name,
];

$person = [
    "name" => $person_name,
    "phone" => $phone,
    "email" => $email,
    "fac468a2757b9bac40bef3d33c67f6e190f80b55" =>  "39", // contact type
];

$result = postRequest($organizationurl, $organization);


echo "<br>";

if ($result['success']) {
    echo 'Organization created successfully!';
} else {
    echo '<br><br>Failed to create Organization: ' . $result['error'];
}

$person['org_id'] = $result['data']['id'];
$lead['organization_id'] = $result['data']['id']; //Merk: sjølv om denne sin API er org_id, funka det berre når eg brukte organization_id (som vist i pipedrive tutorialen: https://developers.pipedrive.com/tutorials/adding-leads-to-pipedrive?step=5).

$result = postRequest($personurl, $person);
echo "<br>";

if ($result['success']) {
    echo 'person created successfully!';
} else {
    echo '<br><br>Failed to create person: ' . $result['error'];
}
$lead['person_id'] = $result['data']['id'];
echo "<br>";
$result = postRequest($leadurl, $lead);
// Handle success or failure
if ($result['success']) {
    echo 'Lead created successfully!';
} else {
    echo 'Failed to create lead: ' . $result['error']; //Error seier kva feilen er
    echo '<br>' . $result['error_info']; //Error info gir meir informasjon
}