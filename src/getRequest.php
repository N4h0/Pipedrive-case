<?php
//Funksjon for å sende ein post request til pipedrive med ei gitt adresse og gitt data.
//Inspirasjon frå: https://endgrate.com/blog/using-the-pipedrive-api-to-create-or-update-leads-in-php

function getRequest($address, $field, $searchTerm) {
    $api_key = $_ENV['Pipedrive_API_TOKEN']; //Henter API-nøklen
    $address = $address . '/search?api_token=' . $api_key;
    $encodedTerm = urlencode($searchTerm); // Safely encode the search term
    $searchUrl = $address . '&term=' . $encodedTerm . '&fields=' . $field;
    $ch = curl_init($searchUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Prevent output to stdout
    $response = curl_exec($ch); // Execute the request
    $result = json_decode($response, true);
    curl_close($ch);
    return $result;
}