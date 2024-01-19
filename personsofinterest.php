<?php
require_once __DIR__."/checklogin.php";
require_once __DIR__."/navbar.php";
require_once __DIR__."/databaseFunctions.php";
checkLogin();

$persons = Query([[
    "sql"=>"SELECT * FROM personsofinterest",
    "values"=>[]
]])[0]["result"] ->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <title>persons of interest</title>
</head>
<body>
    <?php
        addNavbar();
    ?>
    <div class="w3-container">
    <?php
    if($_SESSION["userrank"] != "announcer" || $_SESSION["userrank"] != "other" || $_SESSION["userrank"] != "police trainee" || $_SESSION["userrank"]!="police patrol officer"){
        ?>
        <p>
            <a href="poiEditor.php" class="w3-button w3-border">Add POI</a>
        </p>
        <?php
    }
    if($persons){
        foreach($persons as $row){
            ?>
            <p>
            <form action="poiEditor.php" method="POST">
                <label><?php echo$row["id"]." : ".$row["firstname"]." ".$row["lastname"]?></label>
                <input hidden name="personid" value="<?php echo $row["id"] ?>">
                <input class="w3-button w3-border" type="submit" name="view" value="View">
            </form>
            </p>
            <?php
        }
    }
    else{
        echo "No persons of interest found";
    }
    ?>
    </div>
</body>
</html>