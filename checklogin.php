<?php
session_start();

function checkLogin(){
    if(!isset($_SESSION["userid"])){
        header('location: http://localhost/hacklab/policeSite/login.php', true, 303);
    }
    else if(filter_input(INPUT_SERVER, 'REQUEST_URI')==="/hacklab/policeSite/index.php"){
        header('location: http://localhost/hacklab/policeSite/home.php', true, 303);
    }
}
?>