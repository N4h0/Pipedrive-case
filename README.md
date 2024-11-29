# Pipedrive-case

Dette er eit script som laster opp leads til pipedrive via API med og koblar desse opp mot personar og leads. I tillegg til standard felt har lead følgande felt obligatorisk: housing_type, property_size, comment og deal_type, og person krever feltet contact_type. Scriptet koblar leads opp mot eksisterande personar og organisasjonar om desse eksisterer, og opprettar nye organisasjonar og brukarar om dei ikkje blir funne. 

Installer PHP. Last ned CURL om den ikkje er lasta ned og aktiver den i php ved å endre php.ini-fila ved å fjerne ";" frå linja "extension=curl
". Last ned curl-CA-sertifikatet og legg ein gyldig path til det i php.ini ved å endre linja ";curl.cainfo =" til "curl.cainfo = "pathToCertificate."
Composer må vere installert.


Remove-Item -Recurse -Force .\vendor

composer install


Finn iden til custom fields som er lagt til. Dette kan du feks gjere ved bruk av PostMan, ved å følge denne tutorialen: https://pipedrive.readme.io/docs/run-pipedrive-api-in-postman-or-insomnia. Deretter kan du køyre {{baseUrl}}/leadFields for å finne iden til dei ulike vala til informajsonen, sidan iden ikkje kjem opp utan vidare.

Antek her at ideen er talet de har sett opp i parantes for dei ulike vala. 

guzzlehttp/guzzle
curl

last ned og pass på at du har nyaste oppdaterte Mozilla CA certificate. Definer kvar det ligg ved å finne linja "curl.cainfo" i php.init i din nedlasta versjon av curl, og 
fjern ";" og endren den til "curl.cainfo ="pathtoyourMozilla_CA_certificate"".

https://github.com/vlucas/phpdotenv


Vanlige feilmeldinger: 
Warning: require(PATH\integration_project\vendor\composer/../symfony/polyfill-ctype/bootstrap.php): Failed to open stream: No such file or directory in PATH\integration_project\vendor\composer\autoload_real.php on line 41

og 

Undefined type 'Dotenv\Dotenv'.

Desse kan bli løyst ved å reinstallere .\Vendor. 
På windows: Køyr 
Remove-Item -Recurse -Force .\vendor
så
composer install
For å fullstendig slette og reinstallere Vendor, som burde fikse feilen. 