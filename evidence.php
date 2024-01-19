<?php
require_once __DIR__."/checklogin.php";
require_once __DIR__."/databaseFunctions.php";
require_once __DIR__."/navbar.php";
checkLogin();

$evidence = Query([[
    "sql"=>"SELECT evidence.*, pictures.filedata FROM evidence JOIN evidence_pictures ON evidence_pictures.evidence_id = evidence.id JOIN pictures ON evidence_pictures.picture_id = pictures.id",
    "values"=>[]
]])[0]["result"]->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <title>Evidence</title>
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
            <a href="evidenceEditor.php" class="w3-button w3-border">Add evidence</a>
        </p>
        <?php
    }
    ?>
    <p>
    <?php
        if($evidence){
            foreach($evidence as $row){
                ?>
                    <p>
                    <form action="evidenceEditor.php" method="POST">
                        <label><?php echo$row["id"]?></label>
                        <input hidden name="evidenceid" value="<?php echo $row["id"] ?>">
                        <img id="output" src=<?php echo "data:image/jpeg;base64,".base64_encode($row["filedata"])?> height="50px"/>
                        <input class="w3-button w3-border" type="submit" name="view" value="View">
                    </form>
                    </p>
                    <?php
            }
        }
        else{
            echo "no evidence available";
        }    
        ?>
    </p>
</div>
</body>
</html>