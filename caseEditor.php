<?php

use function PHPSTORM_META\map;

require_once __DIR__."/checklogin.php";
require_once __DIR__."/navbar.php";
require_once __DIR__."/databaseFunctions.php";
checkLogin();

if($_SESSION["userrank"] == "announcer" || $_SESSION["userrank"] == "other" || $_SESSION["userrank"] == "police trainee" || $_SESSION["userrank"] == "police patrol officer"){
    header('location: http://localhost/hacklab/policeSite/cases.php', true, 303);  
    exit();
}

if(isset($_POST["caseid"])){
    $_SESSION["caseid"] = $_POST["caseid"];
}

$cases = Query([[
    "sql"=>"SELECT * FROM cases WHERE id = (?)",
    "values"=>[$_SESSION["caseid"]]
]])[0]["result"]->fetch_all(MYSQLI_ASSOC);
$caseName = getValue($cases, "name");
$description = getValue($cases, "description");
$date = getValue($cases, "opendate");
$imageData = "";

$evidence = Query([[
    "sql"=>"SELECT evidence.* FROM evidence JOIN cases_evidence ON cases_evidence.evidence_id = evidence.id WHERE cases_evidence.case_id != (?) ",
    "values"=>[$_SESSION["caseid"]]
]])[0]["result"]->fetch_all(MYSQLI_ASSOC);

$personsofinterest = Query([[
    "sql"=>"SELECT personsofinterest.* FROM personsofinterest JOIN cases_personsofinterest ON cases_personsofinterest.personofinterest_id = personsofinterest.id WHERE cases_personsofinterest.case_id != (?)",
    "values"=>[$_SESSION["caseid"]]
]])[0]["result"]->fetch_all(MYSQLI_ASSOC);

$officers = Query([[
    "sql"=>"SELECT officers.* FROM officers WHERE officers.id NOT IN (SELECT officer_id FROM officers_cases WHERE case_id = (?))",
    "values"=>[$_SESSION["caseid"]]
]])[0]["result"]->fetch_all(MYSQLI_ASSOC);

