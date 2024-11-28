<?php
require __DIR__ . '/../vendor/autoload.php'; //Brukt for å bruke lokale variablar for å gøyme API-nøklen
//Henter eigenlagde funksjonar
require __DIR__ . '/postRequest.php'; //postRequest blir brukt for å sende alle post-requests. 
require __DIR__ . '/getRequest.php'; //getRequest blir brukt for å sende alle get-requests. 
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..'); //Må spesifisere at .env ligg i nivået over. 
$dotenv->load(); //Henter lokale variablar (API_KEY). Denne blir brukt i postRequest.php og getRequest.php

$testdata1 = include('../testdata/eksempeldata.php'); //Komplett eksempeldata lagt ved oppgåva, med name som Johan Tryti.
$testdata2 = include('../testdata/missingName.php'); //Eksempeldata der "name" manglar.
$testdata3 = include('../testdata/eksempeldata2.php'); //Komplett eksempeldata som er unik frå det fyrste eksempelet
$testdata4 = include('../testdata/MissingContactDealType.php'); //Eksempeldata der deal_type og contact_type manglar
$testdata5 = include('../testdata/eksempeldataDifferentLead.php'); //Eksempeldata med samme person som 1, men anna leadtitle
addLeadToPipedrive($testdata5);

function logMessage($infomasjon) //Funksjon for å logge. 
{
    error_log("\n" . date('Y-m-d H:i:s') . " " . $infomasjon, 3, __DIR__ . '/../logs/log.txt');
}

