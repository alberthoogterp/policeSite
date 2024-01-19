<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <title>Police login</title>
</head>
<body>
<div class="w3-display-container" style="height:400px;">
    <div class="w3-display-middle w3-panel w3-border w3-round-large" >
        <h2>login</h3>
        <form action="loginValidation.php" method="POST">
            <p>
                <input class="w3-input w3-border w3-round-large" type="text" name="email" placeholder="e-mail">
            </p>
            <p>
                <input class="w3-input w3-border w3-round-large" type="password" name="password" placeholder="Password">
            </p>
            <p>
                <input class="w3-btn w3-border w3-round-large w3-blue" type="submit" name="loginsubmit" value="login">
            </p>
        </form>
    </div>
</div>
</body>
</html>