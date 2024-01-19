<?php
require_once __DIR__."/databaseFunctions.php";
session_start();

if(isset($_POST["email"]) && isset($_POST["password"])){
    $email = $_POST["email"];
    $password = $_POST["password"];
    $result = Query([[
        "sql"=>"SELECT id, email, password, rank FROM officers where email = (?)",
        "values"=>[$email]
    ]])[0]["result"]->fetch_assoc();
    if($result){
        $passhash = $result["password"];
        if(password_verify($password,$passhash)){
            $_SESSION["userid"] = $result["id"];
            $_SESSION["userrank"] = $result["rank"]; 
            header('location: http://localhost/hacklab/policeSite/home.php', true, 303);  
            exit();
        }
    }
    header('location: http://localhost/hacklab/policeSite/login.php', true, 303);  
    exit();
}
?>