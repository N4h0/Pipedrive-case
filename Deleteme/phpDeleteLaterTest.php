<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    $exampledata = include('../exampledata.php'); //Henter eksempeldataen.

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

</body>

</html>