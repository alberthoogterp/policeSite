<?php
require_once __DIR__."/databaseFunctions.php";

$firstname = null;
$lastname = null;
$adress = null;
$height = null;
$haircolour = null;
$eyecolour = null;
$dexterity = null;
$dateofbirth = null;
$gender = null;

if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST'){
    $results = Query([[
        "sql"=>"INSERT INTO personsofinterest (firstname, lastname, adress, height, haircolour, eyecolour, dexterity, dateofbirth, gender) VALUES (?,?,?,?,?,?,?,?,?)",
        "values"=>[$firstname, $lastname , $adress, $height,$haircolour,$eyecolour,$dexterity,$dateofbirth,$gender]]
    ]);
    echo "gelukt";
}
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="POST">
        <input type="text" name="username" value="">
        <input type="submit" value="button">
    </form>
</body>
</html>