function addLeadToPipedrive($input_data)
{
    $leadurl = 'https://api.pipedrive.com/v1/leads';  //API for å legge til leads
    $personurl = 'https://api.pipedrive.com/v1/persons'; //API for å legge til personar
    $organizationurl = 'https://api.pipedrive.com/v1/organizations'; //API for å legge til organisasjonar
    //enkel funksjon eg bruker for all logging

    error_log("\n\n", 3, __DIR__ . '/../logs/log.txt');
    logMessage("Info: Running src\pipedrive_lead_integration.php to add lead to pipedrive.");

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
        'enebolig' => 33,
        'leilighet' => 34,
        'tomannsbolig' => 35,
        'rekkehus' => 36,
        'hytte' => 37,
        'annet' => 38
    ];

    $dealTypeMapping = [
        'alle strømavtaler er aktuelle' => 39,
        'fastpris' => 40,
        'spotpris' => 41,
        'kraftforvaltning' => 42,
        'annen avtale/vet ikke' => 43
    ];

    $contactTypeMapping = [
        'privat' => 30,
        'borettslag' => 31,
        'bedrift' => 32
    ];

    //Looper gjennom og sjekker at det alle obligatoriske felt eksisterer og har verdiar. Skriv ut ei feilmelding og stopper ved manglar. 
    foreach ($required_information as $field) {
        if (empty($input_data[$field])) {
            logMessage("Error: Data validation failed, missing field: " . $field);
            throw new Exception("Error: missing required field: " . $field);
        }
    };

    //Try-catch blokker for å konvertere housing type, deal type og contact type til ID for å kunne bruke den i pipedrive. 
    //Case-insensitive
    try {
        $housingTypeValue = $housingTypeMapping[strtolower($input_data['housing_type'])];
    } catch (Exception $e) {
        logMessage("Error: Data validation failed, invalid housing_type value." . $e->getMessage());
        throw new Exception("Error: Invalid housing_type value."); // Housing må vere Enebolig, Leilighet, Tomannsbolig, Rekkehus, Hytte eller Annet
        return false;
    }

    try {
        $dealTypeValue = $dealTypeMapping[strtolower($input_data['deal_type'])];
    } catch (Exception $e) {
        logMessage("Error: Data validation failed, invalid housing_type value." . $e->getMessage());
        throw new Exception("Error: Invalid deal_type value."); //"Deal type må vere aktuelle, Fastpris, Spotpris, Kraftforvaltning eller Annen avtale/vet ikke"
        return false;
    }
    try {
        $contactTypeValue = $contactTypeMapping[strtolower($input_data['contact_type'])];
    } catch (Exception $e) {
        logMessage("Error: Data validation failed, invalid contact_type value."  . $e->getMessage());
        throw new Exception("Error: Invalid contact_type value."); //Contact type må vere Privat, Borettslag eller Bedrift"
        return false;
    }

    #Kode for å køyre på min pipedrive-konto for å få rett samanheng mellom idane til dykkar og mine options (Må slette):
    $housingTypeValue -= 6; //Housing type
    $dealTypeValue -= 6; //Deal type
    $contactTypeValue += 8; //Contact type

    //Oppretter person, organisasjon og lead som skal bli sendt til pipedrive
    $organization = [
        "name" => "Johan Tryti sin organisasjon", //Til denne casen bruker eg berre same organisasjon overalt. 
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

    $leadresponse = getRequest($leadurl, "title", $lead['title']); //Køyrer lead-getRequest fyrst sidan eg stopper programmet om det fins duplikat
    if (!empty($leadresponse['data']['items'])) {
        $leadcount = count($leadresponse['data']['items']);
        $errorMessage = "Error: " . $leadcount . " duplicate(s) of lead already existing in pipedrive. Creation of duplicate leads not allowed.";
        logMessage($errorMessage);
        throw new Exception($errorMessage);
    }
    $organizationresponse = getRequest($organizationurl, "name", $organization["name"]); //Get request for å sjekke om det er organisasjon-duplikat
    if (!empty($organizationresponse['data']['items'])) {
        logMessage("Info: Organization already exists: " . $organization["name"]);
        $lead['organization_id']  = $organizationresponse['data']['items'][0]['item']['id']; //Henter id for å linke lead med organisasjon
        $person['org_id'] = $organizationresponse['data']['items'][0]['item']['id']; //Id for å linke person med organisasjon
        $organizationCount = count($organizationresponse['data']['items']); //Sjekker om det fins meir enn eit duplikat
        if ($organizationCount > 1) {
            logMessage("Warning: " . $organizationCount . "duplicates of organization " . $organization['name'] . "found.");
            echo ("Note: " . $organizationCount . "duplicates or organization found.");
        }
        logMessage("Info: Organization already exists, linking lead and person with existing organization.");
        echo "Organization already exists, linking lead and person with existing organization.";
    } else { //Hvis get ikkje eksisterer organisasjoner med gitt navn lager me ein ny organisasjon og henter id-en til denne for å bruke til person og lead. Gir ei åtvaring om det fins meir enn 1 duplikat. 
        $result = postRequest($organizationurl, $organization);
        logMessage("Info: Organization not found. Creating new organization: " . $organization["name"]);
        echo "<br>";
        if ($result['success']) {
            echo '<br>Organization created successfully!';
        } else {
            logMessage("Error: Failed to create organization. " . $result['error'] . " " . $result['error_info']);
            throw new Exception('<br><br>Failed to create Organization: ' . $result['error'] . " " . $result['error_info']);
            return false;
        }
        $lead['organization_id'] = $result['data']['id'];
        $person['org_id'] = $result['data']['id'];
    }


    $personresponse = getRequest($personurl, "name", $input_data['name']); //Get request for å sjekke om det er person-duplikat. 
    if (!empty($personresponse['data']['items'])) {
        logMessage("Info: Person already exists, linking lead to existing person.");
        $lead['person_id']  = $personresponse['data']['items'][0]['item']['id'];
        echo "Person already existing, linking lead to existing person.";
    } else { //Om person fins linkar eg lead med den eksisterande personen. Gir ei åtvaring om det fins meir enn 1 duplikat. 
        $result = postRequest($personurl, $person);
        if ($result['success']) {
            logMessage("Info: Person created successfully with ID: " . $result['data']['id']);
            echo '<br>Person created successfully!';
        } else {
            logMessage("Error: Failed to create person. " . $result['error'] . " " . $result['error_info']);
            throw new Exception('<br><br>Failed to create person: ' . $result['error'] . " " . $result['error_info']);
        }
        $lead['person_id'] = $result['data']['id'];
    }

    $result = postRequest($leadurl, $lead);

    if ($result['success']) {
        logMessage("Info: Lead created successfully with ID: " . $result['data']['id']);
        echo '<br>Lead created successfully!';
    } else {
        logMessage("Error: Failed to create lead. " . $result['error'] . " " . $result['error_info']);
        throw new Exception('Failed to create lead: ' . $result['error'] . " " . $result['error_info']);
    }

    logMessage("Info: Script successfully executed!");
}
