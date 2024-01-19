<?php
require_once __DIR__."/databaseFunctions.php";

if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST'){
    if(isset($_POST["firstname"]) && isset($_POST["lastname"]) && isset($_POST["password"]) && isset($_POST["rank"]) && isset($_POST["email"])){
        $firstname = $_POST["firstname"];
        $lastname = $_POST["lastname"];
        $email = $_POST["email"];
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $rank = $_POST["rank"];
        Query([[
            "sql"=>"INSERT into officers (firstname, lastname, email, password, rank) values (?,?,?,?,?)",
            "values"=>[$firstname, $lastname, $email, $password, $rank]]
        ]);
        header('location: http://localhost/hacklab/policeSite/login.php', true, 303);
        exit();
    }

}
else{
    header('location: http://localhost/hacklab/policeSite/createUser.php', true, 303);
    exit();
}
?>