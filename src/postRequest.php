<?php
//Funksjon for å sende ein post request til pipedrive med ei gitt adresse og gitt data.
//Inspirasjon frå: https://endgrate.com/blog/using-the-pipedrive-api-to-create-or-update-leads-in-php

function postRequest($adress, $data){
    $api_key = $_ENV['Pipedrive_API_TOKEN']; //Henter API-nøklen
    $adress = $adress . '?api_token=' . $api_key;
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