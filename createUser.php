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
    <title>New user</title>
</head>
<body>
<div class="w3-display-container" style="height:400px;">
    <div class="w3-display-middle w3-panel w3-border w3-round-large" >
        <h2>New user</h3>
        <form action="createUserValidation.php" method="POST">
            <p>
                <input class="w3-input w3-border w3-round-large" type="text" name="firstname" placeholder="First name" 
                required pattern="^[a-zA-Z]*$" maxlength="20" 
                oninvalid="this.setCustomValidity('Requires up to 20 letters and no spaces')" oninput="setCustomValidity('')">
            </p>
            <p>
                <input class="w3-input w3-border w3-round-large" type="text" name="lastname" placeholder="Last name" 
                required pattern="^[a-zA-Z]*$" maxlength="20" 
                oninvalid="this.setCustomValidity('Requires up to 20 letters and no spaces')" oninput="setCustomValidity('')">
            </p>
            <p>
                <input class="w3-input w3-border w3-round-large" type="email" name="email" placeholder="email" required pattern="^[a-zA-Z1-9]{3,}@(gmail\.com|hotmail\.com|live\.nl|outlook.com)$" oninvalid="this.setCustomValidity('not a valid email adress')" oninput="setCustomValidity('')"> 
            </p>
            <p>
                <input class="w3-input w3-border w3-round-large" type="password" id="password" name="password" placeholder="Password" 
                required pattern="^(?=[^a-z]*[a-z])(?=[^A-Z]*[A-Z])(?=\D*\d)(?=[^!@#$%^&*_\-,.<>\\\|\{\}\[\]:;?\/]*[!@#$%^&*_\-,.<>\\\|\{\}\[\]:;?\/])[A-Za-z0-9!@#$%^&*_\-,.<>\\\|\{\}\[\]:;?\/]{8,}$" 
                onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Requires passwords of at least 8 length, at least one capital letter, one number and one special character' : ''); if(this.checkValidity()) form.passwordconfirm.pattern = this.value;">
            </p>
            <p>
                <input class="w3-input w3-border w3-round-large" type="password" id="passwordconfirm" name="passwordconfirm" placeholder="Confirm password" 
                required pattern="^(?=[^a-z]*[a-z])(?=[^A-Z]*[A-Z])(?=\D*\d)(?=[^!@#$%^&*_\-,.<>\\\|\{\}\[\]:;?\/]*[!@#$%^&*_\-,.<>\\\|\{\}\[\]:;?\/])[A-Za-z0-9!@#$%^&*_\-,.<>\\\|\{\}\[\]:;?\/]{8,}$"
                onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Passwords must match' : '');">
            </p>
            <p>
                <select class="w3-select" name="rank">
                    <option value="sergeant">sergeant</option>
                    <option value="inspector">inspector</option>
                    <option value="chief inspector">chief inspector</option>
                    <option value="commissioner">commissioner</option>
                    <option value="chief commissioner">chief commissioner</option>
                    <option value="agent">agent</option>
                    <option value="chief agent">chief agent</option>
                    <option value="police patrol officer">police patrol officer</option>
                    <option value="police trainee">police trainee</option>
                    <option value="other">other</option>
                    <option value="announcer">announcer</option>
                </select>
            </p>
            <p>
                <input class="w3-btn w3-border w3-round-large w3-blue" type="submit" name="createuser" value="Create">
            </p>
        </form>
    </div>
</div>
</body>
</html>