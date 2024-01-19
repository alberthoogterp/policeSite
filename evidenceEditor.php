<?php
require_once __DIR__."/checklogin.php";
require_once __DIR__."/navbar.php";
require_once __DIR__."/databaseFunctions.php";
checkLogin();
$description = "";
$finddate = "";
$imageData = "";

if($_SESSION["userrank"] == "announcer" || $_SESSION["userrank"] == "other" || $_SESSION["userrank"] == "police trainee" || $_SESSION["userrank"] == "police patrol officer"){
    header('location: http://localhost/hacklab/policeSite/evidence.php', true, 303);  
    exit();
}
if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST'){
    if(isset($_POST["add"])){
        $description = $_POST["description"];
        $finddate = $_POST["finddate"];
        $imageName = $_FILES["image"]["name"];
        $imageType = $_FILES["image"]["type"];
        $imageData =  file_get_contents($_FILES["image"]["tmp_name"]);
        $result = Query([[
            "sql"=>"INSERT INTO evidence (description, finddate) VALUES (?,?)",
            "values"=>[$description, $finddate]],
        [
            "sql"=>"INSERT INTO pictures (filename, filetype, filedata) VALUES (?,?,?)",
            "values"=>[$imageName, $imageType, $imageData]]
        ]);
        $evidenceid = $result[0]["insertid"];
        $pictureid = $result[1]["insertid"];
        Query([[
            "sql"=>"INSERT INTO evidence_pictures (evidence_id, picture_id) VALUES (?,?)",
            "values"=>[$evidenceid, $pictureid]
        ]]);
        header('location: http://localhost/hacklab/policeSite/evidence.php', true, 303);  
        exit();
    }
    else if(isset($_POST["view"]) || isset($_POST["edit"])){
        $result = Query([[
            "sql"=>"SELECT * FROM evidence WHERE id = (?)",
            "values"=>[$_POST["evidenceid"]]
        ]])[0]["result"]->fetch_all(MYSQLI_ASSOC);
        $description = getValue($result, "description");
        $finddate = getValue($result, "finddate");
        $img = Query([[
            "sql"=>"SELECT filedata FROM pictures JOIN evidence_pictures ON pictures.id = evidence_pictures.picture_id WHERE evidence_pictures.evidence_id = (?)",
            "values"=>[$_POST["evidenceid"]]
        ]])[0]["result"]->fetch_all(MYSQLI_ASSOC);
        $imageData = base64_encode(getValue($img, "filedata"));
    }
    else if(isset($_POST["delete"])){
        Query([[
            "sql"=>"DELETE FROM pictures WHERE id = (SELECT picture_id FROM evidence_pictures WHERE evidence_id = (?))",
            "values"=>[$_POST["evidenceid"]],
        [
            "sql"=>"DELETE FROM evidence WHERE id = (?)",
            "values"=>[$_POST["evidenceid"]]
        ]]]);
        header('location: http://localhost/hacklab/policeSite/evidence.php', true, 303); 
        exit();
    }
    else if(isset($_POST["update"])){
        if(($_FILES["image"]["error"] == 0)){
            $imageName = $_FILES["image"]["name"];
            $imageType = $_FILES["image"]["type"];
            $imageData = file_get_contents($_FILES["image"]["tmp_name"]);
            $result = Query([[
                "sql"=>"UPDATE evidence SET description = (?), finddate = (?) where id = (?)",
                "values"=>[$_POST["description"], $_POST["finddate"], $_POST["evidenceid"]]
            ],[
                "sql"=>"UPDATE pictures SET filename = (?), filetype = (?), filedata = (?) WHERE id = (SELECT picture_id FROM evidence_pictures WHERE evidence_id = (?))",
                "values"=>[$imageName, $imageType, $imageData, $_POST["evidenceid"]]]
            ]);
        }
        else{
            Query([[
                "sql"=>"UPDATE evidence SET description = (?), finddate = (?) where id = (?)",
                "values"=>[$_POST["description"], $_POST["finddate"], $_POST["evidenceid"]]
            ]]);
        }
        header('location: http://localhost/hacklab/policeSite/evidence.php', true, 303); 
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
    <title>Evidence editor</title>
</head>
<body>
    <?php
    addNavbar();
    ?>
    <p>
        <div class="w3-display-middle">
            <form class="w3-container" action="" method="POST" enctype="multipart/form-data">
                <div class="w3-half">
                    <label for="description">Description</label>
                    <textarea class="w3-input w3-border" style="resize:none" id="description" name="description" placeholder="Description" cols="10" rows="6" value="<?php echo $description?>" required <?php if(isset($_POST["view"])){echo "disabled";} ?>><?php echo htmlentities($description, ENT_QUOTES, 'UTF-8')?></textarea>
                    <label for="finddate">Find date</label>
                    <input class="w3-input w3-border" type="datetime-local" id="finddate" name="finddate" value="<?php echo $finddate?>" max="<?php echo date('Y-m-d')?>" required <?php if(isset($_POST["view"])){echo "disabled";} ?>>
                    <p>
                        <input hidden name="evidenceid" value="<?php if(isset($_POST["evidenceid"])){echo $_POST["evidenceid"];}?>">
                        <input class="w3-button w3-border" type="submit" name="<?php if(isset($_POST["view"])){echo "edit";}elseif(isset($_POST["edit"])){echo "update";}else{echo "add";} ?>" value="<?php if(isset($_POST["view"])){echo "Edit";}elseif(isset($_POST["edit"])){echo "Update";}else{echo "Add";} ?>">
                        <?php if(isset($_POST["edit"])){
                            ?>
                            <input class="w3-button w3-border" type="submit" name="delete" value="Delete">
                            <?php
                        }
                        ?>
                        <a href="evidence.php" class="w3-btn w3-border">Back</a>
                    </p>
                </div>
                <div class="w3-half">
                    <label for="image">Add image</label></p>
                    <img id="output" src=<?php if($imageData == ""){echo "emptyprofile.png";} else {echo "data:image/jpeg;base64,".$imageData;}?> height="300px"/>
                    <input class="w3-right" type="file" id="image" name="image" accept="image/*" onchange="loadFile(event)" <?php if(isset($_POST["view"])){echo "disabled";}?> <?php if(!isset($_POST["edit"])){echo "required";}?>>
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