if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST'){
    if(isset($_POST["addevidencetocase"])){
        Query([[
            "sql"=>"INSERT INTO cases_evidence (case_id, evidence_id) VALUES (?,?)",
            "values"=>[$_SESSION["caseid"], $_POST["evidence"]]
        ]]);
        //echo("<meta http-equiv='refresh' content='1'>");
    }
    elseif(isset($_POST["addpoitocase"])){
        $prime = 0;
        if(isset($_POST["prime"])){
            $prime = 1;
        }
        Query([[
            "sql"=>"INSERT INTO cases_personsofinterest (case_id, personofinterest_id, type, prime) VALUES (?,?,?,?)",
            "values"=>[$_SESSION["caseid"], $_POST["poi"], $_POST["poitype"], $prime]
        ]]);
    }
    elseif(isset($_POST["addofficertocase"])){
        Query([[
            "sql"=>"INSERT INTO officers_cases (officer_id, case_id) VALUES (?,?)",
            "values"=>[$_POST["officerid"], $_SESSION["caseid"]]
        ]]);
    }
    elseif(isset($_POST["remove"])){
        if($_POST["table"] == "evidence"){
            Query([[
                "sql"=>"DELETE FROM cases_evidence WHERE evidence_id = (?) AND case_id = (?)",
                "values"=>[$_POST["id"], $_SESSION["caseid"]]
            ]]);
        }
        elseif($_POST["table"] == "personsofinterest"){
            Query([[
                "sql"=>"DELETE FROM cases_personsofinterest WHERE personofinterest_id = (?) AND case_id = (?)",
                "values"=>[$_POST["id"], $_SESSION["caseid"]]
            ]]);
        }
        elseif($_POST["table"] == "officers"){
            Query([[
                "sql"=>"DELETE FROM officers_cases WHERE officer_id = (?) AND case_id = (?)",
                "values"=>[$_POST["id"], $_SESSION["caseid"]]
            ]]);
        }
        
    }
    elseif(isset($_POST["changestate"])){
        Query([[
            "sql"=>"INSERT INTO casestate (case_id, state, date) values (?,?,?)",
            "values"=>[$_SESSION["caseid"], $_POST["state"], date('Y-m-d H:i:s')]
        ]]);
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
    <title>Case Editor</title>
</head>
<body>
    <?php
    addNavbar();
    ?>
    <div class="w3-display-container" style="height:400px;">
        <div class="w3-display-topleft w3-container">
            <p>
                <a class="w3-button w3-border" href="cases.php">Back</a> 
            </p>
            <label for="casename">Case name</label>
            <input class="w3-input w3-border" type="text" id="casename" name="casename" value="<?php echo $caseName?>" autocomplete="off" disabled>
            <label for="description">Description</label>
            <textarea class="w3-input w3-border" style="resize:none" id="description" name="description" cols="10" rows="6" value="<?php echo $description?>" autocomplete="off" disabled><?php echo htmlentities($description, ENT_QUOTES, 'UTF-8')?></textarea>
            <label for="date">Case opening-date</label>
            <input class="w3-input w3-border" type="datetime-local" id="date" name="date" value="<?php echo $date?>" max="<?php echo date('Y-m-d')?>" disabled> 
        </div>
        <div class="w3-display-topmiddle w3-container">
            <p>
                <?php
                if(!isset($_POST["editcase"])){
                    ?>
                    <form class="w3-container" action="" method="POST">
                        <input class="w3-button w3-border" type="submit" name="editcase" value="edit case-state">
                    </form>
                    <?php
                }
                elseif(isset($_POST["editcase"])){
                    $caseStates = Query([[
                        "sql"=>"SELECT * FROM casestate WHERE case_id = (?) ORDER BY date DESC",
                        "values"=>[$_SESSION["caseid"]]
                    ]])[0]["result"]->fetch_all(MYSQLI_ASSOC);
                    $states = ["ongoing", "closed", "coldcase"];
                    ?>
                    <form class="w3-container" action="" method="POST">
                        <select required class="w3-select w3-border" name="state">
                            <option selected disabled value="">Select state:</option>
                            <?php
                            foreach($states as $state){
                                if($caseStates[0]["state"] != $state){
                                    ?>
                                    <option value="<?php echo $state?>"><?php echo $state?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        <input class="w3-button w3-border" type="submit" name="changestate" value="Change state">
                    </form>
                    <?php
                }
                ?>
            </p>
        </div>
    </div>
    <div class="w3-display-container" style="height:800px;">
        <div class="w3-display-topleft w3-container">
            <?php
                if(!isset($_POST["addevidence"])){
                    ?>
                    <form class="w3-container" action="" method="POST">
                        <input class="w3-button w3-border" type="submit" name="addevidence" value="Add Evidence">
                    </form>
                    <?php
                }
                elseif(isset($_POST["addevidence"])){
                    ?>
                    <form class="w3-container" action="" method="POST">
                        <select required class="w3-select w3-border" id="evidence" name="evidence">
                            <option selected disabled value="">Select evidence:</option>
                            <?php
                            foreach($evidence as $row){
                                ?>
                                <option value="<?php echo $row["id"]?>"><?php echo $row["id"].": ".$row["description"]?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <input class="w3-button w3-border" type="submit" name="addevidencetocase" value="Add to case">
                    </form>
                    <?php
                }
                $caseEvidence = Query([[
                    "sql"=>"SELECT evidence.*, pictures.filename, pictures.filetype, pictures.filedata
                            FROM evidence 
                            JOIN cases_evidence ON cases_evidence.evidence_id = evidence.id
                            JOIN evidence_pictures ON evidence_pictures.evidence_id = evidence.id
                            JOIN pictures ON pictures.id = evidence_pictures.picture_id
                            WHERE cases_evidence.case_id = (?)",
                    "values"=>[$_SESSION["caseid"]]
                ]])[0]["result"]->fetch_all(MYSQLI_ASSOC);
                foreach($caseEvidence as $row){
                $imageData = base64_encode($row["filedata"]);
                ?>
                <p>
                    <div class="w3-card">
                        <div class="w3-container w3-centre" style="width: fit-content;">
                            <label for="description">Evidence id: <?php echo $row["id"]?></label>
                            <br>
                            <img id="output" src=<?php if($imageData == ""){echo "emptyprofile.png";} else {echo "data:image/jpeg;base64,".$imageData;}?> height="300px"/>
                            <textarea class="w3-input w3-border" style="resize:none" id="description" name="description" cols="8" rows="6" value="<?php echo $row["description"]?>" autocomplete="off" disabled><?php echo htmlentities($row["description"], ENT_QUOTES, 'UTF-8')?></textarea>
                            <form action="" method="POST">
                                <p>
                                    <input hidden name="table" value="<?php echo "evidence"?>">
                                    <input hidden name="id" value="<?php echo $row["id"]?>">
                                    <input class="w3-button w3-border" type="submit" name="remove" value="Remove"> 
                                </p>
                            </form>
                        </div>
                    </div>
                </p>
                <?php
                }
            ?>
        </div>
        <div class="w3-display-topmiddle w3-container">
            <?php
                if(!isset($_POST["addpoi"])){
                    ?>
                    <form class="w3-container" action="" method="POST">
                        <input class="w3-button w3-border" type="submit" name="addpoi" value="Add person of interest">
                    </form>
                    <?php
                }
                elseif(isset($_POST["addpoi"])){
                    ?>
                    <form class="w3-container" action="" method="POST">
                        <select required class="w3-select w3-border" id="poi" name="poi">
                            <option selected disabled value="">Select poi:</option>
                            <?php
                            foreach($personsofinterest as $row){
                                ?>
                                <option value="<?php echo $row["id"]?>"><?php echo $row["id"].": ".$row["firstname"]." ".$row["lastname"]?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <label for="prime">is Prime:</label>
                        <input type="checkbox" name="prime" value="Prime-subject">
                        <select required class="w3-select w3-border" name="poitype">
                            <option value="witness">Witness</option>
                            <option value="suspect">Suspect</option>
                            <option value="victim">Victim</option>
                        </select>
                        <input class="w3-button w3-border" type="submit" name="addpoitocase" value="Add to case">
                    </form>
                    <?php
                }
                $casePoi = Query([[
                    "sql"=>"SELECT personsofinterest.*, cases_personsofinterest.type, cases_personsofinterest.prime, pictures.filename, pictures.filetype, pictures.filedata FROM `personsofinterest`
                            JOIN cases_personsofinterest ON cases_personsofinterest.personofinterest_id = personsofinterest.id
                            JOIN personsofinterest_pictures ON personsofinterest_pictures.personofinterest_id = personsofinterest.id
                            JOIN pictures ON pictures.id = personsofinterest_pictures.picture_id
                            WHERE cases_personsofinterest.case_id = (?)
                            ORDER BY cases_personsofinterest.prime DESC",
                    "values"=>[$_SESSION["caseid"]]
                ]])[0]["result"]->fetch_all(MYSQLI_ASSOC);
                foreach($casePoi as $row){
                    $imageData = base64_encode($row["filedata"]);
                    ?>
                    <p>
                    <div class="w3-card">
                        <div class="w3-container w3-centre" style="width: fit-content;">
                            <label for="description">person of interest id: <?php echo $row["id"]?></label>
                            <br>
                            <img id="output" src=<?php if($imageData == ""){echo "emptyprofile.png";} else {echo "data:image/jpeg;base64,".$imageData;}?> height="300px"/>
                            <p><label>Info:</label></p>
                            <p><label>First name: <?php echo $row["firstname"]?></label></p>
                            <p><label>Last name: <?php echo $row["lastname"]?></label></p>
                            <p><label>adress: <?php echo $row["adress"]?></label></p>
                            <p><label>height: <?php echo $row["height"]?></label></p>
                            <p><label>haircolour: <?php echo $row["haircolour"]?></label></p>
                            <p><label>eyecolour: <?php echo $row["eyecolour"]?></label></p>
                            <p><label>dexterity: <?php echo $row["dexterity"]?></label></p>
                            <p><label>date of birth: <?php echo $row["dateofbirth"]?></label></p>
                            <p><label>gender: <?php echo $row["gender"]?></label></p>
                            <p><label>type: <?php if($row["prime"] == 1){echo "prime ";}echo $row["type"]?></label></p>
                            
                            <form action="" method="POST">
                                <p>
                                    <input hidden name="table" value="<?php echo "personofinterest"?>">
                                    <input hidden name="id" value="<?php echo $row["id"]?>">
                                    <input class="w3-button w3-border" type="submit" name="remove" value="Remove"> 
                                </p>
                            </form>
                        </div>
                    </div>
                    </p>
                    <?php
                }
            ?>
        </div>
        <div class="w3-display-topright w3-container">
            <?php
            if(!isset($_POST["addofficer"])){
                ?>
                <form class="w3-container" action="" method="POST">
                    <input class="w3-button w3-border" type="submit" name="addofficer" value="Add officer">
                </form>
                <?php
            }
            elseif(isset($_POST["addofficer"])){
            ?>
                <form action="" method="POST">
                    <select class="w3-select w3-border" required name="officerid">
                        <?php
                        foreach($officers as $row){
                            if($row["rank"] != "announcer" && $row["rank"] != "other" && $row["rank"] != "police trainee" && $row["rank"] != "police patrol officer"){
                                ?>
                                <option value="<?php echo $row["id"]?>"><?php echo $row["id"]." ".$row["firstname"]." ".$row["lastname"]?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                    <input class="w3-button w3-border" type="submit" name="addofficertocase" value="Add">
                </form>
            <?php
            }
            $caseOfficers = Query([[
                "sql"=>"SELECT officers.id, officers.firstname, officers.lastname FROM officers JOIN officers_cases ON officers_cases.officer_id = officers.id WHERE officers_cases.case_id = (?)",
                "values"=>[$_SESSION["caseid"]]
            ]])[0]["result"]->fetch_all(MYSQLI_ASSOC);
            foreach($caseOfficers as $row){
                ?>
                <p>
                <div class="w3-card">
                    <div class="w3-container w3-centre" style="width: fit-content;">
                        <label for="description">Officer id: <?php echo $row["id"]?></label>
                        <br>
                        <p><label>Info:</label></p>
                        <p><label>First name: <?php echo $row["firstname"]?></label></p>
                        <p><label>Last name: <?php echo $row["lastname"]?></label></p>
                        
                        <form action="" method="POST">
                            <p>
                                <input hidden name="table" value="<?php echo "officers"?>">
                                <input hidden name="id" value="<?php echo $row["id"]?>">
                                <input class="w3-button w3-border" type="submit" name="remove" value="Remove"> 
                            </p>
                        </form>
                    </div>
                </div>
                </p>
                <?php
            }
            ?>
        </div>
    </div>
</body>
</html>