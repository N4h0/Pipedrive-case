# Pipedrive-case

Installer PHP. Last ned CURL om den ikkje er lasta ned og aktiver den i php ved å endre php.ini-fila ved å fjerne ";" frå linja "extension=curl
". Last ned curl-CA-sertifikatet og legg ein gyldig path til det i php.ini ved å endre linja ";curl.cainfo =" til "curl.cainfo = "pathToCertificate."
Composer må vere installert. 
Finn iden til custom fields som er lagt til. Dette kan du feks gjere ved bruk av PostMan, ved å følge denne tutorialen: https://pipedrive.readme.io/docs/run-pipedrive-api-in-postman-or-insomnia. Deretter kan du køyre {{baseUrl}}/leadFields for å finne iden til dei ulike vala til informajsonen, sidan iden ikkje kjem opp utan vidare.

Antek her at ideen er talet de har sett opp i parantes for dei ulike vala. 

guzzlehttp/guzzle
curl

last ned og pass på at du har nyaste oppdaterte Mozilla CA certificate. Definer kvar det ligg ved å finne linja "curl.cainfo" i php.init i din nedlasta versjon av curl, og 
fjern ";" og endren den til "curl.cainfo ="pathtoyourMozilla_CA_certificate"".

https://github.com/vlucas/phpdotenv