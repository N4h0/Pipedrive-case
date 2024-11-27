<?php

$data = [
    "title" => "lead found in a back alley",
    "bfd7de6d20fd153c450e6a9fa9e687846a9fcb84" => 27, // housing_type (enebolig)
    "3803e2afd28dcf016b749abefce02952f63afb76" => 123, // property size
    "9ae06060d6cc0e797c5a5235e58e4d41afeace0b" => "Scary lead, maybe don't follow up?", // Comment
    "ad0c0c5d6d5b85d6ce81777139e55f5155d4bd2e" => 37, // deal_type
    "person_id" => 1
];

function myMessage($input)
{
    echo "Lead: <br>";
    echo "Housing_type: " . $input['housing_type'] . "<br>";
    echo "Property_size: " . $input['property_size'] . "<br>";
    echo "Comment: "  . ($input['comment'] ?? '') . "<br>";
    echo "Deal_type: " . $input['deal_type'] . "<br>";
    echo "<br>";
    echo "Person:<br>";
    echo "Contact_type: "  . $input['contact_type'] . "<br>";
    echo "Name: " . $input['name'] . "<br>";
    echo "Phone: " . $input['phone'] . "<br>";
    echo "Email: " . $input['email'] . "<br>" . "<br>";


    echo $input['name'] . "<br>";
    echo "<br><br>" . json_encode($input); //Gjer arrayet om til eit jsonobjekt for å kunne skrive ut på nettsida
}

myMessage($exampledata);
?>