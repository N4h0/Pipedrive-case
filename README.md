# Oppsummering

Dette er eit script som laster opp leads til pipedrive og koblar desse opp mot personar og organisasjonar, eller laster personar og organisasjonar opp om dei ikkje eksisterer

# Features

- Legg til leads i pipedrive og link desse opp mot nye eller eksisterande personar og leads.
- Duplikat av leads i pipedrive er ikkje lovlig.
- Ved duplikat av person eller organisasjon som allereie fins i pipedrive blir den eksisterande personen og organisasjonen linka til.
- Logging.

# Dependencies

- PHP
- Curl
- Composer
- Vendor
- Openssl
- I tillegg må [Mozilla CA Certificate](https://curl.se/docs/caextract.html) til curl vere lasta ned og korrekt linka til i php.ini.
.


# Køyre scriptet
1. **Køyr `composer install`** for å laste ned nødvendige pakker (Vendor):
   ```bash
   composer install
2. Legg til ei .env-fil og legg til API-nøkkelen til pipedrive med formatet "Pipedrive_API_TOKEN="DINAPINØKKELHER""
3. Deretter er det berre å køyre scriptet i pipedrive_lead_integration. For å endre kva data som blir sendt kan du endre testdata i linje 17.

# Testdata

Det er lagt ved 5 ulike testdata. Kva testdata som blir køyrt kan bli endra ved å endre linje 17: addLeadToPipedrive($testdata); Beskrivelse av dei ulike testdataene er lagt ved i linjene over. 

# Filstruktur

### /logs
- Ei tom mappe der log blir oppretta når scriptet køyrer.
### /scripts
- postRequest.php: Køyrer POST-requests opp mot serveren.
- getRequest.php: Køyrer GET-requests opp mot serveren.
- pipedrive_lead_integration.php: Hovudscript.
### testdata
- Inneheld testdata i .php-format.

# Logging

Alle GET og POST requests blir logga. Ingen sensitiv data eller API-nøklar blir logga. All logging er i formatet: "*dato*, *label:* *Informasjon*. Eksempel på fullstendig logg ved duplikat-lead:

2024-11-29 10:16:20 Info: Running src\pipedrive_lead_integration.php to add lead to pipedrive. <br />
2024-11-29 10:16:20 Info: Sending GET request to: https://api.pipedrive.com/v1/leads/search?api_token=THEAPETOKENINSERTERHERE&term=SEARCHTERMGOESHERE&fields=title <br />
2024-11-29 10:16:21 Info: GET request completed successfully. <br />
2024-11-29 10:16:21 Error: 1 duplicate(s) of lead already existing in pipedrive. Creation of duplicate leads not allowed.

# Køyre scriptet på eigen pipedrive-konto

Scriptet er hardkoda for å laste opp til ein spesifikk pipedrive-konto og api og id til dei ulike felta er hardkoda. For å kunne laste opp til din eigen konto må du legge til felta under i din pipedrive. I tillegg må du finne APIen til dei ulike felta og id til dei ulike valga (id til ulike val er ein int). Du må så endre dei hardkoda verdiane i pipedrive_lead_integration.php.  API og idear kan du feks finne ved å bruke postman og køyre GET {{baseUrl}}/leadFields, {{baseUrl}}/personFields og {{baseUrl}}/organizationFields, sjekk [Pipedrive API Documentation](https://pipedrive.readme.io/docs/run-pipedrive-api-in-postman-or-insomnia) for meir info. 

### Lead Fields

| Field Name     | Type           | Options                                     |
|-----------------|----------------|---------------------------------------------|
| Housing_type    | Single-Option | Enebolig                                    |
|                 |                | Leilighet                                   |
|                 |                | Tomannsbolig                                |
|                 |                | Rekkehus                                    |
|                 |                | Hytte                                       |
|                 |                | Annet                                       |
| property_size   | Integer (int) | -                                           |
| comment         | Text          | -                                           |
| deal_type       | Single-Option | aktuelle                                    |
|                 |                | Fastpris                                    |
|                 |                | Spotpris                                    |
|                 |                | Kraftforvaltning                            |
|                 |                | Annen avtale/vet ikke                      |

### Person Fields

| Field Name      | Type           | Options        |
|------------------|----------------|----------------|
| contact_type     | Single-Option | Privat         |
|                  |                | Borettslag     |
|                  |                | Bedrift        |