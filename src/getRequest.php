<?php
//Funksjon for å sende ein post request til pipedrive med ei gitt adresse og gitt data.
//Inspirasjon frå: https://endgrate.com/blog/using-the-pipedrive-api-to-create-or-update-leads-in-php

function getRequest($address, $field, $searchTerm) {
    //Lager korrekt adresse for å gjere GET-requesten
    $api_key = $_ENV['Pipedrive_API_TOKEN']; //Henter API-nøklen
    $address = $address . '/search?api_token=' . $api_key; //Legger in APIen i get-requesten
    $encodedTerm = urlencode($searchTerm); // Encoder search-term for å kunne bruke spesial-karakterar i søket
    $searchUrl = $address . '&term=' . $encodedTerm . '&fields=' . $field; //Lager fullstendig søk-adresse

    $ch = curl_init($searchUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Hindre at curl skriv ut outputten
    $response = curl_exec($ch); // Execute the request
    $result = json_decode($response, true);
    curl_close($ch);
    return $result;
}