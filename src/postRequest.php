<?php
//Funksjon for å sende ein post request til pipedrive med ei gitt adresse og gitt data.
//Inspirasjon frå: https://endgrate.com/blog/using-the-pipedrive-api-to-create-or-update-leads-in-php

function postRequest($adress, $data){
    $api_key = $_ENV['Pipedrive_API_TOKEN']; //Henter API-nøklen
    $addressWIthAPI = $adress . '?api_token=' . $api_key;
    logMessage("Info: Sending POST request to " . $adress);

    $ch = curl_init($addressWIthAPI);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch); // Execute the request

    if ($response === false) {
        logMessage("Error: POST request failed: " . curl_error($ch));
    } else {
        logMessage("Info: POST request completed successfully.");
    }

    $result = json_decode($response, true);


    curl_close($ch);
    return $result;
}