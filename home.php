<?php
require_once __DIR__."/checklogin.php";
require_once __DIR__."/databaseFunctions.php";
require_once __DIR__."/navbar.php";
checkLogin();

if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST'){
    if(isset($_POST["add"])){
        $important = isset($_POST["important"]) ? true: false;
        $announcement = $_POST["text"];
        Query([[
            "sql"=>"INSERT INTO announcements (announcement, important) values (?,?)",
            "values"=>[$announcement, $important]]
        ]);
    }
    else if(isset($_POST["remove"])){
        $id = $_POST["id"];
        Query([[
            "sql"=>"DELETE FROM announcements WHERE id = (?)",
            "values"=>[$id]
        ]]);
    }
}
$announcements = Query([[
    "sql"=>"SELECT * FROM announcements",
    "values"=>[]
]])[0]["result"] ->fetch_all(MYSQLI_ASSOC);

$cases = Query([[
    "sql"=>"SELECT cases.* FROM `cases` JOIN officers_cases ON officers_cases.case_id = cases.id WHERE officers_cases.officer_id = (?) ORDER BY cases.id ASC",
    "values"=>[$_SESSION["userid"]]
]])[0]["result"]->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <title>Police Homepage</title>
</head>
<body>
    <?php
    addNavbar();
    if($_SESSION["userrank"] === "announcer"){
        ?>
        <form class="w3-container" action="" method="POST">
            <p>
                <input class="w3-check" type="checkbox" name="important"> 
                <label>Important</label> 
            </p>
            <input class="w3-input" name="text" placeholder="announcement text" required autocomplete="off">
            <input class="w3-btn w3-border" name="add" type="submit" value="create">
        </form>
        <?php
    }
    ?>
    <div class="w3-container">
    <p>
        <label>Announcements:</label>
    </p>
    <?php
    if($announcements){
        foreach($announcements as $row){
            $id = $row["id"];
            $important = $row["important"];
            $announcement = $row["announcement"];
            $date = $row["date"];
            if($_SESSION["userrank"] === "announcer"){
                ?>
                <form action="" method="POST">
                <?php
            }
            if($important){
                ?>
                <div class="w3-container w3-border w3-red">
                    <div class="w3-left-align"><p>!IMPORTANT!</p></div>
                <?php
            }
            else{
                ?>
                <div class="w3-container w3-border">
                <?php
            }
            ?>
                <div class="w3-left-align"><?php echo $date." >".$announcement;?></div>
            <?php
            if($_SESSION["userrank"] === "announcer"){
                ?>
                    <input hidden name="id" value="<?php echo $id?>">
                    <input class="w3-btn w3-border" name="remove" type="submit" value="remove">
                </form>
                <?php
            }
            ?>
            </div>
            <?php
        }
    }
    else{
        echo "No announcements";
    }
    ?>
    <p>
        <label>Your cases:</label>
    </p>
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
        echo "No cases";
    }
    ?>
    </div>
</body>
</html>