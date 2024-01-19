<?php
require_once __DIR__."/checklogin.php";
require_once __DIR__."/navbar.php";
require_once __DIR__."/databaseFunctions.php";
checkLogin();
$EmptyFields = "";

$firstname = "unknown";
$lastname = "unknown";
$adress = "";
$height = "";
$haircolour = "";
$eyecolour = "";
$dexterity = "";
$dateofbirth = "";
$gender = "";
$imageData = "";

if($_SESSION["userrank"] == "announcer" || $_SESSION["userrank"] == "other" || $_SESSION["userrank"] == "police trainee" || $_SESSION["userrank"] == "police patrol officer"){
    header('location: http://localhost/hacklab/policeSite/personsofinterest.php', true, 303);  
    exit();
}
if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST'){
    if(isset($_POST["delete"])){
        Query([[
                "sql"=>"DELETE FROM pictures WHERE id = (SELECT picture_id FROM personsofinterest_pictures WHERE personofinterest_id = (?))",
                "values"=>[$_POST["personid"]]
        ],[
            "sql"=>"DELETE FROM personsofinterest WHERE id = (?)",
            "values"=>[$_POST["personid"]]]
        ]);
        header('location: http://localhost/hacklab/policeSite/personsofinterest.php', true, 303); 
        exit();
    }
    else if((isset($_POST["view"]) || isset($_POST["edit"])) && isset($_POST["personid"])){
        $person = Query([[
            "sql"=>"SELECT * FROM personsofinterest WHERE personsofinterest.id = (?)",
            "values"=>[$_POST["personid"]]
        ]])[0]["result"]->fetch_all(MYSQLI_ASSOC);
        $firstname = getValue($person, "firstname");
        $lastname = getValue($person, "lastname");
        $adress = getValue($person, "adress");
        $height = getValue($person, "height");
        $haircolour = getValue($person, "haircolour");
        $eyecolour = getValue($person, "eyecolour");
        $dexterity = getValue($person, "dexterity");
        $dateofbirth = getValue($person, "dateofbirth");
        $gender = getValue($person, "gender");
        $img = Query([[
            "sql"=>"SELECT filedata FROM pictures JOIN personsofinterest_pictures ON pictures.id = personsofinterest_pictures.picture_id WHERE personsofinterest_pictures.personofinterest_id = (?)",
            "values"=>[$_POST["personid"]]
        ]])[0]["result"]->fetch_all(MYSQLI_ASSOC);
        $imageData = base64_encode(getValue($img, "filedata"));
    }
    elseif(isset($_POST["add"])){
        $fields = ["firstname", "lastname", "adress", "height", "haircolour", "eyecolour", "dexterity", "dateofbirth", "gender"];
        $allEmpty = false;
        $emptyCounter = 0;
        foreach($fields as $field){
            if($_POST[$field] == ""){
                $emptyCounter +=1;
            }
        }
        if($emptyCounter == count($fields)){
            $EmptyFields = "You must fill in atleast one field";
        }
        else{
            if(($_FILES["image"]["error"] == 0)){
                $imageName = $_FILES["image"]["name"];
                $imageType = $_FILES["image"]["type"];
                $imageData = file_get_contents($_FILES["image"]["tmp_name"]);
                $results = Query([[
                    "sql"=>"INSERT INTO personsofinterest (firstname, lastname, adress, height, haircolour, eyecolour, dexterity, dateofbirth, gender) VALUES (?,?,?,?,?,?,?,?,?)",
                    "values"=>[$_POST["firstname"], $_POST["lastname"], $_POST["adress"], $_POST["height"],$_POST["haircolour"],$_POST["eyecolour"],$_POST["dexterity"],$_POST["dateofbirth"],$_POST["gender"]]],
                [
                    "sql"=>"INSERT INTO pictures (filename, filetype, filedata) VALUES (?,?,?)",
                    "values"=>[$imageName,$imageType,$imageData]]
                ]);
                $personid = $results[0]["insertid"];
                $pictureid = $results[1]["insertid"];
                Query([[
                    "sql"=>"INSERT INTO personsofinterest_pictures (personofinterest_id, picture_id) VALUES (?,?)",
                    "values"=>[$personid, $pictureid]
                ]]);
                header('location: http://localhost/hacklab/policeSite/personsofinterest.php', true, 303); 
                exit();
            }
            else{
                Query([[
                    "sql"=>"INSERT INTO personsofinterest (firstname, lastname, adress, height, haircolour, eyecolour, dexterity, dateofbirth, gender) VALUES (?,?,?,?,?,?,?,?,?)",
                    "values"=>[$_POST["firstname"], $_POST["lastname"], $_POST["adress"], $_POST["height"],$_POST["haircolour"],$_POST["eyecolour"],$_POST["dexterity"],$_POST["dateofbirth"],$_POST["gender"]]]
                ]);
                header('location: http://localhost/hacklab/policeSite/personsofinterest.php', true, 303); 
                exit();
            }
        }
    }
    elseif(isset($_POST["update"])){
        if(($_FILES["image"]["error"] == 0)){
            $imageName = $_FILES["image"]["name"];
            $imageType = $_FILES["image"]["type"];
            $imageData = file_get_contents($_FILES["image"]["tmp_name"]);
            $result = Query([[
                "sql"=>"UPDATE personsofinterest SET firstname = (?), lastname = (?), adress = (?), height = (?), haircolour = (?), eyecolour = (?), dexterity = (?), dateofbirth = (?), gender = (?) where id = (?)",
                "values"=>[$_POST["firstname"], $_POST["lastname"], $_POST["adress"], $_POST["height"], $_POST["haircolour"], $_POST["eyecolour"], $_POST["dexterity"], $_POST["dateofbirth"], $_POST["gender"], $_POST["personid"]]
            ],[
                "sql"=>"UPDATE pictures SET filename = (?), filetype = (?), filedata = (?) WHERE id = (SELECT picture_id FROM personsofinterest_pictures WHERE personofinterest_id = (?))",
                "values"=>[$imageName, $imageType, $imageData, $_POST["personid"]]]
            ]);
        }
        else{
            Query([[
                "sql"=>"UPDATE personsofinterest SET firstname = (?), lastname = (?), adress = (?), height = (?), haircolour = (?), eyecolour = (?), dexterity = (?), dateofbirth = (?), gender = (?) where id = (?)",
                "values"=>[$_POST["firstname"], $_POST["lastname"], $_POST["adress"], $_POST["height"], $_POST["haircolour"], $_POST["eyecolour"], $_POST["dexterity"], $_POST["dateofbirth"], $_POST["gender"], $_POST["personid"]]
            ]]);
        }
        header('location: http://localhost/hacklab/policeSite/personsofinterest.php', true, 303); 
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <title>personsofinterest</title>
</head>
<body>
    <?php
        addNavbar();
    ?>
     <p>
        <div class="w3-display-middle">
        <div class="w3-container w3-border w3-red"><?php echo $EmptyFields ?></div>
            <form class="w3-container" action="" method="POST" enctype="multipart/form-data">
            <div class="w3-half">
                <label for="firstname">Firstname</label>
                <input class="w3-input w3-border" type="text" name="firstname" id="firstname" placeholder="Unknown" autocomplete="off" value="<?php echo $firstname?>" <?php if(isset($_POST["view"])){echo "disabled";} ?>>
                <label for="lastname">Lastname</label>
                <input class="w3-input w3-border" type="text" name="lastname" id="lastname" placeholder="Unknown" autocomplete="off" value="<?php echo $lastname?>" <?php if(isset($_POST["view"])){echo "disabled";} ?>>
                <label for="adress">adress</label>
                <input class="w3-input w3-border" type="text" name="adress" id="adress" placeholder="Unknown" autocomplete="off" value="<?php echo $adress?>" <?php if(isset($_POST["view"])){echo "disabled";} ?>>
                <label for="height">Height</Height>
                <input class="w3-input w3-border" type="text" name="height" id="height" placeholder="Unknown" autocomplete="off" pattern="^[0-9]{1}\.{1}[0-9]{1,2}$" oninvalid="this.setCustomValidity('Requires two dot separated numbers')" oninput="setCustomValidity('')" value="<?php echo $height?>" <?php if(isset($_POST["view"])){echo "disabled";} ?>>
                <label for="haircolour">haircolour</label>
                <input class="w3-input w3-border" type="text" name="haircolour" id="haircolour" placeholder="Unknown" autocomplete="off" value="<?php echo $haircolour?>" <?php if(isset($_POST["view"])){echo "disabled";} ?>>
                <label for="eyecolour">Eyecolour</label>
                <input class="w3-input w3-border" type="text" name="eyecolour" id="eyecolour" placeholder="Unknown" autocomplete="off" value="<?php echo $eyecolour?>" <?php if(isset($_POST["view"])){echo "disabled";} ?>>
                <label for="dexterity">Dexterity</label>
                <select class="w3-select w3-border" id="dexterity" name="dexterity" <?php if(isset($_POST["view"])){echo "disabled";} ?>>
                    <option <?php if($dexterity == ""){echo "selected";}?> value="">Unknown</option>
                    <option <?php if($dexterity == "lefthanded"){echo "selected";}?> value="lefthanded">Lefthanded</option>
                    <option <?php if($dexterity == "righthanded"){echo "selected";}?> value="righthanded">Righthanded</option>
                    <option <?php if($dexterity == "ambidextrous"){echo "selected";}?> value="ambidextrous">Ambidextrous</option>
                </select>
                <label for="dateofbirth">Date of birth</label>
                <input class="w3-input w3-border" type="date" name="dateofbirth" id="dateofbirth" value="<?php echo $dateofbirth?>" <?php if(isset($_POST["view"])){echo "disabled";} ?>>
                <label for="gender">Gender</label>
                <select class="w3-select w3-border" id="gender" name="gender" <?php if(isset($_POST["view"])){echo "disabled";} ?>>
                    <option <?php if($gender == ""){echo "selected";} ?> value="">unknown</option>
                    <option <?php if($gender == "man"){echo "selected";}?> value="man">Man</option>
                    <option <?php if($gender == "woman"){echo "selected";}?> value="woman">Woman</option>
                    <option <?php if($gender == "other"){echo "selected";}?> value="other">Other</option>
                </select>
                <p>
                    <input hidden name="personid" value="<?php if(isset($_POST["personid"])){echo $_POST["personid"];}?>">
                    <input class="w3-btn w3-border" type="submit" name="<?php if(isset($_POST["view"])){echo "edit";}elseif(isset($_POST["edit"])){echo "update";}else{echo "add";} ?>" value="<?php if(isset($_POST["view"])){echo "Edit";}elseif(isset($_POST["edit"])){echo "Update";}else{echo "Add";} ?>">
                    <?php if(isset($_POST["edit"])){
                        ?>
                    <input class="w3-button w3-border" type="submit" name="delete" value="Delete">
                    <?php
                    }
                    ?>
                    <a href="personsofinterest.php" class="w3-btn w3-border">Back</a>
                </p>
            </div>   
            <div class="w3-half">
                <label for="image">Add image</label>
                <img id="output" src=<?php if($imageData == ""){echo "emptyprofile.png";} else {echo "data:image/jpeg;base64,".$imageData;}?> width="400px"/>
                <input class="w3-right" type="file" id="image" name="image" accept="image/*" onchange="loadFile(event)" <?php if(isset($_POST["view"])){echo "disabled";} ?>>
            </div>
            </form>
        </div>
    </p>
</body>
</html>

<script>
  var loadFile = function(event) {
    var output = document.getElementById('output');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
      URL.revokeObjectURL(output.src) // free memory
    }
  };
</script>