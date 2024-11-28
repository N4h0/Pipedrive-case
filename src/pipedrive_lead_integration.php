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

//Data for å konvertere valg til tal for å passe til korrekt Id. 
$housingTypeMapping = [
    'Enebolig' => 33,
    'Leilighet' => 34,
    'Tomannsbolig' => 35,
    'Rekkehus' => 36,
    'Hytte' => 37,
    'Annet' => 38
];

$dealTypeMapping = [
    'Alle strømavtaler er aktuelle' => 39,
    'Fastpris' => 40,
    'Spotpris' => 41,
    'Kraftforvaltning' => 42,
    'Annen avtale/vet ikke' => 43
];

$contactTypeMapping = [
    'Privat' => 30,
    'Borettslag' => 31,
    'Bedrift' => 32
];

//Try-catch blokker for å konvertere housing type, deal type og contact type til ID for å kunne bruke den i pipedrive. 
try {
    $housingTypeValue = $housingTypeMapping[$input_data['housing_type']];
} catch (Exception $e) {
    echo "Error: Invalid housing_type value.";
    return false;
}

try {
    $dealTypeValue = $dealTypeMapping[$input_data['deal_type']];
} catch (Exception $e) {
    echo "Error: Invalid deal_type value.";
    return false;
}
try {
    $contactTypeValue = $contactTypeMapping[$input_data['contact_type']];
} catch (Exception $e) {
    echo "Error: Invalid contact_type value.";
    return false;
}

#Kode for å køyre på min pipedrive-konto for å få rett samanheng mellom idane til dykkar og mine options (Må slette):
$housingTypeValue -= 6; //Housing type
$dealTypeValue -= 6; //Deal type
$contactTypeValue += 8; //Contact type

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
    "fac468a2757b9bac40bef3d33c67f6e190f80b55" =>  $contactTypeValue, // contact type
];

$lead = [
    "title" => $input_data['name'] . " | " . $input_data['deal_type'] . " | " . $input_data['contact_type'], //Lager lead-tittel basert på data. Ville i ein reel case sjekka med kunde kva lead-tittel dei ønska og ikkje antatt.
    "bfd7de6d20fd153c450e6a9fa9e687846a9fcb84" => $housingTypeValue, //Housing type
    "3803e2afd28dcf016b749abefce02952f63afb76" => $input_data['property_size'], //Property size
    "9ae06060d6cc0e797c5a5235e58e4d41afeace0b" => $input_data['comment'], //Comment
    "ad0c0c5d6d5b85d6ce81777139e55f5155d4bd2e" => $dealTypeValue, //deal type
];

$organizationresponse = getRequest($organizationurl, "name", $organization["name"]);
$personresponse = getRequest($personurl, "name", $input_data['name']);
$leadresponse = getRequest($leadurl, "title", $lead['title']);


if (!empty($leadresponse['data']['items'])) {
    $leadcount = count($leadresponse['data']['items']);
    echo "Error: " . $leadcount . " duplicate(s) of lead with name: \"" . $lead["title"] . "\" already existing in pipedrive. Creation of duplicate leads not allowed.";
    return false;
}
if (!empty($organizationresponse['data']['items'])) {
    $lead['organization_id']  = $organizationresponse['data']['items'][0]['item']['id'];
    $person['org_id'] = $organizationresponse['data']['items'][0]['item']['id'];
    echo "Organization already existing, linking lead and person with existing organization.";
}
else { //Hvis get ikkje eksisterer organisasjoner med gitt navn lager me ein ny organisasjon og henter iden til denne
    $result = postRequest($organizationurl, $organization);
    echo "<br>";
    if ($result['success']) {
        echo '<br>Organization created successfully!';
    } else {
        echo '<br><br>Failed to create Organization: ' . $result['error'];
        '<br>' . $result['error_info'];
        return false;
    }
    $lead['organization_id'] = $result['data']['id'];
    $person['org_id'] = $result['data']['id'];
}
if (!empty($personresponse['data']['items'])) {
    $lead['person_id']  = $personresponse['data']['items'][0]['item']['id'];
    echo "Person already existing, linking lead with existing person.";    
}
else {
    $result = postRequest($personurl, $person);
    if ($result['success']) {
        echo '<br>person created successfully!';
    } else {
        echo '<br><br>Failed to create person: ' . $result['error'];
        '<br>' . $result['error_info'];
        return false;
    }
    $lead['person_id'] = $result['data']['id'];
}
$result = postRequest($leadurl, $lead);


if ($result['success']) {
    echo '<br>Lead created successfully!';
} else {
    echo 'Failed to create lead: ' . $result['error']; //Error seier kva feilen er
    echo '<br>' . $result['error_info'];
}