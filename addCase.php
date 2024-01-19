<?php
require_once __DIR__."/checklogin.php";
require_once __DIR__."/navbar.php";
require_once __DIR__."/databaseFunctions.php";
checkLogin();

if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST'){
    if(isset($_POST["add"])){
        $caseName = $_POST["casename"];
        $description = $_POST["description"];
        $date = $_POST["date"];
        $state = "ongoing";
        $result = Query([[
            "sql"=>"INSERT INTO cases (name, description, opendate) VALUES (?,?,?)",
            "values"=>[$caseName, $description, $date]
        ]])[0];
        Query([[
            "sql"=>"INSERT INTO casestate (case_id, state, date) VALUES (?,?,?)",
            "values"=>[$result["insertid"], $state, $date]  
        ]]);
        header('location: http://localhost/hacklab/policeSite/cases.php', true, 303);  
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <title>Add case</title>
</head>
<body>
    <?php
    addNavbar();
    ?>
    <div class="w3-display-container">
        <div class="w3-display-topleft">
            <form class="w3-container" action="" method="POST">
                <label for="casename">Case name</label>
                <input class="w3-input w3-border" type="text" id="casename" name="casename" placeholder="Case name" autocomplete="off" required>
                <label for="description">Description</label>
                <textarea class="w3-input w3-border" style="resize:none" id="description" name="description" placeholder="Description" cols="10" rows="6" autocomplete="off" required ></textarea>
                <label for="date">Case opening-date</label>
                <input class="w3-input w3-border" type="datetime-local" id="date" name="date" value="<?php echo $date?>" max="<?php echo date('Y-m-d')?>" required> 
                <p>
                    <input class="w3-button w3-border" type="submit" name="add" value="add">
                    <a class="w3-button w3-border" href="cases.php">Cancel</a> 
                </p>
            </form>
        </div>
    </div>
</body>
</html>