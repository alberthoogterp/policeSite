<?php
require_once __DIR__."/checklogin.php";
require_once __DIR__."/databaseFunctions.php";
require_once __DIR__."/navbar.php";
checkLogin();
unset($_SESSION["caseid"]);

$cases = Query([[
    "sql"=>"SELECT * FROM cases",
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
    <title>Cases</title>
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
            <a href="addCase.php" class="w3-button w3-border">Add Case</a>
        </p>
        <?php
    }
    ?>
    <p>
    <?php
        if($cases){
            foreach($cases as $row){
                ?>
                <p>
                <form action="caseEditor.php" method="POST">
                    <label><?php echo $row["id"]?></label>
                    <label><?php echo $row["name"]?></label>
                    <input hidden name="caseid" value="<?php echo $row["id"] ?>">
                    <input class="w3-button w3-border" type="submit" name="view" value="View">
                </form>
                </p>
                <?php
            }
        }
        else{
            echo "no cases available";
        }    
        ?>
    </p>
    </div>
</body>
</html>